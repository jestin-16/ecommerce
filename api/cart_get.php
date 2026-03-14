<?php
// api/cart_get.php
require_once 'db.php';
session_start();

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$totalItems = 0;
$subtotal = 0;
$cartData = [];

foreach ($cart as $productId => $item) {
    $totalItems += $item['qty'];
    $lineTotal = $item['price'] * $item['qty'];
    $subtotal += $lineTotal;

    $cartData[] = [
        'product_id' => $item['product_id'],
        'variant_id' => $item['variant_id'],
        'name' => $item['name'],
        'price' => $item['price'],
        'qty' => $item['qty'],
        'color' => $item['color'],
        'size' => $item['size'],
        'line_total' => $lineTotal,
        'cart_key' => $productId // In this context $productId is actually the $cartKey from foreach
    ];
}

$response = [
    'items' => $cartData,
    'total_items' => $totalItems,
    'subtotal' => $subtotal
];

jsonResponse(true, $response, 'Cart retrieved successfully.');
