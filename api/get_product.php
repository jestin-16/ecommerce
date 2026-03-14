<?php
// api/get_product.php
require_once 'db.php';

if (!isset($_GET['id'])) {
    jsonResponse(false, [], 'Product ID is required.');
}

$productId = (int)$_GET['id'];

try {
    // Fetch product
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        jsonResponse(false, [], 'Product not found.');
    }

    // Fetch variants
    $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ?");
    $stmt->execute([$productId]);
    $variants = $stmt->fetchAll();

    jsonResponse(true, [
        'product' => $product,
        'variants' => $variants
    ]);

} catch (PDOException $e) {
    jsonResponse(false, [], 'Error fetching product: ' . $e->getMessage());
}
