<?php
// api/cart_remove.php
require_once 'db.php';
session_start();

$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

if ($productId <= 0) {
    jsonResponse(false, [], 'Invalid product ID.');
}

if (isset($_SESSION['cart'][$productId])) {
    unset($_SESSION['cart'][$productId]);
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

jsonResponse(true, ['total_items' => $totalItems, 'subtotal' => $subtotal], 'Product removed from cart.');
