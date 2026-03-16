<?php
// diag_fk.php
require_once 'api/db.php';

$tables = ['products', 'product_variants', 'variant_stocks'];

foreach ($tables as $table) {
    echo "Table: $table\n";
    $res = $pdo->query("SHOW CREATE TABLE $table")->fetch(PDO::FETCH_ASSOC);
    echo $res['Create Table'] . "\n\n";
}
