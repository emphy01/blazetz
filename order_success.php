<?php
require_once 'auth.php';

$orderNumber = $_GET['order'] ?? '';

if (empty($orderNumber)) {
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - BLAZT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        .success-container { max-width: 600px; margin: 60px auto; padding: 40px 20px; text-align: center; }
        .success-icon { width: 80px; height: 80px; background: #27AE60; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; }
        .success-icon i { font-size: 40px; color: #fff; }
        .success-title { font-size: 28px; color: #222; margin-bottom: 10px; }
        .order-number { font-size: 18px; color: #666; margin-bottom: 30px; }
        .order-number strong { color: #4A90E2; font-size: 20px; }
        .success-message { font-size: 16px; color: #666; line-height: 1.6; margin-bottom: 40px; }
        .action-buttons { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
        .btn { padding: 15px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 16px; display: inline-block; }
        .btn-primary { background: #4A90E2; color: #fff; }
        .btn-primary:hover { background: #357ABD; }
        .btn-secondary { background: #fff; color: #222; border: 2px solid #ddd; }
        .btn-secondary:hover { background: #f5f5f5; }
    </style>
</head>
<body>
    <section id="header">
        <a href="home.php"><img src="img/logo1.png" class="logo" alt=""></a>
        <div>
            <ul id="navbar">
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="orders.php">My Order</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-bag-shopping"></i></a></li>
            </ul>
        </div>
    </section>

    <div class="success-container">
        <div class="success-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        
        <h1 class="success-title">Pesanan Berhasil Dibuat!</h1>
        
        <div class="order-number">
            Nomor Pesanan: <strong><?= htmlspecialchars($orderNumber) ?></strong>
        </div>
        
        <div class="success-message">
            <p>Terima kasih telah berbelanja di BLAZT! Pesanan Anda telah kami terima dan sedang diproses.</p>
            <p>Kami akan mengirimkan konfirmasi dan detail pembayaran ke email Anda.</p>
        </div>
        
        <div class="action-buttons">
            <a href="orders.php" class="btn btn-primary">Lihat Pesanan Saya</a>
            <a href="shop.php" class="btn btn-secondary">Lanjut Belanja</a>
        </div>
    </div>

    <footer id="foot" class="section-p1">
        <div class="cpr">
            <p>© 2024 BLAZT. All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>