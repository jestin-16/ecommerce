<?php
require_once 'api/db.php';
$out = "";
function dump_to_var($q) {
    global $pdo, $out;
    try {
        $stmt = $pdo->query($q);
        $out .= "Q: $q\n";
        $out .= print_r($stmt->fetchAll(PDO::FETCH_ASSOC), true) . "\n";
    } catch (Exception $e) { $out .= "ERR: " . $e->getMessage() . "\n"; }
}

$out .= "=== SCHEMAS ===\n";
dump_to_var("SHOW CREATE TABLE product_variants");
dump_to_var("SHOW CREATE TABLE variant_stocks");

$out .= "=== DATA SAMPLE ===\n";
dump_to_var("SELECT * FROM product_variants ORDER BY id DESC LIMIT 10");
dump_to_var("SELECT * FROM variant_stocks ORDER BY variant_id DESC LIMIT 20");

file_put_contents('diag_output.txt', $out);
echo "Done writing to diag_output.txt\n";
