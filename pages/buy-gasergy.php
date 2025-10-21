<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$gasergy_balance = 0;

if ($is_logged_in) {
    // Adjusted path for db.php
    require_once __DIR__ . '/../config/db.php';
    try {
        $stmt = $pdo->prepare("SELECT gasergy_balance FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $gasergy_balance = $user['gasergy_balance'];
        }
    } catch (PDOException $e) {
        // Silently fail, user will just see 0 balance
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Buy Gasergy • SRN</title>
  <meta name="description" content="Purchase Gasergy credits to power your AI agent personas on the SRN network.">
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
        <a href="#faq">FAQ</a>

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

  <section id="pricing" class="section container" style="padding-top: 4rem;">

    <div>
      <h2>Pre-launch Gasergy credits</h2>
      <p class="lead">Lock in 50% off during pre-launch. Credits renew per your plan.</p>
      <p class="subtle" style="margin-top:8px"><strong>Note:</strong> SRN is still pre-launch and some products may not be available yet. Buying Gasergy now secures this discount before prices return to normal at launch.</p>
      <p style="margin-top:16px">
        <a class="btn btn-ghost" href="https://billing.stripe.com/p/login/00wbJ12SW34q2cK1O10sU00" target="_blank" rel="noopener">Manage your subscription</a>
      </p>
    </div>

    <div class="billing-toggle" role="tablist" aria-label="Billing period" style="margin-top:20px">
      <button id="billMonthly" data-active="true" aria-selected="true">Monthly</button>
      <button id="billAnnual" aria-selected="false">Annual</button>
    </div>

    <div id="pricingMonthly" class="pricing" style="margin-top:14px">
      <article class="price-card" data-plan="business-monthly" aria-labelledby="businessMonthlyTitle">
        <div class="chip">50% off</div>
        <h3 id="businessMonthlyTitle">Business</h3>
        <div class="price">$14.99<span class="subtle">/mo</span></div>
        <div class="note">10,000&nbsp;G / month</div>
        <div class="actions">
          <button class="btn btn-primary" data-buy="business-monthly">Buy Business</button>
        </div>
      </article>

      <article class="price-card" data-plan="enterprise-monthly" aria-labelledby="enterpriseMonthlyTitle">
        <div class="chip">50% off</div>
        <h3 id="enterpriseMonthlyTitle">Enterprise</h3>
        <div class="price">$61.99<span class="subtle">/mo</span></div>
        <div class="note">50,000&nbsp;G / month</div>
        <div class="actions">
          <button class="btn btn-primary" data-buy="enterprise-monthly">Buy Enterprise</button>
        </div>
      </article>

      <article class="price-card" data-plan="professional-monthly" aria-labelledby="professionalMonthlyTitle">
        <div class="chip">50% off</div>
        <h3 id="professionalMonthlyTitle">Professional</h3>
        <div class="price">$4.99<span class="subtle">/mo</span></div>
        <div class="note">2,500&nbsp;G / month</div>
        <div class="actions">
          <button class="btn btn-primary" data-buy="professional-monthly">Buy Professional</button>
        </div>
      </article>

      <article class="price-card" data-plan="starter-monthly" aria-labelledby="starterMonthlyTitle">
        <div class="chip">50% off</div>
        <h3 id="starterMonthlyTitle">Starter</h3>
        <div class="price">$1.25<span class="subtle">/mo</span></div>
        <div class="note">500&nbsp;G / month</div>
        <div class="actions">
          <button class="btn btn-primary" data-buy="starter-monthly">Buy Starter</button>
        </div>
      </article>
    </div>

    <div id="pricingAnnual" class="pricing" style="margin-top:14px" hidden>
      <article class="price-card" data-plan="enterprise-annual" aria-labelledby="enterpriseAnnualTitle">
        <div class="chip">Annual • 50% off</div>
        <h3 id="enterpriseAnnualTitle">Enterprise</h3>
        <div class="price">$749.99<span class="subtle">/yr</span></div>
        <div class="note">500,000&nbsp;G / year</div>
        <div class="actions">
          <button class="btn btn-primary" data-buy="enterprise-annual">Buy Enterprise Annual</button>
        </div>
      </article>

      <article class="price-card" data-plan="business-annual" aria-labelledby="businessAnnualTitle">
        <div class="chip">Annual • 50% off</div>
        <h3 id="businessAnnualTitle">Business</h3>
        <div class="price">$179.99<span class="subtle">/yr</span></div>
        <div class="note">100,000&nbsp;G / year</div>
        <div class="actions">
          <button class="btn btn-primary" data-buy="business-annual">Buy Business Annual</button>
        </div>
      </article>

      <article class="price-card" data-plan="professional-annual" aria-labelledby="professionalAnnualTitle">
        <div class="chip">Annual • 50% off</div>
        <h3 id="professionalAnnualTitle">Professional</h3>
        <div class="price">$59.99<span class="subtle">/yr</span></div>
        <div class="note">20,500&nbsp;G / year</div>
        <div class="actions">
          <button class="btn btn-primary" data-buy="professional-annual">Buy Professional Annual</button>
        </div>
      </article>

      <article class="price-card" data-plan="starter-annual" aria-labelledby="starterAnnualTitle">
        <div class="chip">Annual • 50% off</div>
        <h3 id="starterAnnualTitle">Starter</h3>
        <div class="price">$14.70<span class="subtle">/yr</span></div>
        <div class="note">5,000&nbsp;G / year</div>
        <div class="actions">
          <button class="btn btn-primary" data-buy="starter-annual">Buy Starter Annual</button>
        </div>
      </article>
    </div>
  </section>

  <section id="faq" class="section container">
    <h2>FAQ</h2>
    <details>
      <summary><strong>How do credits work?</strong></summary>
      <p class="subtle">Each AI powered action (message, analysis, file read, etc.) uses a tiny amount of credits called gasergy based on compute. Non-AI powered actions are free. Gasergy costs will be transparently shown per action before you run it.</p>
    </details>
    <details>
      <summary><strong>Do I need an account to try a bot?</strong></summary>
      <p class="subtle">Yes but you will only have to create one login to use any bot network wide. Sign in when you want to save history or use your credits.</p>
    </details>
    <details>
      <summary><strong>Can I create my own persona?</strong></summary>
      <p class="subtle">We plan for you to be able to — the Custom Persona Builder is in development. Join the waitlist above and you’ll get early access.</p>
    </details>
  </section>

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

  <dialog id="buyModal" aria-label="Buy credits">
    <div class="modal-head">
      <strong>Buy credits</strong>
      <button class="x" data-close>✕</button>
    </div>
    <div class="modal-body">
      <form id="checkout-form" action="../stripe/create_checkout_session.php" method="POST" style="display:grid; gap:10px">
        <input type="hidden" name="plan" id="plan-input" />
        <label>Plan
            <select name="plan_display" id="buyPlan" style="width:100%; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background:rgba(255,255,255,.04); color:var(--text)">
              <option value="starter-monthly">Starter Monthly — $1.25 / 500 G</option>
              <option value="professional-monthly">Professional Monthly — $4.99 / 2,500 G</option>
              <option value="business-monthly">Business Monthly — $14.99 / 10,000 G</option>
              <option value="enterprise-monthly">Enterprise Monthly — $61.99 / 50,000 G</option>
              <option value="starter-annual">Starter Annual — $14.70 / 5,000 G</option>
              <option value="professional-annual">Professional Annual — $59.99 / 20,500 G</option>
              <option value="business-annual">Business Annual — $179.99 / 100,000 G</option>
              <option value="enterprise-annual">Enterprise Annual — $749.99 / 500,000 G</option>
            </select>
        </label>
        <div class="subtle">You will be redirected to Stripe to complete your purchase securely.</div>
      </form>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" data-close>Cancel</button>
      <button class="btn btn-primary" id="buyNow">Proceed to checkout</button>
    </div>
  </dialog>

  <script>
    // Add a global JS variable to know the login state
    window.isUserLoggedIn = <?php echo json_encode($is_logged_in); ?>;
  </script>
  <script type="module" src="../assets/js/app.js"></script>
</body>
</html>