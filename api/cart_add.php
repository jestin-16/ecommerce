<?php
// api/cart_add.php
require_once 'db.php';
session_start();

$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

if ($productId <= 0 || $quantity <= 0) {
    jsonResponse(false, [], 'Invalid product ID or quantity. Product ID: ' . $productId . ' Qty: ' . $quantity);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        jsonResponse(false, [], 'Product not found.');
    }

    if ($product['stock'] < $quantity) {
        jsonResponse(false, [], 'Not enough stock available.');
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['qty'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'qty' => $quantity
        ];
    }

    // Calculate total items
    $totalItems = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalItems += $item['qty'];
    }

    jsonResponse(true, ['total_items' => $totalItems], 'Product added to cart successfully.');
} catch (PDOException $e) {
    jsonResponse(false, [], 'Failed to add to cart: ' . $e->getMessage());
}
