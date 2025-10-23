<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth.html?notice=login_required');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/settings.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/logging.php';

function redirect_with_feedback(string $type, string $message): void {
    $_SESSION['settings_feedback'] = [
        'type' => $type,
        'message' => $message,
    ];
    header('Location: ../pages/settings.php');
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!$csrf_token || !isset($_SESSION['settings_csrf']) || !hash_equals($_SESSION['settings_csrf'], $csrf_token)) {
    redirect_with_feedback('error', 'Your session expired. Please try updating your password again.');
}

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($current_password === '' || $new_password === '' || $confirm_password === '') {
    redirect_with_feedback('error', 'All password fields are required.');
}

if (strlen($new_password) < 8) {
    redirect_with_feedback('error', 'Please choose a new password that is at least 8 characters long.');
}

if (!hash_equals($new_password, $confirm_password)) {
    redirect_with_feedback('error', 'The new passwords do not match. Please re-enter them.');
}

try {
    $stmt = $pdo->prepare('SELECT password_hash, provider FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        redirect_with_feedback('error', 'We could not find your account. Please contact support.');
    }

    if (($user['provider'] ?? 'local') !== 'local' || empty($user['password_hash'])) {
        redirect_with_feedback('error', 'This account uses a single sign-on provider. Please manage your password through that provider.');
    }

    if (!password_verify($current_password, $user['password_hash'])) {
        redirect_with_feedback('error', 'Your current password was incorrect. Please try again.');
    }

    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

    $update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $update->execute([$new_hash, $_SESSION['user_id']]);

    $_SESSION['settings_csrf'] = bin2hex(random_bytes(32));

    redirect_with_feedback('success', 'Password updated successfully.');
} catch (PDOException $e) {
    custom_log('Password update failed: ' . $e->getMessage(), 'auth.log');
    redirect_with_feedback('error', 'We ran into a database error while updating your password. Please try again later.');
}
