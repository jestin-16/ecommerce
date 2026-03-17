<?php
session_start();
require_once 'api/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email)) {
        $error = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
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
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap"
        rel="stylesheet">
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-image-side::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .glass-hero-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3.5rem;
            max-width: 80%;
            z-index: 2;
            text-align: center;
        }

        .premium-badge {
            position: absolute;
            top: 3rem;
            left: 3rem;
            z-index: 2;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1rem;
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: white;
        }

        .floating-accent {
            position: absolute;
            color: rgba(255, 255, 255, 0.1);
            pointer-events: none;
            z-index: 1;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
            100% { transform: translateY(0) rotate(0deg); }
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
                <div class="premium-badge">Established in 2026</div>

                <!-- Floating Ornaments -->
                <div class="floating-accent" style="top: 15%; left: 10%; font-size: 4rem;"><i class="bi bi-asterisk"></i></div>
                <div class="floating-accent" style="bottom: 25%; right: 15%; font-size: 3rem; animation-delay: -5s;"><i class="bi bi-snow"></i></div>
                <div class="floating-accent" style="top: 60%; left: 15%; font-size: 2rem; animation-delay: -12s;"><i class="bi bi-asterisk"></i></div>

                <div class="glass-hero-card">
                    <span class="text-uppercase tracking-widest fs-8 mb-3 d-block text-white opacity-75">Core Principles</span>
                    <h1 class="font-playfair display-5 text-white mb-4 italic">"True luxury is found in the silence of quality."</h1>
                    <div class="w-25 mx-auto border-top border-white opacity-25 mb-4"></div>
                    <p class="text-white fs-7 opacity-75 tracking-wider text-uppercase">Crafting warmth for the Arctic modernist with ethical precision and timeless design.</p>
                </div>

                <div class="position-absolute bottom-0 start-0 p-5 text-white z-2">
                    <p class="text-uppercase tracking-widest fs-8 mb-2">Winter Collection</p>
                    <h2 class="font-playfair fs-3 mb-0">REDEFINING LUXURY WARMTH</h2>
                </div>
            </div>

            <!-- Form Side -->
            <div class="col-lg-6 login-form-side">
                <!-- Atmospheric Decor -->
                <div class="snow-deco-small" style="top: 10%; right: 10%; font-size: 80px;"><i class="bi bi-snow"></i>
                </div>
                <div class="snow-deco-small" style="bottom: 15%; left: 5%; font-size: 60px;"><i class="bi bi-snow"></i>
                </div>

                <div class="form-container">
                    <div class="text-center mb-5">
                        <a class="text-decoration-none text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand"
                            href="index.php">
                            <i class="bi bi-asterisk me-2 brand-snow"></i>BOREAL
                        </a>
                        <h2 class="font-playfair mt-4 mb-2">Welcome Back</h2>
                        <p class="text-secondary fs-7 tracking-wider text-uppercase">Enter your credentials to access
                            your account</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-0 border-0 fs-7 mb-4 py-3" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="email"
                                class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Email
                                Address</label>
                            <input type="email"
                                class="form-control bg-transparent border-dark border-opacity-10 text-dark rounded-0 shadow-none ps-3 py-3 fs-7"
                                id="email" name="email" required placeholder="name@example.com">
                            <div class="invalid-feedback fs-8">
                                Please provide a valid email address.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password"
                                class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold d-flex justify-content-between">
                                Password
                                <a href="#" class="text-secondary text-decoration-none hover-dark fw-normal">Forgot?</a>
                            </label>
                            <input type="password"
                                class="form-control bg-transparent border-dark border-opacity-10 text-dark rounded-0 shadow-none ps-3 py-3 fs-7"
                                id="password" name="password" required placeholder="••••••••">
                            <div class="invalid-feedback fs-8">
                                Please enter your password.
                            </div>
                        </div>

                        <div class="mb-4 form-check mt-3">
                            <input type="checkbox"
                                class="form-check-input rounded-0 border-dark border-opacity-25 shadow-none"
                                id="remember">
                            <label class="form-check-label fs-8 text-secondary text-uppercase tracking-wider pt-1"
                                for="remember">Keep me signed in</label>
                        </div>

                        <button type="submit"
                            class="btn btn-dark rounded-0 w-100 py-3 text-uppercase fs-7 fw-bold tracking-widest mt-2 hover-lift">Sign
                            In</button>
                    </form>

                    <div class="text-center mt-5 pt-4 border-top border-light">
                        <p class="text-secondary fs-8 text-uppercase tracking-widest mb-0">Discover the collection. <a
                                href="register.php"
                                class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1 ms-1">Create
                                Account</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
          'use strict'

          // Fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.querySelectorAll('.needs-validation')

          // Loop over them and prevent submission
          Array.prototype.slice.call(forms)
            .forEach(function (form) {
              form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                  event.preventDefault()
                  event.stopPropagation()
                }

                form.classList.add('was-validated')
              }, false)
            })
        })()
    </script>
</body>
</html>