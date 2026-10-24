<?php
/**
 * authenticate.php – Handles login form submission.
 * - CSRF verified
 * - Rate-limiting / login throttle via session
 * - Error display disabled (logs to file via db.php → config.php)
 */

require_once 'db.php';      // also applies error_reporting settings
require_once 'functions.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// CSRF check
$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf)) {
    header('Location: login.php?error=' . urlencode('Invalid request.'));
    exit;
}

// ── Rate-limiting ─────────────────────────────────────────────────────────────
$_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
$_SESSION['login_last_attempt'] = $_SESSION['login_last_attempt'] ?? 0;

// Reset counter after lockout period
if (
    $_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS
    && (time() - $_SESSION['login_last_attempt']) < LOGIN_LOCKOUT_SECONDS
) {
    $wait = LOGIN_LOCKOUT_SECONDS - (time() - $_SESSION['login_last_attempt']);
    header('Location: login.php?error=' . urlencode("Terlalu banyak percobaan. Coba lagi dalam {$wait} detik."));
    exit;
}

if ((time() - $_SESSION['login_last_attempt']) >= LOGIN_LOCKOUT_SECONDS) {
    $_SESSION['login_attempts'] = 0;
}

// ── Input validation ──────────────────────────────────────────────────────────
$email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    header('Location: login.php?error=' . urlencode('Email atau kata sandi tidak boleh kosong.'));
    exit;
}

// ── Database lookup ───────────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare(
        'SELECT user_id, name, email, password_hash FROM users WHERE email = :email LIMIT 1'
    );
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
} catch (Exception $e) {
    error_log('Login query error: ' . $e->getMessage());
    header('Location: login.php?error=' . urlencode('Terjadi kesalahan. Silakan coba lagi.'));
    exit;
}

// ── Password verification ─────────────────────────────────────────────────────
if ($user && password_verify($password, $user['password_hash'])) {
    // Successful login – reset rate-limit counter
    $_SESSION['login_attempts']    = 0;
    $_SESSION['login_last_attempt'] = 0;

    session_regenerate_id(true);
    $_SESSION['user_id']    = $user['user_id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name']  = $user['name'];
    unset($_SESSION['csrf_token']); // invalidate old CSRF token

    header('Location: home.php');
    exit;
}

// Failed login – increment counter
$_SESSION['login_attempts']++;
$_SESSION['login_last_attempt'] = time();

header('Location: login.php?error=' . urlencode('Email atau kata sandi salah.'));
exit;
