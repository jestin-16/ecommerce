<?php
session_start();
require_once 'api/db.php';

$error = '';

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
                        <p class="text-secondary-light mt-2 mb-0">Create an account</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-0 border-0 fs-7" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label text-white text-uppercase tracking-wider fs-8">Full Name</label>
                            <input type="text" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-white text-uppercase tracking-wider fs-8">Email Address</label>
                            <input type="email" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-white text-uppercase tracking-wider fs-8">Password</label>
                            <input type="password" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" id="password" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label text-white text-uppercase tracking-wider fs-8">Confirm Password</label>
                            <input type="password" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-light rounded-0 w-100 py-2 text-uppercase fs-7 fw-bold">Create Account</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-secondary-light fs-8 mb-0">Already have an account? <a href="login.php" class="text-white text-decoration-none hover-accent">Log In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
