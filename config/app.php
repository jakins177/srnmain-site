<?php
require_once __DIR__ . '/config.php';

return [
    'billing_portal_url' => getenv('STRIPE_BILLING_PORTAL_URL') ?: '',
];
