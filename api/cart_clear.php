<?php
// api/cart_clear.php
require_once 'db.php';
session_start();

if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

jsonResponse(true, ['total_items' => 0, 'subtotal' => 0], 'Cart cleared successfully.');
