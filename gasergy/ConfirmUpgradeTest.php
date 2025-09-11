<?php
// Test for confirm_upgrade.php

// Set up test environment
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';

class ConfirmUpgradeTest extends \PHPUnit\Framework\TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        require __DIR__ . '/../auth-system/config/db.php';
        $this->pdo = $pdo;
        // Create a test user
        $email = 'testuser@example.com';
        $password = 'password';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stripe_customer_id = 'cus_test123';
        $stripe_subscription_id = 'sub_test123';

        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, stripe_customer_id, stripe_subscription_id) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE password = ?, stripe_customer_id = ?, stripe_subscription_id = ?");
        $stmt->execute([$email, $hashed_password, $stripe_customer_id, $stripe_subscription_id, $hashed_password, $stripe_customer_id, $stripe_subscription_id]);

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userId = $stmt->fetchColumn();
        $_SESSION['user_id'] = $userId;
    }

    protected function tearDown(): void
    {
        // Clean up the test user
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute(['testuser@example.com']);
    }

    public function testConfirmUpgrade()
    {
        // Simulate a POST request
        $_POST['amount'] = 500;

        // Capture the output of the script
        ob_start();
        include __DIR__ . '/confirm_upgrade.php';
        $output = ob_get_clean();

        // Debugging
        echo $output;

        // Assertions
        $this->assertStringContainsString('<h1>Confirm Plan Change</h1>', $output);
        $this->assertStringContainsString('$10.00', $output);
    }
}
