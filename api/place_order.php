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
    foreach ($cart as $productId => $item) {
        $qty = $item['qty'];
        $price = $item['price'];

        if ($qty <= 0) continue;

        // Verify stock
        $stmt_stock = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
        $stmt_stock->execute([$productId]);
        $productRow = $stmt_stock->fetch();

        if (!$productRow || $productRow['stock'] < $qty) {
            throw new Exception("Product {$item['name']} is out of stock or insufficient quantity.");
        }

        // Insert order item
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        $stmt_item->execute([$order_id, $productId, $qty, $price]);

        // Deduct stock
        $stmt_update = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt_update->execute([$qty, $productId]);
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
