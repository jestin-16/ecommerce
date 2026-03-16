<?php
session_start();
require_once 'api/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    header("Location: admin_orders.php");
    exit;
}

// Fetch order
$stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: admin_orders.php");
    exit;
}

// Fetch order items
$stmt = $pdo->prepare("SELECT oi.*, p.name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$date = date('F j, Y, g:i a', strtotime($order['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?> - Admin BOREAL</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body class="luxury-light-theme bg-white d-flex flex-column min-vh-100">
    
    <nav class="navbar navbar-expand-lg sticky-top p-3 boreal-navbar bg-white border-bottom border-light">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand" href="admin_orders.php">
                <i class="bi bi-asterisk me-2"></i>BOREAL <span class="fs-6 text-accent fw-normal text-capitalize ms-2">Admin</span>
            </a>
            <div class="d-flex align-items-center gap-4 text-dark ms-auto">
                 <a class="nav-link text-dark text-uppercase fs-7 tracking-wider d-none d-md-block" href="admin_orders.php">Back to Orders</a>
            </div>
        </div>
    </nav>

    <section class="flex-grow-1 py-5">
        <div class="container-fluid px-lg-5">
            <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-3 mb-5">
                <div>
                    <h2 class="text-dark font-playfair mb-1">Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></h2>
                    <p class="text-secondary tracking-wider fs-8 text-uppercase mb-0"><?php echo $date; ?></p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark border border-light rounded-0 px-3 py-2 text-uppercase tracking-wider">
                        STATUS: <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6 text-dark">
                    <h5 class="font-playfair text-secondary mb-3">Customer Information</h5>
                    <p class="mb-1 fw-bold"><?php echo htmlspecialchars($order['user_name']); ?></p>
                    <p class="mb-0 text-secondary fs-7"><?php echo htmlspecialchars($order['user_email']); ?></p>
                </div>
                <div class="col-md-6 text-dark text-md-end">
                    <h5 class="font-playfair text-secondary mb-3">Shipping Address</h5>
                    <p class="mb-0 fs-7" style="white-space: pre-wrap;"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                </div>
            </div>

            <h4 class="text-dark font-playfair mb-4">Items Included</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle border-light">
                    <thead>
                        <tr class="text-secondary text-uppercase tracking-wider fs-8 bg-light">
                            <th scope="col" class="fw-normal py-3 px-3">Item</th>
                            <th scope="col" class="fw-normal py-3 px-3 text-end">Price</th>
                            <th scope="col" class="fw-normal py-3 px-3 text-center">QTY</th>
                            <th scope="col" class="fw-normal py-3 text-end px-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr class="border-light">
                            <td class="px-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="" style="width: 50px; height: 60px; object-fit: cover; border: 1px solid rgba(0,0,0,0.05);">
                                    <span class="text-dark font-playfair fs-6 fw-bold"><?php echo htmlspecialchars($item['name']); ?></span>
                                </div>
                            </td>
                            <td class="text-end px-3 font-mono text-secondary fs-7">₹<?php echo number_format($item['price_at_purchase'], 2); ?></td>
                            <td class="text-center px-3 text-dark fs-7"><?php echo $item['quantity']; ?></td>
                            <td class="text-end px-3 font-mono text-dark fs-6 fw-bold">₹<?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end py-4 px-3 text-secondary text-uppercase tracking-wider fs-7">Order Total</td>
                            <td class="text-end py-4 px-3 font-mono text-dark fs-4 fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="text-center mt-5">
                 <a href="admin_orders.php" class="btn btn-dark rounded-0 text-uppercase tracking-wider fs-8 px-5 py-3">Return to Orders</a>
            </div>
        </div>
    </section>

    <footer class="footer-boreal py-4 border-top mt-auto bg-light">
        <div class="container text-center">
             <p class="text-secondary fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. Admin Services.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
