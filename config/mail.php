<?php
require_once __DIR__ . '/config.php';

$recipientsRaw = getenv('CONTACT_NOTIFICATION_RECIPIENTS') ?: '';
$recipients = array_values(array_filter(array_map('trim', explode(',', $recipientsRaw))));

return [
    'host' => getenv('SMTP_HOST') ?: '',
    'port' => getenv('SMTP_PORT') ?: '',
    'username' => getenv('SMTP_USER') ?: '',
    'password' => getenv('SMTP_PASS') ?: '',
    'encryption' => getenv('SMTP_ENCRYPTION') ?: '',
    'from_address' => getenv('SMTP_FROM_ADDRESS') ?: (getenv('SMTP_USER') ?: ''),
    'from_name' => getenv('SMTP_FROM_NAME') ?: 'SRN Notifications',
    'reply_to_override' => getenv('CONTACT_REPLY_TO') ?: '',
    'notification_recipients' => $recipients,
];
