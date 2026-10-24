<?php
session_start();

// Kalo udah login = redirect home
if (!empty($_SESSION['user_email'])) {
    header('Location: home.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login - BLAZT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <style>
    .login-page {
        min-height: 80vh;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:40px 16px;
    }
    .login-card{
        width:100%;
        max-width:420px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        padding:28px;
    }
    .login-card h2{ margin:0 0 12px 0; font-size:22px; }
    .login-card p.lead{ color: #666; margin-bottom:18px; }
    .form-group{ margin-bottom:14px; }
    .form-group label{ display:block; font-size:13px; margin-bottom:6px; color:#333; }
    .form-group input{
        width:100%; padding:12px 14px; border-radius:8px; border:1px solid #e6e6e6; font-size:15px;
    }
    .btn-primary{
        width:100%; padding:12px; border-radius:8px; border:none; cursor:pointer;
        background: #111827; color:#fff; font-weight:600;
    }
    .muted { color:#777; font-size:14px; margin-top:10px; text-align:center; }
    .error{ background:#FFE7E7; color:#8A1F1F; padding:10px; border-radius:8px; margin-bottom:12px; font-size:14px; }
    .top-links { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .top-links a { color:#111827; text-decoration:none; font-weight:600; }
    @media (max-width:480px){
        .login-card{ padding:20px; border-radius:10px; }
    }
    </style>
</head>
<body>
    <section id="header" style="padding:18px 20px;">
        <a href="login.php"><img src="img/logo1.png" class="logo" alt="logo" style="height:40px;"></a>
        <div style="float:right;">
            <ul id="navbar" style="display:flex; gap:12px; list-style:none;">
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </div>
    </section>

    <main class="login-page">
        <div class="login-card" role="main" aria-labelledby="loginTitle">
            <div class="top-links">
                <a href="register.php">Daftar</a>
            </div>

            <h2 id="loginTitle">Masuk ke akun Anda</h2>
            <p class="lead">Masuk untuk melanjutkan belanja — sederhana dan aman.</p>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="authenticate.php" method="post" autocomplete="on" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" placeholder="nama@domain.com" required />
                </div>

                <div class="form-group">
                    <label for="password">Kata sandi</label>
                    <input id="password" name="password" type="password" placeholder="Masukkan kata sandi" required />
                </div>

                <button class="btn-primary" type="submit">Masuk</button>
            </form>

            <div class="muted">
                <small>Belum punya akun? <a href="register.php">Daftar sekarang</a></small>
            </div>
        </div>
    </main>

</body>
</html>
