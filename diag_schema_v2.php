<?php
// diag_schema_v2.php
require_once 'api/db.php';

$tables = ['products', 'product_variants', 'variant_stocks'];

foreach ($tables as $table) {
    echo "\nTABLE: $table\n";
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo " - " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } catch (Exception $e) {
        echo " Error: " . $e->getMessage() . "\n";
    }
}
