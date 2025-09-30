<?php
require_once __DIR__ . '/../config/logging.php';

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/stripe.php';

$webhookLogFile = 'stripe_webhook.log';

/**
 * Cache for table column lookups so we only ask MySQL once per request.
 *
 * @var array<string, array<string, bool>>
 */
$tableColumnCache = [];

/**
 * Checks whether the provided table contains the specified column.
 *
 * @param PDO    $pdo
 * @param string $table
 * @param string $column
 * @return bool
 */
function tableHasColumn(PDO $pdo, string $table, string $column): bool
{
    global $tableColumnCache, $webhookLogFile;

    if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $column)) {
        custom_log("tableHasColumn called with invalid identifiers: table={$table}, column={$column}.", $webhookLogFile);
        return false;
    }

    if (isset($tableColumnCache[$table][$column])) {
        return $tableColumnCache[$table][$column];
    }

    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        $tableColumnCache[$table][$column] = $exists;
        return $exists;
    } catch (PDOException $e) {
        custom_log('Failed to determine if column exists: ' . $e->getMessage(), $webhookLogFile);
        return false;
    }
}

try {
    $payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// Verify the webhook signature
try {
    $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $stripeWebhookSecret);
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    custom_log('Invalid payload: ' . $e->getMessage(), $webhookLogFile);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    custom_log('Invalid signature: ' . $e->getMessage(), $webhookLogFile);
    exit();
}

// Extract app_id from metadata
$app_id = null;
if (isset($event->data->object->metadata->app_id)) {
    $app_id = $event->data->object->metadata->app_id;
} else if ($event->type === 'invoice.payment_succeeded' || $event->type === 'invoice.payment_failed') {
    // For invoice events, we might need to retrieve the subscription to get the metadata
    $invoice = $event->data->object;
    if (isset($invoice->subscription)) {
        try {
            $subscription = \Stripe\Subscription::retrieve($invoice->subscription);
            if (isset($subscription->metadata->app_id)) {
                $app_id = $subscription->metadata->app_id;
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            custom_log('Error retrieving subscription: ' . $e->getMessage(), $webhookLogFile);
        }
    }
}

custom_log("Received event: " . $event->type, $webhookLogFile);

// Handle the event
switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;
        $userId = $session->client_reference_id;
        $gasergyAmount = intval($session->metadata->gasergy_amount ?? 0);
        if ($gasergyAmount <= 0) {
            custom_log("checkout.session.completed missing gasergy_amount metadata for user {$userId}.", $webhookLogFile);
        }
        $subscriptionId = $session->subscription;
        $customerId = $session->customer;

        if ($userId && $gasergyAmount > 0) {
            try {
                // This is the first payment for a new subscription.
                // We set the balance, customer ID, subscription ID, and the amount of gasergy this subscription provides.
                $setClauses = [
                    'gasergy_balance = COALESCE(gasergy_balance, 0) + ?',
                    'stripe_customer_id = ?',
                    'stripe_subscription_id = ?',
                    'subscription_gasergy = ?'
                ];
                $params = [$gasergyAmount, $customerId, $subscriptionId, $gasergyAmount];

                if ($app_id !== null && tableHasColumn($pdo, 'users', 'app_id')) {
                    $setClauses[] = 'app_id = ?';
                    $params[] = $app_id;
                } elseif ($app_id !== null) {
                    custom_log("checkout.session.completed received app_id '{$app_id}' but users table has no app_id column.", $webhookLogFile);
                }

                $params[] = $userId;

                $stmt = $pdo->prepare(
                    'UPDATE users SET ' . implode(', ', $setClauses) . ' WHERE id = ?'
                );
                $stmt->execute($params);
                custom_log("SUCCESS: checkout.session.completed for user $userId. Credited $gasergyAmount gasergy.", $webhookLogFile);
            } catch (PDOException $e) {
                http_response_code(500);
                custom_log('DATABASE ERROR on checkout.session.completed: ' . $e->getMessage(), $webhookLogFile);
            }
        } else {
            custom_log("checkout.session.completed skipped update. userId={$userId}, gasergyAmount={$gasergyAmount}.", $webhookLogFile);
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
                } else {
                    custom_log("invoice.payment_succeeded: No user found for customer {$customerId}.", $webhookLogFile);
                }
            } else {
                custom_log('invoice.payment_succeeded missing customer ID.', $webhookLogFile);
            }

            // Determine the gasergy amount from the invoice's price ID
            if (isset($invoice->lines->data[0]->price->id)) {
                $priceId = $invoice->lines->data[0]->price->id;
                $gasergyAmount = getGasergyForPriceId($priceId); // Using our helper function
                if ($gasergyAmount <= 0) {
                    custom_log("invoice.payment_succeeded: Unable to map price ID {$priceId} to gasergy.", $webhookLogFile);
                }
            } else {
                custom_log('invoice.payment_succeeded missing price ID in invoice line.', $webhookLogFile);
            }

            if ($userId && $gasergyAmount > 0) {
                try {
                    // This is a renewal or plan change.
                    // We add the gasergy for the new period and update the record of how much gasergy the subscription provides.
                    $stmt = $pdo->prepare(
                        "UPDATE users SET gasergy_balance = COALESCE(gasergy_balance, 0) + ?, subscription_gasergy = ? WHERE id = ?"
                    );
                    $stmt->execute([$gasergyAmount, $gasergyAmount, $userId]);
                    custom_log("SUCCESS: invoice.payment_succeeded for user $userId. Credited $gasergyAmount gasergy for reason: $billingReason.", $webhookLogFile);
                } catch (PDOException $e) {
                    http_response_code(500);
                    custom_log('DATABASE ERROR on invoice.payment_succeeded: ' . $e->getMessage(), $webhookLogFile);
                }
            } else {
                custom_log("invoice.payment_succeeded skipped update. userId={$userId}, gasergyAmount={$gasergyAmount}.", $webhookLogFile);
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
            custom_log("SUCCESS: customer.subscription.deleted for subscription $subscriptionId.", $webhookLogFile);
        } catch (PDOException $e) {
            http_response_code(500);
            custom_log('DATABASE ERROR on customer.subscription.deleted: ' . $e->getMessage(), $webhookLogFile);
        }
        break;

    case 'invoice.payment_failed':
        $invoice = $event->data->object;
        custom_log("Payment failed for invoice {$invoice->id}, customer {$invoice->customer}.", $webhookLogFile);
        // Optionally, implement logic to notify user or pause service.
        break;

    default:
        custom_log("INFO: Unhandled event type '{$event->type}'.", $webhookLogFile);
}

http_response_code(200);
} catch (\Throwable $e) {
    http_response_code(500);
    custom_log("FATAL Error: " . $e->getMessage() . "\n" . $e->getTraceAsString(), $webhookLogFile);
    // We can exit here, as the response code has been set and the error logged.
    exit;
}
