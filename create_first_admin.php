<?php
require_once 'api/db.php';

$email = 'admin@boreal.com';
$password = 'admin123';
$name = 'System Admin';

// Check if admin already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo "Admin account already exists! You can log in with $email.";
} else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
    
    if ($stmt->execute([$name, $email, $hashed_password])) {
        echo "Successfully created the first admin account!<br>";
        echo "Email: " . $email . "<br>";
        echo "Password: " . $password . "<br>";
        echo "<a href='login.php'>Go to Login</a>";
    } else {
        echo "Failed to create admin account.";
    }
}
?>
