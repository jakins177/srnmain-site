<?php
session_start();
error_log("create_checkout_session.php: Script started for user: " . ($_SESSION['user_id'] ?? 'Not logged in'));


// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    error_log("create_checkout_session.php: User not logged in. Redirecting to login page.");
    header('Location: ../auth.html?error=login_required');
    exit;
}

// 2. Include necessary files
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php'; // Provides Stripe keys and helper functions

// 3. Get the plan from the POST data
$planId = $_POST['plan'] ?? '';
error_log("create_checkout_session.php: Received plan ID: '{$planId}'");
if (empty($planId)) {
    http_response_code(400);
    error_log("create_checkout_session.php: Error: Plan ID is missing.");
    exit('Error: Plan ID is missing.');
}

// 4. Get the Price ID and Gasergy amount for the selected plan
$priceId = getPriceIdForPlan($planId);
$gasergyAmount = getGasergyForPlan($planId);
error_log("create_checkout_session.php: Plan '{$planId}' maps to Price ID '{$priceId}' and Gasergy Amount '{$gasergyAmount}'");


if (!$priceId || $gasergyAmount <= 0) {
    http_response_code(400);
    error_log("create_checkout_session.php: Error: Invalid plan selected or plan not found in config. Plan ID: '{$planId}'");
    exit('Error: Invalid plan selected.');
}

// 5. Set up Stripe API key
\Stripe\Stripe::setApiKey($stripeSecretKey);

// 6. Define the base URL for success/cancel URLs
// In a real app, you might want a more robust way to determine this.
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$baseUrl = "{$protocol}://{$host}";

try {
    // 7. Create a new Stripe Checkout Session
    error_log("create_checkout_session.php: Attempting to create Stripe Checkout session for user " . $_SESSION['user_id']);
    $checkout_session = \Stripe\Checkout\Session::create([
        'mode' => 'subscription',
        'line_items' => [[
            'price' => $priceId,
            'quantity' => 1,
        ]],
        'success_url' => $baseUrl . '/stripe/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => $baseUrl . '/index.html', // Redirect to home page on cancellation
        'client_reference_id' => $_SESSION['user_id'], // Link the session to the logged-in user
        'metadata' => [
            'gasergy_amount' => $gasergyAmount // Pass gasergy amount for webhook and success page
        ],
    ]);

    // 8. Redirect the user to the Stripe Checkout page
    error_log("create_checkout_session.php: Successfully created session. Redirecting to: " . $checkout_session->url);
    header('Location: ' . $checkout_session->url, true, 303);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    // Handle Stripe API errors
    http_response_code(500);
    error_log("create_checkout_session.php: Stripe API Error: " . $e->getMessage());
    echo 'Error creating checkout session: ' . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    // Handle other errors
    http_response_code(500);
    error_log("create_checkout_session.php: Unexpected Error: " . $e->getMessage());
    echo 'An unexpected error occurred.';
}
