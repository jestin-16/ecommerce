<?php
require_once 'api/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'category'");
$row = $stmt->fetch();
echo $row['Type'];
