<?php
require_once 'api/db.php';

try {
    // Create product_variants table
    $pdo->exec("CREATE TABLE IF NOT EXISTS product_variants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        color_name VARCHAR(50),
        color_hex VARCHAR(10),
        image_url VARCHAR(255),
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // Create variant_stocks table
    $pdo->exec("CREATE TABLE IF NOT EXISTS variant_stocks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        variant_id INT NOT NULL,
        size VARCHAR(10),
        stock INT DEFAULT 0,
        FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
    )");
    
    // Check if description column exists before adding
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'description'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE products ADD COLUMN description TEXT AFTER category");
        echo "Added 'description' column to products.\n";
    }

    echo "Migration Successful";
} catch (PDOException $e) {
    echo "Migration Failed: " . $e->getMessage();
}
