<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: ../auth.html?error=empty_fields');
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ? AND provider = 'local'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {

        // Password is correct, start the session
        $_SESSION['user_id'] = $user['id'];

        // Redirect to the home page
        header('Location: ../index.html');
        exit;
    } else {
        // Invalid credentials
        header('Location: ../auth.html?error=invalid_credentials');
        exit;
    }
} catch (PDOException $e) {
    // Database error
    header('Location: ../auth.html?error=db_error');
    exit;
}
