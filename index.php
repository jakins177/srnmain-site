<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$gasergy_balance = 0;

if ($is_logged_in) {
    require_once __DIR__ . '/config/db.php';
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
  <title>PalmTreesAI • Levo Genesis AI Agent Builder Marketplace</title>
  <meta name="description" content="PalmTreesAI is building Levo Genesis AI, a flagship platform for creating, launching, and managing AI agents with a future-ready marketplace.">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-base-path="./">
  <nav class="nav">
    <div class="container nav-inner">
      <div class="brand">
        <div class="logo" aria-hidden="true"></div>
        <div>
          <div>PalmTreesAI</div>
          <small style="color:var(--muted); font-weight:600">Home of Levo Genesis AI</small>
        </div>
      </div>
      <div class="nav-links">
        <a href="#flagship">Levo Genesis</a>
        <a href="#vision">Platform Vision</a>
        <a href="#builder">Marketplace</a>
        <a href="#faq">FAQ</a>

        <a href="pages/contact.php">Contact</a>

        <?php if ($is_logged_in): ?>
          <a href="pages/settings.php">Settings</a>
          <span class="chip">Balance: <?php echo number_format($gasergy_balance); ?> G</span>
          <a href="auth/logout.php" class="btn btn-ghost">Log Out</a>
        <?php else: ?>
          <a href="auth.html" class="btn btn-ghost">Log In</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <header class="hero container">
    <div class="hero-grid">
      <div>
        <h1>Build, launch, and scale with <span class="gradient-text">Levo Genesis AI</span></h1>
        <p><strong>PalmTreesAI</strong> is building a focused AI agent ecosystem, led by <strong>Levo Genesis AI</strong> — our flagship platform for helping users create, manage, and eventually monetize their own AI agents.</p>
        <div class="cta-row">
          <a href="#flagship" class="btn btn-primary">Explore Levo Genesis</a>
          <a href="http://levo.palmtreesai.com/" class="btn btn-ghost">Visit SaaS App</a>
          <span class="chip" title="Live status"><span class="dot"></span> Flagship product in active build</span>
        </div>
        <div class="hero-card" style="margin-top:16px" aria-live="polite">
          <div class="row"><span class="chip">Multi-tenant SaaS</span><span class="chip">User accounts</span><span class="chip">Marketplace-ready</span></div>
          <div class="row" style="color:var(--muted)">Levo Genesis AI is being designed as a monetized platform where users can register, build their own agents, manage them in a dashboard, and grow into a broader marketplace experience.</div>
        </div>
      </div>
      <aside>
        <div class="hero-card" role="status">
          <div class="row"><div class="avatar" aria-hidden="true">LG</div><div><strong>Levo Genesis AI</strong><br><small class="subtle">Flagship AI agent builder platform</small></div></div>
          <div class="row" style="color:var(--muted)">“Create your own agents, manage them, and prepare for marketplace growth.”</div>
          <div>
            <a class="btn btn-primary" href="http://levo.palmtreesai.com/">Coming soon</a>
            <button class="btn btn-ghost" data-details="html-master">Platform details</button>
          </div>
          <div class="row"><small class="subtle">PalmTreesAI’s core product direction is shifting toward a commercial AI agent builder and marketplace experience.</small></div>
        </div>
      </aside>
    </div>
  </header>

  <section id="flagship" class="section container">
    <h2>Flagship product: Levo Genesis AI</h2>
    <p class="lead">Levo Genesis AI is the product we are building first: a focused SaaS platform where users can sign up, create their own AI agents, manage them through a secure dashboard, and prepare for a larger agent marketplace.</p>
    <div class="grid" id="cards"></div>
  </section>

  <section id="vision" class="section container">
    <div class="cta-section">
      <h2>PalmTreesAI is the brand. <span class="gradient-text">Levo Genesis AI is the flagship.</span></h2>
      <p class="lead">PalmTreesAI remains the parent company and ecosystem brand. Levo Genesis AI becomes the spearhead product — a clearer, more monetizable path that can later expand into templates, reusable agents, team workspaces, and a marketplace.</p>
      <div class="cta-row">
        <span class="chip">Parent company brand</span>
        <span class="chip">Flagship SaaS product</span>
        <span class="chip">Future marketplace path</span>
      </div>
    </div>
  </section>

  <section id="builder" class="section container">
    <div class="cta-section">
      <h2>Marketplace roadmap: <span class="gradient-text">build and publish AI agents</span></h2>
      <p class="lead">Levo Genesis AI starts as an agent builder SaaS, then grows into a marketplace where users can create, refine, share, and potentially monetize agent templates and specialized assistants. <strong style="color:var(--warn)">Coming soon.</strong></p>
      <div class="cta-row">
        <a
          href="https://forms.gle/jdXHYwVAL5miG11f7"
          class="btn btn-primary"
          id="joinWaitlist"
          target="_blank"
          rel="noopener"
        >Join Waitlist</a>
        <!-- <button class="btn btn-ghost" id="watchDemo">Watch concept demo</button> -->
        <span class="subtle">We’ll email you when Levo Genesis AI opens for early users.</span>
      </div>
    </div>
  </section>

  <section id="pricing-cta" class="section container">
    <div class="cta-section">
        <h2>Get early access to the flagship build</h2>
        <p class="lead">We’re focusing PalmTreesAI around Levo Genesis AI first. Join early, track the rollout, and be first in line when the commercial agent builder opens.</p>
        <div class="cta-row">
            <a href="http://levo.palmtreesai.com/" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">Open Levo Subdomain</a>
        </div>
    </div>
  </section>

  <section id="faq" class="section container">
    <h2>FAQ</h2>
    <details>
      <summary><strong>What is Levo Genesis AI?</strong></summary>
      <p class="subtle">Levo Genesis AI is PalmTreesAI’s flagship SaaS product: a platform for registering an account, building AI agents, managing them from a dashboard, and growing into a larger marketplace over time.</p>
    </details>
    <details>
      <summary><strong>Will users have their own login and dashboard?</strong></summary>
      <p class="subtle">Yes. The SaaS version is being designed with user registration, secure login, and account-owned agent management so each customer can build and manage their own agents.</p>
    </details>
    <details>
      <summary><strong>Will there be a marketplace?</strong></summary>
      <p class="subtle">That is the direction. The plan is to begin with a strong agent builder foundation, then expand into a marketplace experience for reusable templates, specialized assistants, and monetization opportunities.</p>
    </details>
  </section>

  <footer class="container">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap">
      <div>© <span id="year"></span> PalmTreesAI — Home of Levo Genesis AI</div>
      <div style="display:flex; gap:14px">
        <a href="pages/policies.php">Terms</a>
        <a href="pages/policies.php">Privacy</a>

        <a href="pages/contact.php">Contact</a>

      </div>
    </div>
  </footer>

  <dialog id="demoModal" aria-label="Persona demo">
    <div class="modal-head">
      <strong id="demoTitle">Persona demo</strong>
      <button class="x" data-close>✕</button>
    </div>
    <div class="modal-body">
      <div class="subtle">This is a placeholder chat. Wire this up to your API when ready.</div>
      <div id="demoChat" style="border:1px solid var(--border); border-radius:12px; padding:12px; display:grid; gap:10px; background:rgba(255,255,255,.03)"></div>
      <div style="display:flex; gap:10px">
        <input id="demoInput" placeholder="Type to the bot…" style="flex:1; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background:rgba(255,255,255,.04); color:var(--text)" />
        <button class="btn btn-primary" id="demoSend">Send</button>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-ghost" data-close>Close</button>
      <button class="btn btn-primary" id="demoUpgrade">Sign up & use credits</button>
    </div>
  </dialog>


  <dialog id="infoModal" aria-label="Details">
    <div class="modal-head">
      <strong id="infoTitle">Details</strong>
      <button class="x" data-close>✕</button>
    </div>
    <div class="modal-body" id="infoBody"></div>
    <div class="modal-actions">
      <button class="btn btn-ghost" data-close>Close</button>
    </div>
  </dialog>

  <script>
    // Add a global JS variable to know the login state
    window.isUserLoggedIn = <?php echo json_encode($is_logged_in); ?>;
  </script>
  <script type="module" src="assets/js/app.js"></script>
</body>
</html>
