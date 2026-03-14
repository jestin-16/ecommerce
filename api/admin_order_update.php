<?php
// api/admin_order_update.php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    jsonResponse(false, [], 'Unauthorized access.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, [], 'Invalid request method.');
}

$order_id = $_POST['order_id'] ?? 0;
$status = $_POST['status'] ?? '';

$validStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

if (!$order_id || !in_array($status, $validStatuses)) {
    jsonResponse(false, [], 'Invalid order ID or status.');
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    
    jsonResponse(true, [], 'Order status updated successfully.');
} catch (Exception $e) {
    jsonResponse(false, [], 'Failed to update order: ' . $e->getMessage());
}
?>
