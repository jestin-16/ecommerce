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
        'product_id' => $productId,
        'name' => $item['name'],
        'price' => $item['price'],
        'qty' => $item['qty'],
        'line_total' => $lineTotal
    ];
}

$response = [
    'items' => $cartData,
    'total_items' => $totalItems,
    'subtotal' => $subtotal
];

jsonResponse(true, $response, 'Cart retrieved successfully.');
