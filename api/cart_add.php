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

    $color = isset($_POST['color']) ? $_POST['color'] : '';
    $size = isset($_POST['size']) ? $_POST['size'] : '';
    $variantId = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : null;

    // Create a unique key for the cart item based on product and variant/size/color
    $cartKey = $productId . '_' . $variantId . '_' . $size . '_' . $color;

    if (isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['qty'] += $quantity;
    } else {
        $_SESSION['cart'][$cartKey] = [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'name' => $product['name'],
            'price' => $product['price'],
            'qty' => $quantity,
            'color' => $color,
            'size' => $size
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
