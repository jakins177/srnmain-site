<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$gasergy_balance = 0;

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
        // Silently fail, user will just see 0 balance
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Terms &amp; Privacy • Palm Trees AI Software Robot Network</title>
  <meta name="description" content="Terms of Service and Privacy Policy for Palm Trees AI Software Robot Network (SRN) at https://palmtreesai.com.">
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

  <main class="section container" style="padding-top:4rem; padding-bottom:4rem;">
    <div class="policy-intro">
      <span class="eyebrow">Palm Trees AI Software Robot Network</span>
      <h1>Terms of Service &amp; Privacy Policy</h1>
      <p class="lead">These terms govern your use of Palm Trees AI Software Robot Network (&ldquo;SRN&rdquo;) and explain how we handle personal information on <a href="https://palmtreesai.com" target="_blank" rel="noopener">https://palmtreesai.com</a> and related services.</p>
      <p class="subtle">Effective: July 22, 2024</p>
    </div>

    <div class="policy-layout">
      <section class="policy-section">
        <h2>1. Acceptance of Terms</h2>
        <p>By creating an account, purchasing Gasergy credits, or using any SRN product, you agree to these Terms of Service (&ldquo;Terms&rdquo;). If you do not agree, please do not use the platform.</p>
        <p>These Terms apply to all Palm Trees AI Software Robot Network offerings, including experimental or alpha features, and supplement any specific terms provided for individual personas or tools.</p>
      </section>

      <section class="policy-section">
        <h2>2. Accounts &amp; Eligibility</h2>
        <ul>
          <li>You must be at least 18 years old (or the age of majority in your jurisdiction) to create an account.</li>
          <li>Keep your login credentials secure and notify us immediately if you suspect unauthorized access.</li>
          <li>You are responsible for all activity that occurs under your account, including usage by teammates you invite.</li>
        </ul>
      </section>

      <section class="policy-section">
        <h2>3. Gasergy Credits &amp; Payments</h2>
        <ul>
          <li>SRN uses metered credits called Gasergy to power AI actions across the network.</li>
          <li>Credit purchases and subscriptions are processed through trusted payment partners such as Stripe. Additional purchase terms may apply.</li>
          <li>Unless otherwise stated, credits are non-refundable. We may pause or reverse charges in cases of suspected fraud or abuse.</li>
        </ul>
        <p class="subtle">Discounted pre-launch pricing may change at any time. We will notify active subscribers before any material price change takes effect.</p>
      </section>

      <section class="policy-section">
        <h2>4. Acceptable Use</h2>
        <ul>
          <li>Use the services in compliance with all applicable laws and industry regulations.</li>
          <li>Do not attempt to reverse engineer, scrape, or disrupt SRN systems, nor misrepresent outputs as human-generated without disclosure.</li>
          <li>Respect third-party rights&mdash;avoid submitting content that is unlawful, infringing, or confidential without authorization.</li>
        </ul>
        <p>We may suspend or terminate access for behavior that threatens the security, reliability, or reputation of SRN.</p>
      </section>

      <section class="policy-section">
        <h2>5. AI Guidance Disclaimer</h2>
        <p>SRN personas provide automated suggestions. Outputs may be incomplete, inaccurate, or outdated. The platform does not replace professional advice; you remain responsible for reviewing results and complying with applicable standards. Do not use SRN to provide or solicit emergency, medical, legal, or financial advice without qualified oversight.</p>
      </section>

      <section class="policy-section">
        <h2>6. Feedback &amp; Beta Features</h2>
        <p>We welcome feedback about SRN. By submitting ideas or suggestions, you grant Palm Trees AI permission to use them without obligation. Some features are launched in alpha or beta; functionality may change, break, or be retired at any time.</p>
      </section>

      <section class="policy-section">
        <h2>7. Termination</h2>
        <p>You may stop using SRN at any time. We reserve the right to suspend or close accounts that violate these Terms or create security risks. Upon termination, you remain responsible for outstanding fees, and unused credits may be forfeited unless required otherwise by law.</p>
      </section>

      <section class="policy-section">
        <h2>8. Disclaimers &amp; Limitation of Liability</h2>
        <p>SRN is provided on an &ldquo;as is&rdquo; and &ldquo;as available&rdquo; basis. To the fullest extent permitted by law, Palm Trees AI disclaims implied warranties of merchantability, fitness for a particular purpose, and non-infringement.</p>
        <p>To the extent allowed by law, Palm Trees AI and its team will not be liable for indirect, incidental, or consequential damages arising from your use of SRN. Our aggregate liability is limited to the greater of the amounts paid to us in the 12 months before the claim or USD $100.</p>
      </section>

      <section class="policy-section">
        <h2>9. Changes to These Terms</h2>
        <p>We may update these Terms from time to time. Material changes will be posted here with a new effective date, and we may notify you by email or in-app messaging. Continued use after changes take effect constitutes acceptance.</p>
      </section>

      <section class="policy-section">
        <h2>10. Contact</h2>
        <p>If you have questions about these Terms, email <a href="mailto:contact@palmtreesdigital.com">contact@palmtreesdigital.com</a> or write to Palm Trees AI Software Robot Network, Attn: Legal, 548 Market St #87995, San Francisco, CA 94104.</p>
      </section>

      <section class="policy-divider">
        <h2>Privacy Policy</h2>
        <p>We care about your trust. This Privacy Policy explains how Palm Trees AI collects, uses, and protects data when you interact with SRN.</p>
      </section>

      <section class="policy-section">
        <h2>1. Information We Collect</h2>
        <ul>
          <li><strong>Account details:</strong> name, email, password hashes, and organization information you provide.</li>
          <li><strong>Usage data:</strong> persona selections, feature interactions, device metadata, and diagnostic logs to improve reliability.</li>
          <li><strong>Content you share:</strong> prompts, files, or feedback submitted to personas. Do not upload confidential data unless you have rights to do so.</li>
          <li><strong>Billing data:</strong> payment method details and transaction history handled by payment processors on our behalf.</li>
          <li><strong>Support records:</strong> messages and attachments sent to our support channels.</li>
        </ul>
      </section>

      <section class="policy-section">
        <h2>2. How We Use Information</h2>
        <ul>
          <li>Deliver and personalize SRN features, including tailoring personas and credit usage.</li>
          <li>Process transactions, detect fraud, and maintain account security.</li>
          <li>Provide customer support, product updates, and important notices.</li>
          <li>Analyze anonymized trends to improve performance, reliability, and user experience.</li>
        </ul>
      </section>

      <section class="policy-section">
        <h2>3. Sharing &amp; Disclosure</h2>
        <p>We do not sell your personal information. We may share limited data with:</p>
        <ul>
          <li>Service providers and infrastructure partners (e.g., cloud hosting, analytics, email, and payment processors such as Stripe) who are bound by confidentiality obligations.</li>
          <li>Professional advisors or authorities when required to comply with the law, protect our rights, or investigate abuse.</li>
          <li>Successors in a corporate transaction, provided continued protection of your data is ensured.</li>
        </ul>
      </section>

      <section class="policy-section">
        <h2>4. Cookies &amp; Tracking</h2>
        <p>We use cookies and similar technologies to remember your preferences, keep sessions secure, and understand product performance. You can adjust browser settings to limit cookies, but certain features may not function correctly without them.</p>
      </section>

      <section class="policy-section">
        <h2>5. Data Security &amp; Retention</h2>
        <p>SRN uses industry-standard safeguards to protect your data, including encryption in transit, access controls, and monitoring. No system is completely secure; please use strong passwords and enable available security controls. We retain information for as long as necessary to provide services, comply with legal obligations, or resolve disputes.</p>
      </section>

      <section class="policy-section">
        <h2>6. International Users</h2>
        <p>SRN is operated from the United States. If you access the platform from other regions, you consent to the transfer and processing of your information in the U.S. or other countries where our vendors operate.</p>
      </section>

      <section class="policy-section">
        <h2>7. Your Choices &amp; Rights</h2>
        <ul>
          <li>Access, update, or delete profile information through your account settings or by contacting us.</li>
          <li>Manage marketing preferences by following the unsubscribe instructions in our emails.</li>
          <li>Request copies of your data or ask us to restrict processing where applicable by law.</li>
        </ul>
      </section>

      <section class="policy-section">
        <h2>8. Children&rsquo;s Privacy</h2>
        <p>SRN is not directed to children under 13. We do not knowingly collect personal information from children. If you believe a child has provided data, contact us to remove it.</p>
      </section>

      <section class="policy-section">
        <h2>9. Updates to This Policy</h2>
        <p>We may revise this Privacy Policy to reflect product or regulatory changes. Updates will be posted here with a revised effective date. We encourage you to review this page periodically.</p>
      </section>

      <section class="policy-section">
        <h2>10. Contact</h2>
        <p>For privacy questions or data requests, email <a href="mailto:privacy@palmtreesai.com">privacy@palmtreesai.com</a>. You can also reach us at Palm Trees AI Software Robot Network, 548 Market St #87995, San Francisco, CA 94104.</p>
      </section>
    </div>
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
