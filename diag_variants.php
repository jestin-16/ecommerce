<?php
// diag_variants.php
require_once 'api/db.php';
$res = $pdo->query("DESCRIBE product_variants");
$cols = $res->fetchAll(PDO::FETCH_ASSOC);
file_put_contents('pv_cols.json', json_encode($cols));
