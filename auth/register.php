<?php
require_once __DIR__ . '/../config/db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Basic validation failed
    header('Location: ../auth.html?error=invalid_input');
    exit;
}

// Check if user already exists
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        // User already exists
        header('Location: ../auth.html?error=user_exists');
        exit;
    }
} catch (PDOException $e) {
    // Database error
    header('Location: ../auth.html?error=db_error');
    exit;
}

// Hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user
try {
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $passwordHash]);

    // Redirect to login page with a success message
    header('Location: ../auth.html?success=registered');
    exit;
} catch (PDOException $e) {
    // Database error
    header('Location: ../auth.html?error=db_error');
    exit;
}
