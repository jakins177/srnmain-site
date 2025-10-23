<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);

if (!$is_logged_in) {
    header('Location: ../auth.html?notice=login_required');
    exit;
}
$gasergy_balance = 0;

require_once __DIR__ . '/../config/db.php';
$config = require __DIR__ . '/../config/app.php';
$billing_portal_url = $config['billing_portal_url'] ?? '';

try {
    $stmt = $pdo->prepare('SELECT gasergy_balance FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $gasergy_balance = (int) $user['gasergy_balance'];
    }
} catch (PDOException $e) {
    // Silently fail; balance will remain 0.
}

if (!isset($_SESSION['settings_csrf'])) {
    $_SESSION['settings_csrf'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['settings_csrf'];

$feedback = $_SESSION['settings_feedback'] ?? null;
if ($feedback) {
    unset($_SESSION['settings_feedback']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Account Settings • SRN</title>
  <meta name="description" content="Manage your SRN account settings, update your password, and access the billing portal.">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body data-base-path="../">
  <nav class="nav">
    <div class="container nav-inner">
      <a href="../index.php" class="brand">
        <div class="logo" aria-hidden="true"></div>
        <div>
          <div>SRN</div>
          <small style="color:var(--muted); font-weight:600">Palm Trees AI Software Robot Network</small>
        </div>
      </a>
      <div class="nav-links">
        <a href="../index.php#bots">Bots</a>
        <a href="buy-gasergy.php">Credits</a>
        <a href="../index.php#builder">Custom Persona</a>
        <a href="buy-gasergy.php#faq">FAQ</a>
        <a href="contact.php">Contact</a>
        <a href="settings.php" aria-current="page">Settings</a>
        <span class="chip">Balance: <?php echo number_format($gasergy_balance); ?> G</span>
        <a href="../auth/logout.php" class="btn btn-ghost">Log Out</a>
      </div>
    </div>
  </nav>

  <main class="section container" style="padding-top:4rem; padding-bottom:4rem;">
    <header class="settings-header">
      <span class="eyebrow">Account</span>
      <h1 style="margin: 0;">Manage your settings</h1>
      <p class="subtle" style="max-width: 600px;">Update your password and manage your subscription from a single place. Changes apply across the entire SRN network.</p>
    </header>

    <?php if (!empty($feedback) && !empty($feedback['message'])): ?>
      <div class="message <?php echo $feedback['type'] === 'success' ? 'success' : 'error'; ?>" role="status">
        <?php echo htmlspecialchars($feedback['message'], ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <section class="settings-grid">
      <article class="hero-card settings-card" aria-labelledby="changePasswordTitle">
        <div>
          <h2 id="changePasswordTitle" style="margin:0;">Change password</h2>
          <p class="subtle">Set a new password for your SRN account. For security, you&rsquo;ll need your current password.</p>
        </div>
        <form action="../auth/change_password.php" method="POST" class="settings-form">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
          <label>
            Current password
            <input type="password" name="current_password" autocomplete="current-password" required>
          </label>
          <label>
            New password
            <input type="password" name="new_password" autocomplete="new-password" minlength="8" required>
          </label>
          <label>
            Confirm new password
            <input type="password" name="confirm_password" autocomplete="new-password" minlength="8" required>
          </label>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update password</button>
            <span class="subtle">Passwords must be at least 8 characters.</span>
          </div>
        </form>
      </article>

      <article class="hero-card settings-card" aria-labelledby="manageSubscriptionTitle">
        <div>
          <h2 id="manageSubscriptionTitle" style="margin:0;">Manage subscription</h2>
          <p class="subtle">Open the secure Stripe billing portal to update your plan, payment method, or download invoices.</p>
        </div>
        <?php if ($billing_portal_url): ?>
          <a class="btn btn-primary" href="<?php echo htmlspecialchars($billing_portal_url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Open billing portal</a>
          <p class="subtle">The portal opens in a new tab provided by Stripe.</p>
        <?php else: ?>
          <div class="message" style="margin:0;">The billing portal link is not configured. Please contact support for subscription changes.</div>
        <?php endif; ?>
      </article>
    </section>
  </main>

  <footer class="container">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap">
      <div>© <span id="year"></span> SRN — Software Robot Network</div>
      <div style="display:flex; gap:14px">
        <a href="policies.php">Terms</a>
        <a href="policies.php">Privacy</a>
        <a href="contact.php">Contact</a>
      </div>
    </div>
  </footer>

  <script>
    window.isUserLoggedIn = <?php echo json_encode($is_logged_in); ?>;
  </script>
  <script type="module" src="../assets/js/app.js"></script>
</body>
</html>
