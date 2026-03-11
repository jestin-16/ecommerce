<?php
// api/get_products.php
require_once 'db.php';

$category = isset($_GET['category']) ? trim($_GET['category']) : '';

try {
    if ($category && $category !== 'All') {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = :category ORDER BY id DESC");
        $stmt->execute(['category' => $category]);
    } else {
        $stmt = $query = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    }

    $products = $stmt->fetchAll();
    
    // Ensure image_url has a valid path
    foreach ($products as &$product) {
        if (empty($product['image_url'])) {
            $product['image_url'] = 'images/image.png';
        } else {
            // Ensure the path doesn't have double 'images/' prefix
            $product['image_url'] = str_replace('images/images/', 'images/', $product['image_url']);
        }
    }

    jsonResponse(true, $products, 'Products retrieved successfully.');
} catch (PDOException $e) {
    jsonResponse(false, [], 'Failed to retrieve products: ' . $e->getMessage());
}
