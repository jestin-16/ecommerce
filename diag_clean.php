<?php
require_once 'api/db.php';
function dump($q) {
    global $pdo;
    try {
        $stmt = $pdo->query($q);
        echo "Q: $q\n";
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (Exception $e) { echo "ERR: " . $e->getMessage() . "\n"; }
}

echo "=== SCHEMAS ===\n";
dump("SHOW CREATE TABLE product_variants");
dump("SHOW CREATE TABLE variant_stocks");

echo "=== DATA SAMPLE ===\n";
dump("SELECT * FROM product_variants ORDER BY id DESC LIMIT 5");
dump("SELECT * FROM variant_stocks ORDER BY variant_id DESC LIMIT 5");
