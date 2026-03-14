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
</head>
<body class="luxury-dark-theme bg-boreal-darker d-flex flex-column min-vh-100">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top p-3 boreal-navbar bg-boreal-dark border-bottom border-dark-subtle">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-white boreal-brand" href="index.php">
                <i class="bi bi-asterisk me-2"></i>BOREAL
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#borealNav"
                aria-controls="borealNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="borealNav">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item"><a class="nav-link text-white text-uppercase fs-7 tracking-wider" href="index.php">Return to Shop</a></li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-4 text-white d-none d-lg-flex boreal-nav-icons">
                <a href="logout.php" class="text-white" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></a>
                <a href="profile.php" class="text-white" title="Profile"><i class="bi bi-person-check fs-5"></i></a>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <section class="flex-grow-1 py-6 d-flex align-items-center">
        <div class="container px-lg-5">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card bg-boreal-dark border border-dark-subtle rounded-0 shadow-luxury p-4 p-md-5">
                        <div class="text-center mb-5">
                            <div class="d-inline-flex justify-content-center align-items-center bg-dark border border-secondary rounded-circle mb-4" style="width: 80px; height: 80px;">
                                <i class="bi bi-person text-white fs-1"></i>
                            </div>
                            <h2 class="text-white font-playfair mb-1"><?php echo htmlspecialchars($user['name']); ?></h2>
                            <p class="text-accent text-uppercase tracking-wider fs-8"><?php echo htmlspecialchars($user['role']); ?></p>
                        </div>

                        <div class="profile-details mb-5">
                            <div class="row mb-3 border-bottom border-secondary pb-3">
                                <div class="col-4">
                                    <span class="text-secondary-light text-uppercase tracking-wider fs-8">Email</span>
                                </div>
                                <div class="col-8 text-end">
                                    <span class="text-white font-mono fs-7"><?php echo htmlspecialchars($user['email']); ?></span>
                                </div>
                            </div>
                            <div class="row mb-3 border-bottom border-secondary pb-3">
                                <div class="col-4">
                                    <span class="text-secondary-light text-uppercase tracking-wider fs-8">Member Since</span>
                                </div>
                                <div class="col-8 text-end">
                                    <span class="text-white font-mono fs-7"><?php echo $join_date; ?></span>
                                </div>
                            </div>
                            <div class="row mb-3 border-bottom border-secondary pb-3">
                                <div class="col-4">
                                    <span class="text-secondary-light text-uppercase tracking-wider fs-8">Orders</span>
                                </div>
                                <div class="col-8 text-end">
                                    <span class="text-white font-mono fs-7">0</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="admin_dashboard.php" class="btn btn-outline-light rounded-0 py-3 text-uppercase fs-7 fw-bold tracking-wider hover-dark">Admin Dashboard</a>
                            <?php endif; ?>
                            <a href="index.php" class="btn btn-outline-light rounded-0 py-3 text-uppercase fs-7 fw-bold tracking-wider hover-dark">Continue Shopping</a>
                            <a href="logout.php" class="btn btn-danger rounded-0 py-3 text-uppercase fs-7 fw-bold tracking-wider opacity-75 hover-opacity-100">Log Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-boreal py-4 bg-boreal-deep mt-auto border-top border-dark-subtle">
        <div class="container px-lg-5 text-center">
             <p class="text-secondary-light fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
