<?php
/**
 * register.php – User registration.
 * - Debug output disabled (handled by db.php → config.php)
 * - Password minimum length from config constant
 * - CSRF protected
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

$errors = [];
$old    = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token tidak valid.';
    }

    $name     = sanitize($_POST['name'] ?? '');
    $email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    // Preserve old input (safe)
    $old['name']  = sanitize($_POST['name'] ?? '');
    $old['email'] = sanitize($_POST['email'] ?? '');

    if (!$name)                                      $errors[] = 'Nama wajib diisi.';
    if (!$email)                                     $errors[] = 'Email tidak valid.';
    if (strlen($password) < MIN_PASSWORD_LENGTH)     $errors[] = 'Password minimal ' . MIN_PASSWORD_LENGTH . ' karakter.';
    if ($password !== $confirm)                      $errors[] = 'Konfirmasi password tidak cocok.';

    if (!$errors) {
        try {
            $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $errors[] = 'Email sudah terdaftar.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
                );
                $stmt->execute([$name, $email, $hash]);

                header('Location: login.php?success=' . urlencode('Registrasi berhasil! Silakan masuk.'));
                exit;
            }
        } catch (Exception $e) {
            error_log('Register error: ' . $e->getMessage());
            $errors[] = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar akun BLAZT untuk mulai berbelanja sneaker original Nike, Adidas, dan Puma.">
    <title>Daftar Akun – BLAZT</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f4f6f8; font-family: 'Segoe UI', sans-serif; }
        .auth-container { min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .auth-card { width: 100%; max-width: 420px; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .auth-card h2 { text-align: center; margin-bottom: 10px; }
        .auth-card p { text-align: center; color: #777; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; margin-bottom: 6px; color: #333; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border-radius: 6px; border: 1px solid #ddd; font-size: 14px; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: #000; }
        .btn-auth { width: 100%; padding: 12px; background: #000; color: #fff; border: none; border-radius: 6px; font-size: 15px; cursor: pointer; }
        .btn-auth:hover { opacity: 0.9; }
        .auth-footer { text-align: center; margin-top: 15px; font-size: 14px; }
        .auth-footer a { color: #000; text-decoration: none; font-weight: 600; }
        .error-box { background: #ffe6e6; color: #b30000; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">

        <h2>Buat Akun</h2>
        <p>Daftar untuk mulai berbelanja</p>

        <?php if ($errors): ?>
            <div class="error-box" role="alert">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <div class="form-group">
                <label for="reg-name">Nama Lengkap</label>
                <input id="reg-name" type="text" name="name" placeholder="Nama Lengkap"
                       value="<?= htmlspecialchars($old['name']) ?>" required autocomplete="name">
            </div>

            <div class="form-group">
                <label for="reg-email">Email</label>
                <input id="reg-email" type="email" name="email" placeholder="Email"
                       value="<?= htmlspecialchars($old['email']) ?>" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="reg-password">Password (min. <?= MIN_PASSWORD_LENGTH ?> karakter)</label>
                <input id="reg-password" type="password" name="password" placeholder="Password" required autocomplete="new-password">
            </div>

            <div class="form-group">
                <label for="reg-confirm">Konfirmasi Password</label>
                <input id="reg-confirm" type="password" name="password_confirm" placeholder="Konfirmasi Password" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-auth">Daftar</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun?
            <a href="login.php">Masuk</a>
        </div>

    </div>
</div>

</body>
</html>
