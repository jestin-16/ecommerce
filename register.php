<?php
session_start();
require_once 'api/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $terms = isset($_POST['terms']);

    if (empty($name)) {
        $error = 'Full name is required.';
    } elseif (empty($email)) {
        $error = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!$terms) {
        $error = 'You must agree to the Terms & Conditions.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered. Please log in.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                // Get the generated user ID
                $user_id = $pdo->lastInsertId();

                // Log the user in
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'user';
                $_SESSION['name'] = $name;

                header("Location: index.php");
                exit;
            } else {
                $error = 'An error occurred during registration. Please try again.';
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
    <title>Register - BOREAL</title>
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
            background-image: url('https://i.pinimg.com/736x/8a/78/3f/8a783f06c116cfc9da1c2b535d45d36e.jpg');
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
            max-width: 440px;
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
                <div class="premium-badge">Crafted for the Arctic</div>

                <!-- Floating Ornaments -->
                <div class="floating-accent" style="top: 20%; right: 15%; font-size: 3rem;"><i class="bi bi-snow"></i></div>
                <div class="floating-accent" style="bottom: 20%; left: 10%; font-size: 4rem; animation-delay: -7s;"><i class="bi bi-asterisk"></i></div>
                <div class="floating-accent" style="top: 50%; right: 10%; font-size: 2rem; animation-delay: -15s;"><i class="bi bi-asterisk"></i></div>

                <div class="glass-hero-card">
                    <span class="text-uppercase tracking-widest fs-8 mb-3 d-block text-white opacity-75">The BOREAL Promise</span>
                    <h1 class="font-playfair display-5 text-white mb-4">"Eternal style, enduring warmth."</h1>
                    <div class="w-25 mx-auto border-top border-white opacity-25 mb-4"></div>
                    <p class="text-white fs-7 opacity-75 tracking-wider text-uppercase">Join an exclusive circle dedicated to slow fashion, premium materials, and the art of winter living.</p>
                </div>

                <div class="position-absolute bottom-0 start-0 p-5 text-white z-2">
                    <p class="text-uppercase tracking-widest fs-8 mb-2">Arctic Modern Heritage</p>
                    <h2 class="font-playfair fs-3 mb-0">CRAFTED FOR ENDURANCE</h2>
                </div>
            </div>

            <!-- Form Side -->
            <div class="col-lg-6 login-form-side">
                <!-- Atmospheric Decor -->
                <div class="snow-deco-small" style="top: 8%; left: 12%; font-size: 90px;"><i class="bi bi-snow"></i>
                </div>
                <div class="snow-deco-small" style="bottom: 12%; right: 8%; font-size: 70px;"><i class="bi bi-snow"></i>
                </div>

                <div class="form-container">
                    <div class="text-center mb-5">
                        <a class="text-decoration-none text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand"
                            href="index.php">
                            <i class="bi bi-asterisk me-2 brand-snow"></i>BOREAL
                        </a>
                        <h2 class="font-playfair mt-4 mb-2">Create Account</h2>
                        <p class="text-secondary fs-7 tracking-wider text-uppercase">Join us to experience winter luxury
                            redefined</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-0 border-0 fs-7 mb-4 py-3" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="name"
                                class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Full
                                Name</label>
                            <input type="text"
                                class="form-control bg-transparent border-dark border-opacity-10 text-dark rounded-0 shadow-none ps-3 py-3 fs-7"
                                id="name" name="name" required placeholder="John Doe"
                                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            <div class="invalid-feedback fs-8">Please enter your full name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email"
                                class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Email
                                Address</label>
                            <input type="email"
                                class="form-control bg-transparent border-dark border-opacity-10 text-dark rounded-0 shadow-none ps-3 py-3 fs-7"
                                id="email" name="email" required placeholder="name@example.com"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <div class="invalid-feedback fs-8">Please provide a valid email address.</div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="password"
                                    class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Password</label>
                                <input type="password"
                                    class="form-control bg-transparent border-dark border-opacity-10 text-dark rounded-0 shadow-none ps-3 py-3 fs-7"
                                    id="password" name="password" required minlength="6" placeholder="••••••••">
                                <div class="invalid-feedback fs-8">At least 6 characters.</div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="confirm_password"
                                    class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Confirm</label>
                                <input type="password"
                                    class="form-control bg-transparent border-dark border-opacity-10 text-dark rounded-0 shadow-none ps-3 py-3 fs-7"
                                    id="confirm_password" name="confirm_password" required placeholder="••••••••">
                                <div class="invalid-feedback fs-8">Passwords must match.</div>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox"
                                class="form-check-input rounded-0 border-dark border-opacity-25 shadow-none" id="terms"
                                name="terms" required>
                            <label class="form-check-label fs-8 text-secondary text-uppercase tracking-wider pt-1"
                                for="terms">I agree to the <a href="#" class="text-dark">Terms & Conditions</a></label>
                            <div class="invalid-feedback fs-8">You must agree before submitting.</div>
                        </div>

                        <button type="submit"
                            class="btn btn-dark rounded-0 w-100 py-3 text-uppercase fs-7 fw-bold tracking-widest mt-2 hover-lift">Create
                            Account</button>
                    </form>

                    <div class="text-center mt-5 pt-4 border-top border-light">
                        <p class="text-secondary fs-8 text-uppercase tracking-widest mb-0">Already a member? <a
                                href="login.php"
                                class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1 ms-1">Log
                                In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            'use strict'

            var forms = document.querySelectorAll('.needs-validation')

            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        const password = document.getElementById('password');
                        const confirm = document.getElementById('confirm_password');

                        if (password.value !== confirm.value) {
                            confirm.setCustomValidity('Passwords do not match');
                        } else {
                            confirm.setCustomValidity('');
                        }

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