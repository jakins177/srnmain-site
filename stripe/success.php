<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';

$gasergyAmount = 0;
$sessionId = $_GET['session_id'] ?? '';

if ($sessionId) {
    \Stripe\Stripe::setApiKey($stripeSecretKey);
    try {
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        // Use the metadata we set during checkout creation
        $gasergyAmount = $session->metadata->gasergy_amount ?? 0;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Error retrieving session. In a real app, log this.
        // For now, we'll just show a generic success message.
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SRN • Purchase Successful</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <nav class="nav">
    <div class="container nav-inner">
      <div class="brand">
        <div class="logo" aria-hidden="true"></div>
        <div>SRN</div>
      </div>
    </div>
  </nav>

  <main class="container" style="max-width: 600px; margin-top: 40px; text-align: center;">
    <div class="hero-card">
      <h1><span class="gradient-text">Payment Successful!</span></h1>
      <?php if ($gasergyAmount > 0) : ?>
        <p class="lead">
          Your account has been credited with <strong><?php echo htmlspecialchars(number_format($gasergyAmount)); ?></strong> Gasergy.
        </p>
        <p class="subtle">Note: Your balance will update upon confirmation from Stripe's webhook, which is usually instant.</p>
      <?php else : ?>
        <p class="lead">Thank you for your purchase.</p>
      <?php endif; ?>
      <div class="cta-row" style="justify-content: center; margin-top: 20px;">
        <a href="../index.html" class="btn btn-primary">Back to Home</a>
      </div>
    </div>
  </main>

</body>
</html>
