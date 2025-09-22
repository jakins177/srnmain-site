<?php
require_once __DIR__ . '/config.php';

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    require_once __DIR__ . '/logging.php';
    custom_log("Database Connection Error: " . $e->getMessage(), 'db.log');

    // For the user, just show a generic error.
    // The 500 error will be triggered by this `die()` statement.
    http_response_code(500);
    die("A database error occurred. Please check the server logs for details.");
}
