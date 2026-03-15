<?php
session_start();
require_once 'api/db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user data from DB
$stmt = $pdo->prepare("SELECT name, email, role, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$join_date = isset($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'Unknown';

// Get total number of users for the dashboard
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch()['total_users'];

// Get total number of admins
$stmt = $pdo->query("SELECT COUNT(*) as total_admins FROM users WHERE role = 'admin'");
$total_admins = $stmt->fetch()['total_admins'];

// Get total number of products
$stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
$total_products = $stmt->fetch()['total_products'];

// Get total number of orders
$stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
$total_orders = $stmt->fetch()['total_orders'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BOREAL</title>
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

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#borealNav"
                aria-controls="borealNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="borealNav">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item"><a class="nav-link text-dark text-uppercase fs-7 tracking-wider" href="index.php">Return to Shop</a></li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-4 text-dark d-none d-lg-flex boreal-nav-icons">
                <a href="logout.php" class="text-dark" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></a>
                <a href="profile.php" class="text-dark" title="Profile"><i class="bi bi-person-check fs-5"></i></a>
            </div>
        </div>
    </nav>

    <!-- Admin Dashboard Section -->
    <section class="flex-grow-1 py-6 d-flex align-items-center">
        <div class="container px-lg-5">
            <div class="row mb-5">
                <div class="col-12">
                     <h1 class="text-white font-playfair mb-2">Admin Dashboard</h1>
                     <p class="text-secondary-light">Welcome back, <?php echo htmlspecialchars($user['name']); ?>.</p>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <!-- Stat Card 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-white border border-light rounded-0 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-secondary text-uppercase tracking-wider fs-8 mb-0">Total Users</h5>
                            <i class="bi bi-people text-accent fs-4"></i>
                        </div>
                        <h2 class="text-dark font-mono mb-0"><?php echo $total_users; ?></h2>
                    </div>
                </div>
                
                <!-- Stat Card 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-white border border-light rounded-0 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-secondary text-uppercase tracking-wider fs-8 mb-0">Orders</h5>
                            <i class="bi bi-bag text-accent fs-4"></i>
                        </div>
                        <h2 class="text-dark font-mono mb-0"><?php echo $total_orders; ?></h2>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-white border border-light rounded-0 shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-secondary text-uppercase tracking-wider fs-8 mb-0">Products</h5>
                            <i class="bi bi-box-seam text-accent fs-4"></i>
                        </div>
                        <h2 class="text-dark font-mono mb-0"><?php echo $total_products; ?></h2>
                    </div>
                </div>

                <!-- Stat Card 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-white border border-light rounded-0 shadow-sm p-4 h-100">
                         <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-secondary text-uppercase tracking-wider fs-8 mb-0">Quick Actions</h5>
                            <i class="bi bi-lightning text-accent fs-4"></i>
                        </div>
                        <div class="mt-auto">
                            <a href="admin_products.php" class="btn btn-outline-dark rounded-0 w-100 py-2 text-uppercase fs-8 fw-bold tracking-wider hover-dark mb-2">
                                <i class="bi bi-box me-2"></i>Manage Products
                            </a>
                            <a href="admin_orders.php" class="btn btn-outline-dark rounded-0 w-100 py-2 text-uppercase fs-8 fw-bold tracking-wider hover-dark mb-2">
                                <i class="bi bi-cart me-2"></i>Manage Orders
                            </a>
                            <a href="add_admin.php" class="btn btn-outline-dark rounded-0 w-100 py-2 text-uppercase fs-8 fw-bold tracking-wider hover-dark">
                                <i class="bi bi-person-plus me-2"></i>Add New Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card bg-white border border-light rounded-0 shadow-sm p-4">
                        <h4 class="text-dark font-playfair mb-4">Recent Activity</h4>
                        <div class="text-center py-5">
                            <i class="bi bi-clock-history text-secondary-light fs-1 mb-3"></i>
                            <p class="text-secondary mb-0">No recent activity to display.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-boreal py-4 bg-light mt-auto border-top">
        <div class="container px-lg-5 text-center">
             <p class="text-secondary fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
