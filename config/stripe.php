<?php
require_once __DIR__ . '/config.php';

$stripeSecretKey = getenv('STRIPE_SECRET_KEY');
$stripePublishableKey = getenv('STRIPE_PUBLISHABLE_KEY');
$stripeWebhookSecret = getenv('STRIPE_WEBHOOK_SECRET');

// This mapping connects the plan identifiers from the frontend (e.g., 'starter-monthly')
// to your Stripe Price IDs and the amount of gasergy the plan provides.
// IMPORTANT: The price IDs below are for testing purposes only.
// Replace them with your actual Stripe Price IDs from your Stripe dashboard.
$plans = [
    'starter-monthly' => [
        'price_id' => 'price_1PFDNEP9x3d2R65y5rA4M3l6',
        'gasergy' => 500
    ],
    'professional-monthly' => [
        'price_id' => 'price_1PFDNEP9x3d2R65y5rA4M3l6',
        'gasergy' => 2500
    ],
    'business-monthly' => [
        'price_id' => 'price_1PFDNEP9x3d2R65y5rA4M3l6',
        'gasergy' => 10000
    ],
    'enterprise-monthly' => [
        'price_id' => 'price_1PFDNEP9x3d2R65y5rA4M3l6',
        'gasergy' => 50000
    ],
    'starter-annual' => [
        'price_id' => 'price_1PFDNEP9x3d2R65y5rA4M3l6',
        'gasergy' => 5000
    ],
    'professional-annual' => [
        'price_id' => 'price_1PFDNEP9x3d2R65y5rA4M3l6',
        'gasergy' => 20500
    ],
    'business-annual' => [
        'price_id' => 'price_1PFDNEP9x3d2R65y5rA4M3l6',
        'gasergy' => 100000
    ],
    'enterprise-annual' => [
        'price_id' => 'price_1PFDNEP9x3d2R65y5rA4M3l6',
        'gasergy' => 500000
    ],
];

/**
 * Gets the Stripe Price ID for a given plan identifier.
 * @param string $planId e.g., 'starter-monthly'
 * @return string|null The Stripe Price ID or null if not found.
 */
function getPriceIdForPlan($planId) {
    global $plans;
    return $plans[$planId]['price_id'] ?? null;
}

/**
 * Gets the amount of gasergy for a given Stripe Price ID.
 * Used by the webhook to credit the user's account upon successful payment.
 * @param string $priceId The Stripe Price ID
 * @return int The amount of gasergy, or 0 if not found.
 */
function getGasergyForPriceId($priceId) {
    global $plans;
    foreach ($plans as $plan) {
        if ($plan['price_id'] === $priceId) {
            return $plan['gasergy'];
        }
    }
    return 0;
}

/**
 * Gets the amount of gasergy for a given plan identifier.
 * @param string $planId e.g., 'starter-monthly'
 * @return int The amount of gasergy, or 0 if not found.
 */
function getGasergyForPlan($planId) {
    global $plans;
    return $plans[$planId]['gasergy'] ?? 0;
}
