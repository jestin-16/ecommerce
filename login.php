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
    <style>
        .login-split-container {
            min-height: 100vh;
        }
        .login-image-side {
            background-image: url('https://i.pinimg.com/736x/86/5c/58/865c58972f07d2c38d960098f98ec8d6.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .login-image-side::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(26, 22, 22, 0.2);
            mix-blend-mode: multiply;
        }
        .login-form-side {
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .form-container {
            width: 100%;
            max-width: 400px;
            z-index: 2;
        }
        .brand-snow {
            color: #4A90E2;
        }
        .snow-deco-small {
            position: absolute;
            opacity: 0.05;
            color: #1a1616;
            pointer-events: none;
            z-index: 1;
        }
    </style>
</head>
<body class="luxury-light-theme">
    <div class="container-fluid p-0">
        <div class="row g-0 login-split-container">
            <!-- Image Side -->
            <div class="col-lg-6 d-none d-lg-block login-image-side">
                <div class="position-absolute bottom-0 start-0 p-5 text-white z-2">
                    <p class="text-uppercase tracking-widest fs-8 mb-2">Winter Collection - 2026</p>
                    <h2 class="font-playfair display-4 mb-4">REDEFINING<br>LUXURY WARMTH</h2>
                    <div class="d-flex gap-2">
                        <i class="bi bi-asterisk"></i>
                        <i class="bi bi-asterisk"></i>
                        <i class="bi bi-asterisk"></i>
                    </div>
                </div>
            </div>

            <!-- Form Side -->
            <div class="col-lg-6 login-form-side">
                <!-- Atmospheric Decor -->
                <div class="snow-deco-small" style="top: 10%; right: 10%; font-size: 80px;"><i class="bi bi-snow"></i></div>
                <div class="snow-deco-small" style="bottom: 15%; left: 5%; font-size: 60px;"><i class="bi bi-snow"></i></div>

                <div class="form-container">
                    <div class="text-center mb-5">
                        <a class="text-decoration-none text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand" href="index.php">
                            <i class="bi bi-asterisk me-2 brand-snow"></i>BOREAL
                        </a>
                        <h2 class="font-playfair mt-4 mb-2">Welcome Back</h2>
                        <p class="text-secondary fs-7 tracking-wider text-uppercase">Enter your credentials to access your account</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-0 border-0 fs-7 mb-4 py-3" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-4">
                            <label for="email" class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Email Address</label>
                            <input type="email" class="form-control bg-transparent border-dark border-opacity-10 text-dark rounded-0 shadow-none ps-3 py-3 fs-7" id="email" name="email" required placeholder="name@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold d-flex justify-content-between">
                                Password
                                <a href="#" class="text-secondary text-decoration-none hover-dark fw-normal">Forgot?</a>
                            </label>
                            <input type="password" class="form-control bg-transparent border-dark border-opacity-10 text-dark rounded-0 shadow-none ps-3 py-3 fs-7" id="password" name="password" required placeholder="••••••••">
                        </div>
                        
                        <div class="mb-4 form-check mt-3">
                            <input type="checkbox" class="form-check-input rounded-0 border-dark border-opacity-25 shadow-none" id="remember">
                            <label class="form-check-label fs-8 text-secondary text-uppercase tracking-wider pt-1" for="remember">Keep me signed in</label>
                        </div>

                        <button type="submit" class="btn btn-dark rounded-0 w-100 py-3 text-uppercase fs-7 fw-bold tracking-widest mt-2 hover-lift">Sign In</button>
                    </form>
                    
                    <div class="text-center mt-5 pt-4 border-top border-light">
                        <p class="text-secondary fs-8 text-uppercase tracking-widest mb-0">Discover the collection. <a href="register.php" class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1 ms-1">Create Account</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
