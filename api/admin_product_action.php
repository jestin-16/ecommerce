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
        $variants = $_POST['variants'] ?? []; // Array of variants

        if (empty($name) || empty($category) || $price <= 0) {
            throw new Exception('Name, Category, and valid Price are required.');
        }

        $pdo->beginTransaction();

        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category, stock) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $image_url, $category, $stock]);
                $id = $pdo->lastInsertId();
            } else {
                if (!$id) throw new Exception('Product ID required for editing.');
                $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image_url=?, category=?, stock=? WHERE id=?");
                $stmt->execute([$name, $description, $price, $image_url, $category, $stock, $id]);
            }

            // --- Synchronization Logic for Variants ---
            
            // 1. Get existing variant IDs for this product
            $existingVariantIds = [];
            if ($action === 'edit') {
                $stmt = $pdo->prepare("SELECT id FROM product_variants WHERE product_id = ?");
                $stmt->execute([$id]);
                $existingVariantIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            $processedVariantIds = [];

            if (!empty($variants)) {
                $insertVariantStmt = $pdo->prepare("INSERT INTO product_variants (product_id, color_name, color_hex, image_url) VALUES (?, ?, ?, ?)");
                $updateVariantStmt = $pdo->prepare("UPDATE product_variants SET color_name=?, color_hex=?, image_url=? WHERE id=?");
                $insertStockStmt = $pdo->prepare("INSERT INTO variant_stocks (variant_id, size, stock) VALUES (?, ?, ?)");
                $deleteStockStmt = $pdo->prepare("DELETE FROM variant_stocks WHERE variant_id = ?");

                foreach ($variants as $v) {
                    if (empty($v['color_name']) || empty($v['color_hex'])) continue;
                    
                    $vId = $v['id'] ?? null;
                    
                    if ($action === 'edit' && !empty($vId) && in_array($vId, $existingVariantIds)) {
                        // Update existing variant
                        $updateVariantStmt->execute([
                            $v['color_name'], 
                            $v['color_hex'], 
                            $v['image_url'] ?? $image_url,
                            $vId
                        ]);
                        $variantId = $vId;
                        $processedVariantIds[] = $variantId;
                    } else {
                        // Insert new variant
                        $insertVariantStmt->execute([
                            $id, 
                            $v['color_name'], 
                            $v['color_hex'], 
                            $v['image_url'] ?? $image_url
                        ]);
                        $variantId = $pdo->lastInsertId();
                        if ($action === 'edit') {
                            $processedVariantIds[] = $variantId;
                        }
                    }

                    // Sync size stocks for this variant: delete all and re-insert (simplest for sizes)
                    $deleteStockStmt->execute([$variantId]);
                    if (!empty($v['sizes'])) {
                        foreach ($v['sizes'] as $size => $stockCount) {
                            $insertStockStmt->execute([$variantId, $size, (int)$stockCount]);
                        }
                    }
                }
            }

            // 2. Delete variants that were removed in the UI
            if ($action === 'edit') {
                $idsToDelete = array_diff($existingVariantIds, $processedVariantIds);
                if (!empty($idsToDelete)) {
                    $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
                    $pdo->prepare("DELETE FROM product_variants WHERE id IN ($placeholders)")->execute(array_values($idsToDelete));
                    // Stocks for these will be deleted automatically if ON DELETE CASCADE is set, 
                    // but let's be safe if not (though our schema check hinted at constraints).
                    // Actually, let's explicitly delete them if no cascade.
                    $pdo->prepare("DELETE FROM variant_stocks WHERE variant_id IN ($placeholders)")->execute(array_values($idsToDelete));
                }
            }

            $pdo->commit();
            jsonResponse(true, [], ($action === 'add' ? 'Product added' : 'Product updated') . ' successfully.');
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    } else {
        throw new Exception('Invalid action.');
    }
} catch (Exception $e) {
    jsonResponse(false, [], 'Action failed: ' . $e->getMessage());
}
