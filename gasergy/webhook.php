<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/webhook.log');

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/../auth-system/config/db.php';
require_once __DIR__ . '/../config/stripe.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);

$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        $stripeWebhookSecret
    );
} catch (Exception $e) {
    http_response_code(400);
    exit('Invalid signature');
}

// At the top, define the log file
$logFile = __DIR__ . '/webhook.log';
file_put_contents($logFile, date('c') . " webhook received\n", FILE_APPEND);

switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;
        $userId = $session->client_reference_id;
        $amount = intval($session->metadata->amount ?? 0);
        $subscriptionId = $session->subscription;
        $customerId = $session->customer;

        if ($userId && $amount > 0) {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE users SET gasergy_balance = gasergy_balance + ?, " .
                    "stripe_subscription_id = ?, subscription_gasergy = ?, stripe_customer_id = ? " .
                    "WHERE id = ?"
                );
                $stmt->execute([$amount, $subscriptionId, $amount, $customerId, $userId]);
            } catch (Exception $e) {
                // log error in real setup
            }
        }

        file_put_contents(
            $logFile,
            "checkout.session.completed: user={$session->client_reference_id} " .
            "amount={$session->metadata->amount} subscription={$subscriptionId}\n",
            FILE_APPEND
        );
        break;
    case 'invoice.payment_succeeded':
        // handle subscription invoices
        $invoice = $event->data->object;
        $customerId = $invoice->customer;
        $billingReason = $invoice->billing_reason ?? '';

        $userId = null;
        $subscriptionId = null;
        $gasergyAmount = 0;

        if ($customerId) {
            $stmt = $pdo->prepare("SELECT id, stripe_subscription_id FROM users WHERE stripe_customer_id = ?");
            $stmt->execute([$customerId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $userId = $user['id'];
                $subscriptionId = $user['stripe_subscription_id'];
            }
        }

        // Determine the plan from the invoice lines
        foreach ($invoice->lines->data as $line) {
            if (($line->type ?? '') === 'subscription' && isset($line->price->id)) {
                $gasergyAmount = gasergyForPrice($line->price->id) ?? 0;
                break;
            }
        }

        if ($gasergyAmount === 0 && $subscriptionId) {
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);
            $priceId = $subscription->items->data[0]->price->id ?? null;
            $gasergyAmount = $priceId ? gasergyForPrice($priceId) : 0;
        }

        if ($userId && $gasergyAmount > 0) {
            if (in_array($billingReason, ['subscription_cycle', 'subscription_update'], true)) {
                // Add credits and record plan size for new subscription, renewal, or upgrade
                $stmt = $pdo->prepare(
                    "UPDATE users SET gasergy_balance = gasergy_balance + ?, subscription_gasergy = ? WHERE id = ?"
                );
                $stmt->execute([$gasergyAmount, $gasergyAmount, $userId]);

                if ($billingReason === 'subscription_update') {
                    file_put_contents(
                        $logFile,
                        "subscription upgrade applied: subscription={$subscriptionId} user={$userId} gasergy={$gasergyAmount}\n",
                        FILE_APPEND
                    );
                }
            }
        }

        file_put_contents(
            $logFile,
            "invoice.payment_succeeded: subscription={$subscriptionId} user=" . ($userId ?: 'n/a') . " gasergy={$gasergyAmount} reason={$billingReason}\n",
            FILE_APPEND
        );
        break;
    case 'invoice.payment_failed':
        $invoice = $event->data->object;
        file_put_contents(
            $logFile,
            "invoice.payment_failed: subscription={$invoice->subscription} " .
            "customer={$invoice->customer}\n",
            FILE_APPEND
        );
        // notify the user or pause benefits
        break;

    case 'invoice.created':
        // just log for now
        $invoice = $event->data->object;
        $subscriptionId = $invoice->subscription ?? 'n/a';
        file_put_contents(
            $logFile,
            "invoice.created: subscription={$subscriptionId} " .
            "customer={$invoice->customer} reason={$invoice->billing_reason}\n",
            FILE_APPEND
        );
        break;

    case 'customer.subscription.updated':
        $subscription = $event->data->object;
        $subscriptionId = $subscription->id;
        $priceId = $subscription->items->data[0]->price->id ?? null;
        $gasergyAmount = $priceId ? gasergyForPrice($priceId) : 0;
        if ($gasergyAmount > 0) {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE users SET subscription_gasergy = ? WHERE stripe_subscription_id = ?"
                );
                $stmt->execute([$gasergyAmount, $subscriptionId]);
            } catch (Exception $e) {
                // log error in real setup
            }
        }
        file_put_contents(
            $logFile,
            "customer.subscription.updated: subscription={$subscriptionId} gasergy={$gasergyAmount}\n",
            FILE_APPEND
        );
        break;

    case 'customer.subscription.deleted':
        $subscription = $event->data->object;
        $subscriptionId = $subscription->id;
        try {
            $stmt = $pdo->prepare(
                "UPDATE users SET stripe_subscription_id = NULL, subscription_gasergy = NULL WHERE stripe_subscription_id = ?"
            );
            $stmt->execute([$subscriptionId]);
        } catch (Exception $e) {
            // log error in real setup
        }
        file_put_contents(
            $logFile,
            "customer.subscription.deleted: subscription={$subscriptionId}\n",
            FILE_APPEND
        );
        break;

    case 'payment_method.attached':
    case 'payment_method.detached':
        $pm = $event->data->object;
        file_put_contents(
            $logFile,
            "{$event->type}: customer={$pm->customer} payment_method={$pm->id}\n",
            FILE_APPEND
        );
        break;

    case 'customer.updated':
        $customer = $event->data->object;
        $defaultPm = $customer->invoice_settings->default_payment_method ?? '';
        file_put_contents(
            $logFile,
            "customer.updated: customer={$customer->id} default_pm={$defaultPm}\n",
            FILE_APPEND
        );
        break;

    default:
        file_put_contents(
            $logFile,
            "Unhandled event {$event->type}\n",
            FILE_APPEND
        );
}

http_response_code(200);
