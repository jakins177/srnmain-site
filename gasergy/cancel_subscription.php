<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/cancel_subscription.log');

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../auth-system/config/db.php';

function log_subscription($msg) {
    error_log($msg);
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT stripe_subscription_id FROM users WHERE id = ?");
$stmt->execute([$userId]);
$subscriptionId = $stmt->fetchColumn();

log_subscription('db subscription id=' . ($subscriptionId ?: 'none') . ' for user=' . $userId);

if (!$subscriptionId) {
    http_response_code(400);
    exit('No active subscription');
}

\Stripe\Stripe::setApiKey($stripeSecretKey);
try {
    \Stripe\Subscription::update($subscriptionId, ['cancel_at_period_end' => true]);

    log_subscription('set cancel_at_period_end for ' . $subscriptionId);
} catch (Exception $e) {
    log_subscription('Stripe error canceling ' . $subscriptionId . ': ' . $e->getMessage());

    http_response_code(500);
    exit('Stripe error');
}

header('Location: https://billing.stripe.com/p/login/test_00wbJ12SW34q2cK1O10sU00');
exit;
