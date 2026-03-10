<?php
// api/search_products.php
require_once 'db.php';

$term = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    if ($term) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :term OR description LIKE :term ORDER BY id DESC");
        $likeTerm = '%' . $term . '%';
        $stmt->execute(['term' => $likeTerm]);
        $products = $stmt->fetchAll();
    } else {
        $products = []; // Or return all if preferred, but usually search returns empty if no term
    }

    jsonResponse(true, $products, 'Search completed.');
} catch (PDOException $e) {
    jsonResponse(false, [], 'Failed to search products: ' . $e->getMessage());
}
