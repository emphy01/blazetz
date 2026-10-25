<?php
/**
 * db.php – Database connection using PDO.
 * Credentials are pulled from config.php (which can read env vars).
 * Error display is suppressed in production; errors are logged to file instead.
 */

if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// Fallback constants from environment variables (useful for cloud platforms like Render)
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: '127.0.0.1');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'test');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: getenv('DB_PASSWORD') ?: '');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '4000');
}
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'production');
}

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
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // TiDB Cloud requires SSL/TLS encryption
    $isTiDB = (strpos(DB_HOST, 'tidbcloud.com') !== false) || (getenv('MYSQL_SSL') === 'true');
    if ($isTiDB) {
        if (file_exists('/etc/ssl/certs/ca-certificates.crt')) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
        } else {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
    }

    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        $options
    );
} catch (PDOException $e) {
    if (APP_ENV === 'production') {
        error_log('DB connection failed: ' . $e->getMessage());
        http_response_code(503);
        exit('Service temporarily unavailable. Please try again later.');
    }
    exit('Database connection failed: ' . $e->getMessage());
}
