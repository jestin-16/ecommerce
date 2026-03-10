<?php
// api/cart_update.php
require_once 'db.php';
session_start();

$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

if ($productId <= 0) {
    jsonResponse(false, [], 'Invalid product ID.');
}

if (!isset($_SESSION['cart'][$productId])) {
    jsonResponse(false, [], 'Product not in cart.');
}

try {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        // Optional: Check stock constraint before updating
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute(['id' => $productId]);
        $stock = $stmt->fetchColumn();

        if ($stock !== false && $stock < $quantity) {
            jsonResponse(false, [], 'Not enough stock available. Max: ' . $stock);
        }

        $_SESSION['cart'][$productId]['qty'] = $quantity;
    }

    // Return updated totals
    $totalItems = 0;
    $subtotal = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $totalItems += $item['qty'];
            $subtotal += ($item['price'] * $item['qty']);
        }
    }

    jsonResponse(true, ['total_items' => $totalItems, 'subtotal' => $subtotal], 'Cart updated.');
} catch (Exception $e) {
    jsonResponse(false, [], 'Failed to update cart: ' . $e->getMessage());
}
