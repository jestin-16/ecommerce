<?php
session_start();
require_once 'api/db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered. Cannot create this admin.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user as an admin
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = 'New admin account created successfully.';
            } else {
                $error = 'An error occurred while creating the admin account.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin - BOREAL</title>
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
                    <li class="nav-item"><a class="nav-link text-dark text-uppercase fs-7 tracking-wider" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-dark text-uppercase fs-7 tracking-wider" href="index.php">Return to Shop</a></li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-4 text-dark d-none d-lg-flex boreal-nav-icons">
                <a href="logout.php" class="text-dark" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></a>
                <a href="profile.php" class="text-dark" title="Profile"><i class="bi bi-person-check fs-5"></i></a>
            </div>
        </div>
    </nav>

    <!-- Add Admin Section -->
    <section class="flex-grow-1 py-6 d-flex align-items-center justify-content-center">
        <div class="container px-lg-5">
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="card bg-white border border-light rounded-0 shadow-sm p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h2 class="text-dark font-playfair mb-1">Add New Admin</h2>
                            <p class="text-secondary tracking-wider fs-8 text-uppercase">Create administrative account</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger rounded-0 border-0 fs-7" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success rounded-0 border-0 fs-7 bg-light text-success border-success" role="alert" style="border: 1px solid rgba(25, 135, 84, 0.2) !important;">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form action="add_admin.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Admin Name</label>
                                <input type="text" class="form-control border-light rounded-0 shadow-none ps-2" id="name" name="name" required value="<?php echo isset($_POST['name']) && empty($success) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Admin Email</label>
                                <input type="email" class="form-control border-light rounded-0 shadow-none ps-2" id="email" name="email" required value="<?php echo isset($_POST['email']) && empty($success) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Password</label>
                                <input type="password" class="form-control border-light rounded-0 shadow-none ps-2" id="password" name="password" required>
                                <div class="form-text text-secondary fs-8 mt-1">Make sure you use a strong password.</div>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Confirm Password</label>
                                <input type="password" class="form-control border-light rounded-0 shadow-none ps-2" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <div class="d-grid gap-3 mt-5">
                                <button type="submit" class="btn btn-dark rounded-0 py-3 text-uppercase fs-7 fw-bold tracking-wider">Create Admin Account</button>
                                <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-0 py-3 text-uppercase fs-7 fw-bold tracking-wider hover-dark">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-boreal py-4 bg-light mt-auto border-top border-light">
        <div class="container px-lg-5 text-center">
             <p class="text-secondary fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
