<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/create_checkout_session.log');

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';

function log_checkout($msg) {
    error_log($msg);
}

$baseUrl = rtrim(getenv('BASE_URL') ?: '', '/');

log_checkout('start user=' . ($_SESSION['user_id'] ?? 'none') . ' amount=' . ($_POST['amount'] ?? ''));

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$amount = intval($_POST['amount'] ?? 0);
$priceId = priceForGasergy($amount);
if ($amount <= 0 || !$priceId) {
    log_checkout("invalid amount " . $amount . " resolved priceId: " . ($priceId ?: 'null'));
    http_response_code(400);
    exit('Invalid amount');
}

log_checkout("Attempting Stripe session creation with priceId: " . $priceId . " for amount: " . $amount);
\Stripe\Stripe::setApiKey($stripeSecretKey);

try {
    $checkout_session_payload = [
        'mode' => 'subscription',
        'line_items' => [[
            'price' => $priceId,
            'quantity' => 1,
        ]],
        'success_url' => ($baseUrl ?: '') . '/gasergy/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => ($baseUrl ?: '') . '/gasergy/get.php',
        'client_reference_id' => $_SESSION['user_id'],
        'metadata' => ['amount' => $amount],
    ];
    log_checkout("Stripe session payload: " . json_encode($checkout_session_payload));
    $session = \Stripe\Checkout\Session::create([
        'mode' => 'subscription',
        'line_items' => [[
            'price'    => $priceId,
            'quantity' => 1,
        ]],
        'success_url'        => ($baseUrl ?: '') . '/gasergy/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'         => ($baseUrl ?: '') . '/gasergy/get.php',
        'client_reference_id'=> $_SESSION['user_id'],
        'metadata'           => ['amount' => $amount],
    ]);
    
    header('Location: ' . $session->url, true, 303);
    log_checkout("session created " . $session->id . " for user " . $_SESSION['user_id']);
    exit;
} catch (\Stripe\Exception\ApiErrorException $e) {
    log_checkout("Stripe API error: " . $e->getMessage() . " - Full exception: " . json_encode($e->getJsonBody()));
    http_response_code(500);
    // Optionally, provide a more user-friendly error message or a generic one
    echo 'Error creating checkout session due to Stripe API issue.';
} catch (Exception $e) {
    log_checkout("Generic error: " . $e->getMessage() . " - Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo 'Error creating checkout session';
}
