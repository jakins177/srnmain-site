<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/confirm_upgrade.log');

error_log("confirm_upgrade.php started");

session_start();
require_once __DIR__ . '/../auth-system/login_check.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../auth-system/config/db.php';

function log_subscription($msg) {
    error_log($msg);
}

$amount = intval($_POST['amount'] ?? 0);
$priceId = priceForGasergy($amount);
if ($amount <= 0 || !$priceId) {
    http_response_code(400);
    exit('Invalid plan');
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT stripe_subscription_id FROM users WHERE id = ?");
$stmt->execute([$userId]);
$subscriptionId = $stmt->fetchColumn();

if (!$subscriptionId) {
    http_response_code(400);
    exit('No active subscription');
}

\Stripe\Stripe::setApiKey($stripeSecretKey);
try {
    log_subscription('Retrieving subscription with ID: ' . $subscriptionId);
    var_dump($subscriptionId);
    $subscription = \Stripe\Subscription::retrieve($subscriptionId);
    log_subscription('Retrieved subscription successfully.');

    log_subscription('retrieved subscription id=' . $subscriptionId);

    // Check if subscription items are empty
    if (empty($subscription->items->data)) {
        log_subscription('Subscription has no items.');
        http_response_code(400);
        exit('Subscription has no items.');
    }

    $itemId = $subscription->items->data[0]->id;
    // Estimate proration cost using upcoming invoice
    $invoice = \Stripe\Invoice::createPreview([
        'customer' => $subscription->customer,
        'subscription' => $subscriptionId,
        'subscription_details' => [
            'items' => [
                ['id' => $itemId, 'price' => $priceId]
            ],
            'proration_behavior' => 'always_invoice',
        ],
    ]);
    $amountDue = $invoice->amount_due / 100; // convert from cents

    log_subscription('upcoming invoice amount=' . $amountDue);
} catch (Exception $e) {
    log_subscription('Stripe error in confirm_upgrade: ' . $e->getMessage());
    http_response_code(500);
    exit('Stripe error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Upgrade</title>
</head>
<body>
<h1>Confirm Plan Change</h1>
<p>You are upgrading to <?php echo htmlspecialchars(number_format($amount)); ?> Gasergy/month.</p>
<p>This upgrade will charge your saved payment method <strong>$<?php echo number_format($amountDue, 2); ?></strong> immediately for the prorated difference.</p>
<form action="update_subscription.php" method="POST" style="display:inline-block;margin-right:1em;">
    <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
    <button type="submit">Confirm Charge</button>
</form>
<button type="button" style="display:inline-block;" onclick="window.location.href='https://billing.stripe.com/p/login/test_00wbJ12SW34q2cK1O10sU00';">Cancel</button>
</body>
</html>
