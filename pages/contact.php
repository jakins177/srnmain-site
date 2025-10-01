<?php
session_start();

require_once __DIR__ . '/../config/logging.php';

$is_logged_in = isset($_SESSION['user_id']);
$gasergy_balance = 0;
$errors = [];
$success = false;
$name = '';
$email = '';
$message = '';

if (!isset($_SESSION['contact_csrf'])) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['contact_csrf'];

if ($is_logged_in) {
    require_once __DIR__ . '/../config/db.php';
    try {
        $stmt = $pdo->prepare("SELECT gasergy_balance FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $gasergy_balance = $user['gasergy_balance'];
        }
    } catch (PDOException $e) {
        custom_log('Contact page balance lookup failed: ' . $e->getMessage(), 'contact.log');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['contact_csrf'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '') {
            $errors[] = 'Please enter your name so we know who to reply to.';
        } elseif (mb_strlen($name) > 255) {
            $errors[] = 'Your name is a bit long — please keep it under 255 characters.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please share a valid email address.';
        } elseif (mb_strlen($email) > 255) {
            $errors[] = 'Email addresses must be 255 characters or fewer.';
        }

        if ($message === '') {
            $errors[] = 'Let us know how we can help — the message field is required.';
        } elseif (mb_strlen($message) < 10) {
            $errors[] = 'Could you add a bit more detail? A minimum of 10 characters helps us triage.';
        } elseif (mb_strlen($message) > 4000) {
            $errors[] = 'Messages can be up to 4,000 characters long.';
        }

        if (!$errors) {
            if (!isset($pdo)) {
                require_once __DIR__ . '/../config/db.php';
            }

            try {
                $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)');
                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':message' => $message,
                ]);
                $success = true;
                $name = '';
                $email = '';
                $message = '';
                $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
                $csrf_token = $_SESSION['contact_csrf'];
            } catch (PDOException $e) {
                custom_log('Contact form submission failed: ' . $e->getMessage(), 'contact.log');
                $errors[] = 'We ran into an issue saving your message. Please try again shortly or reach out via email.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact SRN • Palm Trees AI Software Robot Network</title>
  <meta name="description" content="Get in touch with the SRN team for product questions, support, or partnership opportunities.">
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
        <?php if ($is_logged_in): ?>
          <span class="chip">Balance: <?php echo number_format($gasergy_balance); ?> G</span>
          <a href="../auth/logout.php" class="btn btn-ghost">Log Out</a>
        <?php else: ?>
          <a href="../auth.html" class="btn btn-ghost">Log In</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main class="section container" style="padding-top: 4rem;">
    <div class="contact-layout">
      <section class="contact-card">
        <header>
          <h1>We’d love to hear from you</h1>
          <p class="subtle">Send us a note about product ideas, partnerships, or anything else on your mind.</p>
        </header>

        <?php if ($success): ?>
          <div class="message success">Thanks! Your message is in our queue and we’ll get back to you shortly.</div>
        <?php endif; ?>

        <?php if ($errors): ?>
          <div class="message error">
            <strong>Please fix the following:</strong>
            <ul style="margin:8px 0 0; padding-left:20px;">
              <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" class="contact-form" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>" />

          <label for="contact-name">Name
            <input id="contact-name" name="name" type="text" required maxlength="255" placeholder="Your name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" />
          </label>

          <label for="contact-email">Email
            <input id="contact-email" name="email" type="email" required maxlength="255" placeholder="you@example.com" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" />
          </label>

          <label for="contact-message">How can we help?
            <textarea id="contact-message" name="message" required minlength="10" maxlength="4000" placeholder="Share a few details so we can help out faster."><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></textarea>
            <small>We typically reply within 1–2 business days.</small>
          </label>

          <div class="cta-row" style="justify-content:flex-start;">
            <button type="submit" class="btn btn-primary">Send message</button>
            <span class="subtle">Or email <a href="mailto:contact@palmtreesdigital.com">contact@palmtreesdigital.com</a></span>
          </div>
        </form>
      </section>

      <aside class="contact-card contact-meta">
        <div>
          <strong>Partnerships &amp; media</strong>
          <p>Looking to collaborate or feature SRN? Drop the details in the form and the right teammate will follow up.</p>
        </div>
        <div>
          <strong>Support hours</strong>
          <p>We’re online Monday–Friday, 9am–5pm PT. Expect quicker replies during these hours.</p>
        </div>
        <div>
          <strong>Need immediate help?</strong>
            <p>Check our <a href="buy-gasergy.php#faq">FAQ</a> for quick answers or email <a href="mailto:contact@palmtreesdigital.com">contact@palmtreesdigital.com</a>.</p>
        </div>
      </aside>
    </div>
  </main>

  <footer class="container">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap">
      <div>© <span id="year"></span> SRN — Software Robot Network</div>
      <div style="display:flex; gap:14px">
        <a href="#">Terms</a>
        <a href="#">Privacy</a>
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
