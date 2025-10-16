<?php
$envPath = __DIR__ . '/../app.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($envKey, $envValue) = explode('=', $line, 2);
        $envKey = trim($envKey);
        $envValue = trim($envValue);

        if (!array_key_exists($envKey, $_SERVER) && !array_key_exists($envKey, $_ENV)) {
            putenv(sprintf('%s=%s', $envKey, $envValue));
            $_ENV[$envKey] = $envValue;
            $_SERVER[$envKey] = $envValue;
        }
    }
}
