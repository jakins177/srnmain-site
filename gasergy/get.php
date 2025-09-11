<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/get.log');

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth-system/frt_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buy Gasergy</title>
  <style>
    body {
      font-family: sans-serif;
      background: #f9f9f9;
      text-align: center;
      padding: 50px;
    }
    .pricing-grid {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }
    .plan {
      background: white;
      border: 2px solid #3498db;
      border-radius: 10px;
      padding: 20px;
      width: 200px;
    }
    .plan h2 {
      margin-top: 0;
    }
    .plan .price {
      font-size: 24px;
      margin: 10px 0;
    }
    .plan .gasergy {
      color: #555;
      margin: 10px 0 20px;
    }
    .popular {
      background: #3498db;
      color: white;
      padding: 2px 6px;
      border-radius: 3px;
      font-size: 12px;
    }
    .option {
      background: #3498db;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    .option:hover {
      background: #2879b8;
    }
  </style>
</head>
<body>

  <h1>Get Gasergy ⚡</h1>
  <p><a href="https://billing.stripe.com/p/login/test_00wbJ12SW34q2cK1O10sU00" target="_blank" rel="noopener">Manage Subscription</a></p>
  <p>Select your plan:</p>

  <div class="pricing-grid">
    <div class="plan">
      <h2>Starter</h2>
      <p class="price">$2.50</p>
      <p class="gasergy">500 Gasergy</p>
      <form action="create_checkout_session.php" method="POST">
        <input type="hidden" name="amount" value="500">
        <button class="option">Buy</button>
      </form>
    </div>

    <div class="plan">
      <h2>Professional <span class="popular">Popular</span></h2>
      <p class="price">$10</p>
      <p class="gasergy">2 500 Gasergy</p>
      <form action="create_checkout_session.php" method="POST">
        <input type="hidden" name="amount" value="2500">
        <button class="option">Buy</button>
      </form>
    </div>

    <div class="plan">
      <h2>Business</h2>
      <p class="price">$30</p>
      <p class="gasergy">10 000 Gasergy</p>
      <form action="create_checkout_session.php" method="POST">
        <input type="hidden" name="amount" value="10000">
        <button class="option">Buy</button>
      </form>
    </div>
  </div>

  <p class="enterprise-note">Need &gt;10 000 Gasergy per month?
    <a href="mailto:contact@palmtreesdigital.com">Contact sales</a>
    or <a href="#" id="show-enterprise">self-serve Enterprise</a>
  </p>

  <div id="enterprise" style="display:none; margin-top:20px;">
    <div class="plan">
      <h2>Enterprise</h2>
      <p class="price">$125</p>
      <p class="gasergy">50 000 Gasergy</p>
      <form action="create_checkout_session.php" method="POST">
        <input type="hidden" name="amount" value="50000">
        <button class="option">Buy</button>
      </form>
    </div>
  </div>

  <script>
  document.getElementById('show-enterprise').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('enterprise').style.display = 'block';
    this.style.display = 'none';
  });
  </script>

</body>
</html>
