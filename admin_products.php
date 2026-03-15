<?php
session_start();
require_once 'api/db.php';

// Must be logged in and admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body class="luxury-light-theme bg-white d-flex flex-column min-vh-100">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top p-3 boreal-navbar bg-white border-bottom border-light">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand" href="index.php">
                <i class="bi bi-asterisk me-2"></i>BOREAL <span class="fs-6 text-accent fw-normal text-capitalize ms-2">Admin</span>
            </a>
            <div class="d-flex align-items-center gap-4 text-dark ms-auto">
                 <a class="nav-link text-dark text-uppercase fs-7 tracking-wider d-none d-md-block" href="admin_dashboard.php">Dashboard</a>
                 <a class="nav-link text-dark text-uppercase fs-7 tracking-wider d-none d-md-block" href="admin_orders.php">Orders</a>
                 <a class="nav-link text-dark text-uppercase fs-7 tracking-wider" href="index.php">Return to Shop</a>
            </div>
        </div>
    </nav>

    <section class="flex-grow-1 py-5">
        <div class="container-fluid px-lg-5">
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom border-dark-subtle pb-3">
                <div>
                     <h2 class="text-dark font-playfair mb-1">Manage Products</h2>
                     <p class="text-secondary-light tracking-wider fs-8 text-uppercase mb-0">Total: <?php echo count($products); ?> items</p>
                </div>
                <a href="admin_product_add.php" class="btn btn-dark rounded-0 text-uppercase fs-8 tracking-wider">
                    <i class="bi bi-plus me-1"></i> Add New Product
                </a>
            </div>

            <div id="action-alert" class="alert d-none rounded-0 border-0 fs-7"></div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border-light">
                    <thead>
                        <tr class="text-secondary text-uppercase tracking-wider fs-8 bg-light">
                            <th scope="col" class="fw-normal py-3 px-3">ID</th>
                            <th scope="col" class="fw-normal py-3">Product</th>
                            <th scope="col" class="fw-normal py-3">Category</th>
                            <th scope="col" class="fw-normal py-3">Description</th>
                            <th scope="col" class="fw-normal py-3">Price</th>
                            <th scope="col" class="fw-normal py-3">Stock / Variants</th>
                            <th scope="col" class="fw-normal py-3 text-end px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): 
                            // Fetch variant count
                            $vStmt = $pdo->prepare("SELECT COUNT(*) as v_count FROM product_variants WHERE product_id = ?");
                            $vStmt->execute([$p['id']]);
                            $vCount = $vStmt->fetch()['v_count'];
                        ?>
                        <tr id="product-row-<?php echo $p['id']; ?>">
                            <td class="font-mono text-dark fs-7 px-3">#<?php echo $p['id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="" style="width: 40px; height: 50px; object-fit: cover; border: 1px solid rgba(0,0,0,0.05);">
                                    <span class="text-dark font-playfair fs-6 fw-bold"><?php echo htmlspecialchars($p['name']); ?></span>
                                </div>
                            </td>
                            <td class="text-secondary fs-8 text-uppercase tracking-wider"><?php echo htmlspecialchars($p['category']); ?></td>
                            <td class="text-secondary fs-8" style="max-width: 250px;">
                                <div class="text-truncate" title="<?php echo htmlspecialchars($p['description']); ?>">
                                    <?php echo htmlspecialchars($p['description'] ?: 'No description'); ?>
                                </div>
                            </td>
                            <td class="font-mono text-dark fs-7 fw-bold">$<?php echo number_format($p['price'], 2); ?></td>
                            <td>
                                <div class="font-mono text-dark fs-7">
                                    <?php 
                                    // Calculate total stock from variants if they exist
                                    $stmt = $pdo->prepare("SELECT SUM(stock) as v_stock FROM variant_stocks WHERE variant_id IN (SELECT id FROM product_variants WHERE product_id = ?)");
                                    $stmt->execute([$p['id']]);
                                    $vStock = $stmt->fetch()['v_stock'];
                                    $displayStock = ($vCount > 0) ? ($vStock ?? 0) : $p['stock'];
                                    echo "Qty: " . $displayStock; 
                                    ?>
                                </div>
                                <?php if($vCount > 0): ?>
                                    <span class="badge bg-accent-warm text-white rounded-pill fs-9 mt-1"><?php echo $vCount; ?> Variants</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end px-3">
                                <a href="admin_product_add.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-dark rounded-0 me-2" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger rounded-0 delete-btn" data-id="<?php echo $p['id']; ?>" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary-light py-5">
                                No products found in the database.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-boreal py-4 border-top mt-auto bg-light">
        <div class="container text-center">
             <p class="text-secondary fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. Admin Services.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function() {
                if(!confirm('Are you sure you want to delete this product? This action cannot be undone.')) return;
                
                const productId = $(this).data('id');
                const btn = $(this);
                btn.prop('disabled', true);
                
                $.ajax({
                    url: 'api/admin_product_action.php',
                    method: 'POST',
                    data: { action: 'delete', id: productId },
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            $('#product-row-' + productId).fadeOut(400, function() { $(this).remove(); });
                            showAlert('success', response.message);
                        } else {
                            showAlert('danger', response.message);
                            btn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        showAlert('danger', 'Server error. Could not delete product.');
                        btn.prop('disabled', false);
                    }
                });
            });

            function showAlert(type, message) {
                const alertEl = $('#action-alert');
                let html = type === 'success' 
                    ? `<i class="bi bi-check-circle me-2"></i> ${message}`
                    : `<i class="bi bi-exclamation-triangle me-2"></i> ${message}`;
                
                alertEl.removeClass('d-none alert-success alert-danger bg-dark text-success text-danger')
                       .addClass(`alert-${type} bg-dark text-${type}`)
                       .html(html);
                
                setTimeout(() => alertEl.addClass('d-none'), 3000);
            }
        });
    </script>
</body>
</html>
