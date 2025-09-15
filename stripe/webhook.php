<?php
// For debugging webhook issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/webhook.log');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/stripe.php';

$logFile = __DIR__ . '/webhook.log';
$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// Verify the webhook signature
try {
    $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $stripeWebhookSecret);
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    error_log('Invalid payload: ' . $e->getMessage());
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    error_log('Invalid signature: ' . $e->getMessage());
    exit();
}

file_put_contents($logFile, "Received event: " . $event->type . "\n", FILE_APPEND);

// Handle the event
switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;
        $userId = $session->client_reference_id;
        $gasergyAmount = intval($session->metadata->gasergy_amount ?? 0);
        $subscriptionId = $session->subscription;
        $customerId = $session->customer;

        if ($userId && $gasergyAmount > 0) {
            try {
                // This is the first payment for a new subscription.
                // We set the balance, customer ID, subscription ID, and the amount of gasergy this subscription provides.
                $stmt = $pdo->prepare(
                    "UPDATE users SET gasergy_balance = gasergy_balance + ?, " .
                    "stripe_customer_id = ?, stripe_subscription_id = ?, subscription_gasergy = ? " .
                    "WHERE id = ?"
                );
                $stmt->execute([$gasergyAmount, $customerId, $subscriptionId, $gasergyAmount, $userId]);
                file_put_contents($logFile, "SUCCESS: checkout.session.completed for user $userId. Credited $gasergyAmount gasergy.\n", FILE_APPEND);
            } catch (PDOException $e) {
                http_response_code(500);
                error_log('DATABASE ERROR on checkout.session.completed: ' . $e->getMessage());
            }
        }
        break;

    case 'invoice.payment_succeeded':
        $invoice = $event->data->object;
        $customerId = $invoice->customer;
        $billingReason = $invoice->billing_reason ?? '';

        // Only handle recurring subscription payments here. Initial payments are handled by checkout.session.completed.
        if ($billingReason === 'subscription_cycle' || $billingReason === 'subscription_update') {
            $userId = null;
            $gasergyAmount = 0;

            // Find the user by their Stripe Customer ID
            if ($customerId) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE stripe_customer_id = ?");
                $stmt->execute([$customerId]);
                $user = $stmt->fetch();
                if ($user) {
                    $userId = $user['id'];
                }
            }

            // Determine the gasergy amount from the invoice's price ID
            if (isset($invoice->lines->data[0]->price->id)) {
                $priceId = $invoice->lines->data[0]->price->id;
                $gasergyAmount = getGasergyForPriceId($priceId); // Using our helper function
            }

            if ($userId && $gasergyAmount > 0) {
                try {
                    // This is a renewal or plan change.
                    // We add the gasergy for the new period and update the record of how much gasergy the subscription provides.
                    $stmt = $pdo->prepare(
                        "UPDATE users SET gasergy_balance = gasergy_balance + ?, subscription_gasergy = ? WHERE id = ?"
                    );
                    $stmt->execute([$gasergyAmount, $gasergyAmount, $userId]);
                    file_put_contents($logFile, "SUCCESS: invoice.payment_succeeded for user $userId. Credited $gasergyAmount gasergy for reason: $billingReason.\n", FILE_APPEND);
                } catch (PDOException $e) {
                    http_response_code(500);
                    error_log('DATABASE ERROR on invoice.payment_succeeded: ' . $e->getMessage());
                }
            }
        }
        break;

    case 'customer.subscription.deleted':
        $subscription = $event->data->object;
        $subscriptionId = $subscription->id;
        try {
            // The subscription was canceled. We null out the subscription fields.
            // We do not remove their remaining gasergy balance.
            $stmt = $pdo->prepare(
                "UPDATE users SET stripe_subscription_id = NULL, subscription_gasergy = NULL WHERE stripe_subscription_id = ?"
            );
            $stmt->execute([$subscriptionId]);
            file_put_contents($logFile, "SUCCESS: customer.subscription.deleted for subscription $subscriptionId.\n", FILE_APPEND);
        } catch (PDOException $e) {
            http_response_code(500);
            error_log('DATABASE ERROR on customer.subscription.deleted: ' . $e->getMessage());
        }
        break;

    case 'invoice.payment_failed':
        $invoice = $event->data->object;
        error_log("Payment failed for invoice {$invoice->id}, customer {$invoice->customer}.");
        // Optionally, implement logic to notify user or pause service.
        break;

    default:
        file_put_contents($logFile, "INFO: Unhandled event type '{$event->type}'.\n", FILE_APPEND);
}

http_response_code(200);
