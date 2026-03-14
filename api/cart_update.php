<?php
// api/cart_update.php
require_once 'db.php';
session_start();

$cartKey = isset($_POST['cart_key']) ? $_POST['cart_key'] : '';
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

if (empty($cartKey)) {
    jsonResponse(false, [], 'Invalid cart key.');
}

if (!isset($_SESSION['cart'][$cartKey])) {
    jsonResponse(false, [], 'Item not in cart.');
}

$productId = $_SESSION['cart'][$cartKey]['product_id'];
$variantId = $_SESSION['cart'][$cartKey]['variant_id'];

try {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$cartKey]);
    } else {
        // Optional: Check stock constraint before updating
        // If variantId is present, check variant stock, else check product stock
        if ($variantId) {
             $stmt = $pdo->prepare("SELECT stock FROM product_variants WHERE id = :id");
             $stmt->execute(['id' => $variantId]);
        } else {
             $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
             $stmt->execute(['id' => $productId]);
        }
        $stock = $stmt->fetchColumn();

        if ($stock !== false && $stock < $quantity) {
            jsonResponse(false, [], 'Not enough stock available. Max: ' . $stock);
        }

        $_SESSION['cart'][$cartKey]['qty'] = $quantity;
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
