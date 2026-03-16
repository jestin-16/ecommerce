<?php
// diag_schema.php
require_once 'api/db.php';

$tables = ['products', 'product_variants', 'variant_stocks'];
$schema = [];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        $schema[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $schema[$table] = "Error: " . $e->getMessage();
    }
}

echo json_encode($schema, JSON_PRETTY_PRINT);
