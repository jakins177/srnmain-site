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
    $appIdColumnExists = false;

    try {
        $columnCheckStmt = $pdo->prepare("SHOW COLUMNS FROM `users` LIKE 'app_id'");
        $columnCheckStmt->execute();
        $appIdColumnExists = $columnCheckStmt->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (PDOException $e) {
        custom_log("Registration DB Warning (checking app_id column): " . $e->getMessage(), 'auth.log');
    }

    $columns = ['email', 'password_hash', 'gasergy_balance'];
    $placeholders = ['?', '?', '?'];
    $values = [$email, $passwordHash, 120];

    if ($appIdColumnExists) {
        $columns[] = 'app_id';
        $placeholders[] = '?';
        $values[] = 'srn_main_0000';
    }

    $stmt = $pdo->prepare(
        'INSERT INTO users (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')'
    );

    $stmt->execute($values);

    header('Location: ../auth.html?success=registered#login-form');
    exit;
} catch (PDOException $e) {
    custom_log("Registration DB Error (inserting new user): " . $e->getMessage(), 'auth.log');
    header('Location: ../auth.html?error=db_error');
    exit;
}
