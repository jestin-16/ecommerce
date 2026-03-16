<?php
// api/place_order.php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, [], 'Invalid request method.');
}

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, [], 'You must be logged in to place an order.');
}

$user_id = $_SESSION['user_id'];
$shipping_address = $_POST['address'] ?? '';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    jsonResponse(false, [], 'Your cart is empty.');
}

if (empty($shipping_address)) {
    jsonResponse(false, [], 'Please provide a shipping address.');
}

// Calculate total
$subtotal = 0;
foreach ($cart as $productId => $item) {
    if ($item['qty'] <= 0) continue;
    $subtotal += ($item['price'] * $item['qty']);
}

if ($subtotal <= 0) {
    jsonResponse(false, [], 'Invalid cart total.');
}

try {
    $pdo->beginTransaction();

    // 1. Insert Order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->execute([$user_id, $subtotal, $shipping_address]);
    $order_id = $pdo->lastInsertId();

    // 2. Insert Order Items and update stock
    foreach ($cart as $cartKey => $item) {
        $productId = $item['product_id'];
        $variantId = $item['variant_id'] ?? null;
        $size = $item['size'] ?? '';
        $qty = $item['qty'];
        $price = $item['price'];

        if ($qty <= 0) continue;

        // Verify stock
        if ($variantId) {
            // For variants we keep stock in variant_stocks (per size).
            // If size is missing, fallback to total available across sizes.
            if ($size !== '') {
                $stmt_stock = $pdo->prepare("SELECT stock FROM variant_stocks WHERE variant_id = ? AND size = ? FOR UPDATE");
                $stmt_stock->execute([$variantId, $size]);
            } else {
                $stmt_stock = $pdo->prepare("SELECT SUM(stock) AS stock FROM variant_stocks WHERE variant_id = ? FOR UPDATE");
                $stmt_stock->execute([$variantId]);
            }
        } else {
            $stmt_stock = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
            $stmt_stock->execute([$productId]);
        }
        
        $stockRow = $stmt_stock->fetch();

        $availableStock = 0;
        if ($stockRow) {
            if (array_key_exists('stock', $stockRow)) {
                $availableStock = (int)($stockRow['stock'] ?? 0);
            } elseif (isset($stockRow[0])) {
                $availableStock = (int)($stockRow[0] ?? 0);
            }
        }

        if ($availableStock < $qty) {
            throw new Exception("Item {$item['name']} is out of stock or insufficient quantity.");
        }

        // Insert order item
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        $stmt_item->execute([$order_id, $productId, $qty, $price]);

        // Deduct stock
        if ($variantId) {
            if ($size !== '') {
                $stmt_update = $pdo->prepare("UPDATE variant_stocks SET stock = stock - ? WHERE variant_id = ? AND size = ?");
                $stmt_update->execute([$qty, $variantId, $size]);
            } else {
                // Deduct across sizes with available stock (deterministic order).
                $remaining = (int)$qty;
                $stmt_sizes = $pdo->prepare("SELECT id, stock FROM variant_stocks WHERE variant_id = ? AND stock > 0 ORDER BY FIELD(size,'XS','S','M','L','XL','XXL'), id FOR UPDATE");
                $stmt_sizes->execute([$variantId]);
                $rows = $stmt_sizes->fetchAll();

                foreach ($rows as $r) {
                    if ($remaining <= 0) break;
                    $take = min($remaining, (int)$r['stock']);
                    if ($take <= 0) continue;
                    $pdo->prepare("UPDATE variant_stocks SET stock = stock - ? WHERE id = ?")->execute([$take, $r['id']]);
                    $remaining -= $take;
                }

                if ($remaining > 0) {
                    throw new Exception("Item {$item['name']} is out of stock or insufficient quantity.");
                }
            }
        } else {
            $stmt_update = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt_update->execute([$qty, $productId]);
        }
    }

    $pdo->commit();

    // Clear cart
    unset($_SESSION['cart']);

    jsonResponse(true, ['order_id' => $order_id], 'Order placed successfully!');

} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(false, [], 'Failed to place order: ' . $e->getMessage());
}
?>
