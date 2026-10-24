<?php
/**
 * functions.php – Shared utility functions.
 * Starts the session once with secure cookie settings,
 * and provides sanitize(), csrf_token(), and verify_csrf().
 */

if (session_status() === PHP_SESSION_NONE) {
    // Set secure cookie parameters before starting the session.
    session_set_cookie_params([
        'lifetime' => 0,           // until browser is closed
        'path'     => '/',
        'domain'   => '',          // current domain only
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,        // not accessible via JavaScript
        'samesite' => 'Lax',       // CSRF mitigation
    ]);
    session_start();
}

/**
 * Sanitize a value for safe HTML output.
 *
 * @param  string $value
 * @return string
 */
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate (or return the existing) CSRF token for the current session.
 *
 * @return string
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token submitted with a form.
 *
 * @param  string $token
 * @return bool
 */
function verify_csrf(string $token): bool {
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
