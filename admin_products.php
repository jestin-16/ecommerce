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
<body class="luxury-dark-theme bg-boreal-darker d-flex flex-column min-vh-100">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top p-3 boreal-navbar bg-boreal-dark border-bottom border-dark-subtle">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-white boreal-brand" href="index.php">
                <i class="bi bi-asterisk me-2"></i>BOREAL <span class="fs-6 text-accent fw-normal text-capitalize ms-2">Admin</span>
            </a>
            <div class="d-flex align-items-center gap-4 text-white ms-auto">
                 <a class="nav-link text-white text-uppercase fs-7 tracking-wider d-none d-md-block" href="admin_dashboard.php">Dashboard</a>
                 <a class="nav-link text-white text-uppercase fs-7 tracking-wider d-none d-md-block" href="admin_orders.php">Orders</a>
                 <a class="nav-link text-white text-uppercase fs-7 tracking-wider" href="index.php">Return to Shop</a>
            </div>
        </div>
    </nav>

    <section class="flex-grow-1 py-5">
        <div class="container-fluid px-lg-5">
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom border-dark-subtle pb-3">
                <div>
                     <h2 class="text-white font-playfair mb-1">Manage Products</h2>
                     <p class="text-secondary-light tracking-wider fs-8 text-uppercase mb-0">Total: <?php echo count($products); ?> items</p>
                </div>
                <a href="admin_product_add.php" class="btn btn-outline-light rounded-0 text-uppercase fs-8 tracking-wider">
                    <i class="bi bi-plus me-1"></i> Add New Product
                </a>
            </div>

            <div id="action-alert" class="alert d-none rounded-0 border-0 fs-7"></div>

            <div class="table-responsive">
                <table class="table table-dark table-hover border-secondary align-middle">
                    <thead>
                        <tr class="text-secondary-light text-uppercase tracking-wider fs-8">
                            <th scope="col" class="border-secondary fw-normal py-3 px-3">ID</th>
                            <th scope="col" class="border-secondary fw-normal py-3">Image</th>
                            <th scope="col" class="border-secondary fw-normal py-3">Name</th>
                            <th scope="col" class="border-secondary fw-normal py-3">Category</th>
                            <th scope="col" class="border-secondary fw-normal py-3">Price</th>
                            <th scope="col" class="border-secondary fw-normal py-3">Stock</th>
                            <th scope="col" class="border-secondary fw-normal py-3 text-end px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): ?>
                        <tr class="border-secondary" id="product-row-<?php echo $p['id']; ?>">
                            <td class="font-mono text-white fs-7 px-3">#<?php echo $p['id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="" style="width: 40px; height: 50px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                            </td>
                            <td class="text-white font-playfair fs-6"><?php echo htmlspecialchars($p['name']); ?></td>
                            <td class="text-secondary-light fs-8 text-uppercase tracking-wider"><?php echo htmlspecialchars($p['category']); ?></td>
                            <td class="font-mono text-accent fs-7">$<?php echo number_format($p['price'], 2); ?></td>
                            <td class="font-mono text-white fs-7 <?php echo $p['stock'] <= 5 ? 'text-danger' : ''; ?>"><?php echo $p['stock']; ?></td>
                            <td class="text-end px-3">
                                <a href="admin_product_add.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-secondary rounded-0 me-2" title="Edit">
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
    <footer class="footer-boreal py-4 border-top border-dark-subtle mt-auto bg-boreal-deep">
        <div class="container text-center">
             <p class="text-secondary-light fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. Admin Services.</p>
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
