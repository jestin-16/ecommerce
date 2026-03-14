<?php
// api/admin_product_action.php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    jsonResponse(false, [], 'Unauthorized access.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, [], 'Invalid request method.');
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if (!$id) throw new Exception('Product ID required.');

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse(true, [], 'Product deleted successfully.');
    } 
    elseif ($action === 'add' || $action === 'edit') {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category = $_POST['category'] ?? '';
        $stock = $_POST['stock'] ?? 0;
        $image_url = $_POST['image_url'] ?? 'images/default.png';

        if (empty($name) || empty($category) || $price <= 0) {
            throw new Exception('Name, Category, and valid Price are required.');
        }

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category, stock) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $image_url, $category, $stock]);
            jsonResponse(true, [], 'Product added successfully.');
        } else {
            if (!$id) throw new Exception('Product ID required for editing.');
            $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image_url=?, category=?, stock=? WHERE id=?");
            $stmt->execute([$name, $description, $price, $image_url, $category, $stock, $id]);
            jsonResponse(true, [], 'Product updated successfully.');
        }
    } else {
        throw new Exception('Invalid action.');
    }
} catch (Exception $e) {
    jsonResponse(false, [], 'Action failed: ' . $e->getMessage());
}
