<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/success.log');

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);
$sessionId = $_GET['session_id'] ?? '';
$amount = '';
$amountFormatted = '';
if ($sessionId) {
    try {
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        $amount = $session->metadata->amount ?? '';
        $amountFormatted = number_format((int)$amount);
    } catch (Exception $e) {
        // ignore errors, just show generic success
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gasergy Purchase Success</title>
</head>
<body>
  <h1>Payment Successful!</h1>
<?php if ($amount) : ?>
  <p>You purchased <?php echo htmlspecialchars($amountFormatted); ?> Gasergy.</p>
<?php else : ?>
  <p>Thank you for your purchase.</p>
<?php endif; ?>
  <p><a href="get.php">Back to Get Gasergy</a></p>
</body>
</html>
