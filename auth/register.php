<?php
require_once __DIR__ . '/../config/db.php';

function log_auth_error($message) {
    $log_file = __DIR__ . '/../logs/auth.log';
    file_put_contents($log_file, date('c') . ' - ' . $message . "\n", FILE_APPEND);
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    log_auth_error("Registration failed: Invalid input for email '{$email}'.");
    header('Location: ../auth.html?error=invalid_input');
    exit;
}

// Check if user already exists
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: ../auth.html?error=user_exists');
        exit;
    }
} catch (PDOException $e) {
    log_auth_error("Registration DB Error (checking user existence): " . $e->getMessage());
    header('Location: ../auth.html?error=db_error');
    exit;
}

// Hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user
try {
    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
    $stmt->execute([$email, $passwordHash]);

    header('Location: ../auth.html?success=registered#login-form');
    exit;
} catch (PDOException $e) {
    log_auth_error("Registration DB Error (inserting new user): " . $e->getMessage());
    header('Location: ../auth.html?error=db_error');
    exit;
}
