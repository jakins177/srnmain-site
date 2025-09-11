<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/update_subscription.log');

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

$amount = intval($_POST['amount'] ?? 0);
$priceId = priceForGasergy($amount);
if ($amount <= 0 || !$priceId) {
    http_response_code(400);
    exit('Invalid plan');
}

log_subscription('requested amount=' . $amount . ' priceId=' . ($priceId ?: 'none'));


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
    $subscription = \Stripe\Subscription::retrieve($subscriptionId);

    log_subscription('Stripe retrieved subscription id=' . $subscriptionId);

    $itemId = $subscription->items->data[0]->id;

    // First, ensure the subscription is not set to be cancelled
    \Stripe\Subscription::update($subscriptionId, [
        'cancel_at_period_end' => false,
    ]);

    // Then, update the price and charge immediately
    \Stripe\Subscription::update($subscriptionId, [
        'items' => [
            ['id' => $itemId, 'price' => $priceId]
        ],
        'proration_behavior' => 'always_invoice',
        'payment_behavior' => 'pending_if_incomplete'
    ]);

    // The webhook will handle all subscription updates, including plan
    // changes. This ensures that gasergy is added only after a successful
    // payment.

} catch (Exception $e) {

    log_subscription('Stripe error updating ' . $subscriptionId . ': ' . $e->getMessage());

    http_response_code(500);
    exit('Stripe error');
}

header('Location: https://billing.stripe.com/p/login/test_00wbJ12SW34q2cK1O10sU00');
exit;
