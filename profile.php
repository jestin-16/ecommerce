<?php
session_start();
require_once 'api/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user data from DB
$stmt = $pdo->prepare("SELECT name, email, role, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // If user somehow doesn't exist in DB anymore
    session_destroy();
    header("Location: login.php");
    exit;
}

// Format the date if it exists
$join_date = isset($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'Unknown';

// Fetch User's Order History
$stmt_orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt_orders->execute([$user_id]);
$orders = $stmt_orders->fetchAll();
$total_orders = count($orders);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - BOREAL</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <style>
        .profile-container {
            padding-top: 4rem;
            padding-bottom: 6rem;
        }
        .status-badge {
            font-size: 0.65rem;
            letter-spacing: 0.1em;
            padding: 0.4em 0.8em;
            font-weight: 600;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-completed { background-color: #dcfce7; color: #166534; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        
        .order-row:hover {
            background-color: #fafafa;
        }
    </style>
</head>
<body class="luxury-light-theme bg-white d-flex flex-column min-vh-100">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top p-3 boreal-navbar bg-white border-bottom">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand" href="index.php">
                <i class="bi bi-asterisk me-2 brand-snow"></i>BOREAL
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

            <div class="d-flex align-items-center gap-4 text-dark d-none d-lg-flex boreal-nav-icons ms-auto">
                <a href="logout.php" class="text-dark" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></a>
                <a href="profile.php" class="text-dark" title="Profile"><i class="bi bi-person-check fs-5"></i></a>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <section class="flex-grow-1 profile-container">
        <div class="container px-lg-5">
            <div class="row g-5">
                <!-- Sidebar: Account Info -->
                <div class="col-lg-4">
                    <div class="card bg-white border rounded-0 p-4 p-md-5 sticky-top" style="top: 100px;">
                        <div class="text-center mb-5">
                            <div class="d-inline-flex justify-content-center align-items-center bg-light border rounded-circle mb-4" style="width: 80px; height: 80px;">
                                <i class="bi bi-person text-dark fs-1"></i>
                            </div>
                            <h2 class="text-dark font-playfair mb-1"><?php echo htmlspecialchars($user['name']); ?></h2>
                            <p class="text-accent text-uppercase tracking-wider fs-8 fw-bold"><?php echo htmlspecialchars($user['role']); ?></p>
                        </div>

                        <div class="profile-details mb-5">
                            <div class="d-flex justify-content-between mb-4 border-bottom pb-2">
                                <span class="text-secondary text-uppercase tracking-wider fs-8">Email</span>
                                <span class="text-dark font-mono fs-7"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-4 border-bottom pb-2">
                                <span class="text-secondary text-uppercase tracking-wider fs-8">Joined</span>
                                <span class="text-dark font-mono fs-7"><?php echo $join_date; ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-0 border-bottom pb-2">
                                <span class="text-secondary text-uppercase tracking-wider fs-8">Total Orders</span>
                                <span class="text-dark font-mono fs-7"><?php echo $total_orders; ?></span>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="admin_dashboard.php" class="btn btn-dark rounded-0 py-3 text-uppercase fs-8 fw-bold tracking-widest">Admin Dashboard</a>
                            <?php endif; ?>
                            <a href="logout.php" class="btn btn-outline-danger rounded-0 py-3 text-uppercase fs-8 fw-bold tracking-widest">Log Out</a>
                        </div>
                    </div>
                </div>

                <!-- Main: Order History -->
                <div class="col-lg-8">
                    <div class="card bg-white border rounded-0 p-4 p-md-5 h-100">
                        <h3 class="text-dark font-playfair mb-5">Order History</h3>

                        <?php if (empty($orders)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-box-seam text-light fs-1 mb-4 d-block" style="font-size: 4rem !important; opacity: 0.3;"></i>
                                <h4 class="font-playfair text-secondary">No orders yet</h4>
                                <p class="text-muted fs-7 mb-4">Your curated winter pieces will appear here after purchase.</p>
                                <a href="index.php" class="btn btn-outline-dark rounded-0 px-5 py-3 text-uppercase fs-8 fw-bold tracking-wider">Start Shopping</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 text-uppercase tracking-wider fs-9 py-3 pw-bold">Order ID</th>
                                            <th class="border-0 text-uppercase tracking-wider fs-9 py-3">Date</th>
                                            <th class="border-0 text-uppercase tracking-wider fs-9 py-3">Status</th>
                                            <th class="border-0 text-uppercase tracking-wider fs-9 py-3 text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): 
                                            $statusClass = 'status-' . strtolower($order['status']);
                                            $orderDate = date('M j, Y', strtotime($order['created_at']));
                                        ?>
                                            <tr class="order-row">
                                                <td class="py-4 font-mono fs-7">#<?php echo $order['id']; ?></td>
                                                <td class="py-4 text-secondary fs-7"><?php echo $orderDate; ?></td>
                                                <td class="py-4">
                                                    <span class="badge rounded-0 status-badge <?php echo $statusClass; ?>">
                                                        <?php echo strtoupper($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 text-end text-dark font-mono fw-bold fs-6">
                                                    ₹<?php echo number_format($order['total_amount'], 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-boreal py-4 mt-auto bg-light border-top">
        <div class="container px-lg-5 text-center">
             <p class="text-secondary fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
