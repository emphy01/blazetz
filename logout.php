<?php
/**
 * logout.php – Menghapus semua sesi login pengguna secara total
 * dan mengalihkan kembali ke halaman utama (index.html).
 */

// Mulai sesi jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kosongkan semua data dalam $_SESSION
$_SESSION = [];

// 2. Hapus cookie sesi dari browser jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Hancurkan sesi di server
session_destroy();

// 4. Alihkan ke halaman awal (index.html)
header("Location: index.html");
exit;
