<?php
session_start();
require_once 'api/db.php';

// Must be logged in and admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("
    SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Dashboard</title>
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
                 <a class="nav-link text-dark text-uppercase fs-7 tracking-wider d-none d-md-block" href="admin_products.php">Products</a>
                 <a class="nav-link text-dark text-uppercase fs-7 tracking-wider" href="index.php">Return to Shop</a>
            </div>
        </div>
    </nav>

    <section class="flex-grow-1 py-5">
        <div class="container-fluid px-lg-5">
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom border-dark-subtle pb-3">
                <div>
                     <h2 class="text-dark font-playfair mb-1">Manage Orders</h2>
                     <p class="text-secondary-light tracking-wider fs-8 text-uppercase mb-0">Total: <?php echo count($orders); ?> orders</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border-light">
                    <thead>
                        <tr class="text-secondary text-uppercase tracking-wider fs-8 bg-light">
                            <th scope="col" class="fw-normal py-3 px-3">Order ID</th>
                            <th scope="col" class="fw-normal py-3">Customer</th>
                            <th scope="col" class="fw-normal py-3">Date</th>
                            <th scope="col" class="fw-normal py-3">Total</th>
                            <th scope="col" class="fw-normal py-3">Status</th>
                            <th scope="col" class="fw-normal py-3 text-end px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): 
                            $date = date('M j, Y H:i', strtotime($o['created_at']));
                            
                            $statusClass = 'bg-secondary';
                            if($o['status'] === 'Processing') $statusClass = 'bg-primary';
                            if($o['status'] === 'Shipped') $statusClass = 'bg-info bg-opacity-75';
                            if($o['status'] === 'Delivered') $statusClass = 'bg-success';
                            if($o['status'] === 'Cancelled') $statusClass = 'bg-danger';
                        ?>
                        <tr class="border-light text-secondary">
                            <td class="font-mono text-dark fs-7 px-3">#<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <div class="text-dark fs-7 fw-bold"><?php echo htmlspecialchars($o['user_name']); ?></div>
                                <div class="text-secondary fs-8"><?php echo htmlspecialchars($o['user_email']); ?></div>
                            </td>
                            <td class="text-secondary fs-7"><?php echo $date; ?></td>
                            <td class="font-mono text-dark fs-7 fw-bold">₹<?php echo number_format($o['total_amount'], 2); ?></td>
                            <td>
                                <select class="form-select form-select-sm bg-light text-dark border-light rounded-0 shadow-none status-select" data-id="<?php echo $o['id']; ?>" style="width: auto; font-size: 0.8rem;">
                                    <option value="Pending" <?php echo $o['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Processing" <?php echo $o['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Shipped" <?php echo $o['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $o['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $o['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td class="text-end px-3">
                                <a href="admin_order_details.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-dark rounded-0 text-uppercase tracking-wider fs-8">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-secondary-light py-5">
                                No orders have been placed yet.
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
            $('.status-select').on('change', function() {
                const orderId = $(this).data('id');
                const newStatus = $(this).val();
                
                $.ajax({
                    url: 'api/admin_order_update.php',
                    method: 'POST',
                    data: { order_id: orderId, status: newStatus },
                    dataType: 'json',
                    success: function(response) {
                        const alertEl = $('#action-alert');
                        if(response.success) {
                            alertEl.removeClass('d-none alert-danger').addClass('alert-success bg-light text-success').html('<i class="bi bi-check-circle me-2"></i>' + response.message);
                        } else {
                            alertEl.removeClass('d-none alert-success').addClass('alert-danger text-danger bg-light').html('<i class="bi bi-exclamation-triangle me-2"></i>' + response.message);
                        }
                        setTimeout(() => alertEl.addClass('d-none'), 3000);
                    },
                    error: function() {
                        const alertEl = $('#action-alert');
                        alertEl.removeClass('d-none alert-success').addClass('alert-danger text-danger bg-dark').text('Server error updating status.');
                        setTimeout(() => alertEl.addClass('d-none'), 3000);
                    }
                });
            });
        });
    </script>
</body>
</html>
