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
  <title>SRN • Palm Trees AI Software Robot Network</title>
  <meta name="description" content="Meet SRN — a network of AI agent personas you can try, customize, and power with monthly credits.">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-base-path="./">
  <nav class="nav">
    <div class="container nav-inner">
      <div class="brand">
        <div class="logo" aria-hidden="true"></div>
        <div>
          <div>SRN</div>
          <small style="color:var(--muted); font-weight:600">Palm Trees AI Software Robot Network</small>
        </div>
      </div>
      <div class="nav-links">
        <a href="#bots">Bots</a>
        <a href="pages/buy-gasergy.php">Credits</a>
        <a href="#builder">Custom Persona</a>
        <a href="#faq">FAQ</a>
        <?php if ($is_logged_in): ?>
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
        <h1>Meet your <span class="gradient-text">Software Robot Network</span></h1>
        <p>Curated AI agent personas for whatever you’re building next — start with coding (HTML, CSS, JavaScript), then expand into legal, real estate, medical, and entertainment. Top‑tier UX, no fluff.</p>
        <div class="cta-row">
          <a href="#bots" class="btn btn-primary">Explore bots</a>
          <a href="pages/buy-gasergy.php" class="btn btn-ghost">Buy credits</a>
          <span class="chip" title="Live status"><span class="dot"></span> Alpha preview</span>
        </div>
        <div class="hero-card" style="margin-top:16px" aria-live="polite">
          <div class="row"><span class="chip">Fast setup</span><span class="chip">Same login for all agents</span><span class="chip">Credit‑based</span></div>
          <div class="row" style="color:var(--muted)">SRN uses monthly credits called gasergy — for now use your same gasergy on any AI functionalty network wide. Upgrade anytime.</div>
        </div>
      </div>
      <aside>
        <div class="hero-card" role="status">
          <div class="row"><div class="avatar" aria-hidden="true">HM</div><div><strong>HTML Master</strong><br><small class="subtle">Your friendly markup AI Tutor</small></div></div>
          <div class="row" style="color:var(--muted)">“Course and AI companion”</div>
          <div>
            <a class="btn btn-primary" href="pages/html-master-signup.html">Try now</a>
            <button class="btn btn-ghost" data-details="html-master">Details</button>
          </div>
          <div class="row"><small class="subtle">Tip: Course is free, learn HTML even faster with the HTML master AI companion</small></div>
        </div>
      </aside>
    </div>
  </header>

  <section id="bots" class="section container">
    <h2>Persona library</h2>
    <p class="lead">Pick a specialist to get moving faster. Filter by category or search by name.</p>
    <div class="pillbar" id="pillbar" role="tablist" aria-label="Categories"></div>
    <div class="searchbar" style="margin-bottom:14px">
      <input id="searchInput" placeholder="Search personas… (e.g. HTML, legal, JavaScript)" aria-label="Search personas" />
      <button class="btn btn-ghost" id="clearSearch" title="Clear search">Clear</button>
    </div>
    <div class="grid" id="cards"></div>
  </section>

  <section id="builder" class="section container">
    <div class="cta-section">
      <h2>Create a <span class="gradient-text">custom AI persona</span></h2>
      <p class="lead">Design the tone, tools, and guardrails. Share it with your team — or with the world. <strong style="color:var(--warn)">Coming soon.</strong></p>
      <div class="cta-row">
        <a
          href="https://forms.gle/jdXHYwVAL5miG11f7"
          class="btn btn-primary"
          id="joinWaitlist"
          target="_blank"
          rel="noopener"
        >Join Waitlist</a>
        <!-- <button class="btn btn-ghost" id="watchDemo">Watch concept demo</button> -->
        <span class="subtle">We’ll email you when the builder opens.</span>
      </div>
    </div>
  </section>

  <section id="pricing-cta" class="section container">
    <div class="cta-section">
        <h2>Ready to power up?</h2>
        <p class="lead">Purchase Gasergy credits to unlock the full potential of your AI agents.</p>
        <div class="cta-row">
            <a href="pages/buy-gasergy.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">Buy Gasergy Credits</a>
        </div>
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
        <a href="#">Terms</a>
        <a href="#">Privacy</a>
        <a href="#">Contact</a>
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