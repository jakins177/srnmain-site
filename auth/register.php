<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/logging.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    custom_log("Registration failed: Invalid input for email '{$email}'.", 'auth.log');
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
    custom_log("Registration DB Error (checking user existence): " . $e->getMessage(), 'auth.log');
    header('Location: ../auth.html?error=db_error');
    exit;
}

// Hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user
try {

    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, gasergy_balance) VALUES (?, ?, 120)");

    $stmt->execute([$email, $passwordHash]);

    header('Location: ../auth.html?success=registered#login-form');
    exit;
} catch (PDOException $e) {
    custom_log("Registration DB Error (inserting new user): " . $e->getMessage(), 'auth.log');
    header('Location: ../auth.html?error=db_error');
    exit;
}
