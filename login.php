<?php
session_start();
require_once 'api/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BOREAL</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body class="luxury-dark-theme d-flex align-items-center justify-content-center vh-100 bg-boreal-darker">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card bg-boreal-dark border border-dark-subtle rounded-0 shadow-luxury p-4">
                    <div class="text-center mb-4">
                        <a class="text-decoration-none text-uppercase fw-bold fs-3 tracking-wide text-white boreal-brand" href="index.php">
                            <i class="bi bi-asterisk me-2"></i>BOREAL
                        </a>
                        <p class="text-secondary-light mt-2 mb-0">Sign in to your account</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-0 border-0 fs-7" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label text-white text-uppercase tracking-wider fs-8">Email Address</label>
                            <input type="email" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" id="email" name="email" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label text-white text-uppercase tracking-wider fs-8 d-flex justify-content-between">
                                Password
                                <a href="#" class="text-secondary-light text-decoration-none hover-white">Forgot?</a>
                            </label>
                            <input type="password" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-light rounded-0 w-100 py-2 text-uppercase fs-7 fw-bold">Sign In</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-secondary-light fs-8 mb-0">Don't have an account? <a href="register.php" class="text-white text-decoration-none hover-accent">Register</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
