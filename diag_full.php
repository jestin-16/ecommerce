<?php
// diag_full.php
require_once 'api/db.php';

echo "=== TABLES ===\n";
$tables = ['products', 'product_variants', 'variant_stocks'];
foreach ($tables as $t) {
    $res = $pdo->query("SHOW CREATE TABLE $t")->fetch();
    echo "--- $t ---\n" . $res['Create Table'] . "\n\n";
}

echo "=== PRODUCT VARIANTS DATA ===\n";
$stmt = $pdo->query("SELECT * FROM product_variants ORDER BY id DESC LIMIT 20");
echo json_encode($stmt->fetchAll(), JSON_PRETTY_PRINT) . "\n\n";

echo "=== VARIANT STOCKS DATA ===\n";
$stmt = $pdo->query("SELECT * FROM variant_stocks ORDER BY variant_id DESC LIMIT 20");
echo json_encode($stmt->fetchAll(), JSON_PRETTY_PRINT) . "\n\n";
