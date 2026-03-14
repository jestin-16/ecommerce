<?php
// api/cart_remove.php
require_once 'db.php';
session_start();

$cartKey = isset($_POST['cart_key']) ? $_POST['cart_key'] : '';

if (empty($cartKey)) {
    jsonResponse(false, [], 'Invalid cart key.');
}

if (isset($_SESSION['cart'][$cartKey])) {
    unset($_SESSION['cart'][$cartKey]);
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
