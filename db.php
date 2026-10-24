<?php
/**
 * db.php – Database connection using PDO.
 * Credentials are pulled from config.php (which can read env vars).
 * Error display is suppressed in production; errors are logged to file instead.
 */

require_once __DIR__ . '/config.php';

// Apply environment-aware error settings.
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/logs/php_errors.log');
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    if (APP_ENV === 'production') {
        error_log('DB connection failed: ' . $e->getMessage());
        http_response_code(503);
        exit('Service temporarily unavailable. Please try again later.');
    }
    exit('Database connection failed: ' . $e->getMessage());
}
