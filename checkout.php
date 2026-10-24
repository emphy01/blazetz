<?php
require_once 'auth.php';
require_once 'cart_functions.php';
require_once 'functions.php';

$userId = $_SESSION['user_id'];
$cartItems = getCartItems($userId);
$cartTotal = getCartTotal($userId);

// Redirect jika cart kosong
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $shippingData = [
            'address' => sanitize($_POST['address'] ?? ''),
            'city' => sanitize($_POST['city'] ?? ''),
            'postal_code' => sanitize($_POST['postal_code'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? '')
        ];
        
        $paymentMethod = sanitize($_POST['payment_method'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        
        // Validasi
        if (empty($shippingData['address']) || empty($shippingData['city']) || 
            empty($shippingData['postal_code']) || empty($shippingData['phone'])) {
            $error = 'Semua field alamat harus diisi.';
        } elseif (empty($paymentMethod)) {
            $error = 'Pilih metode pembayaran.';
        } else {
            $result = createOrder($userId, $shippingData, $paymentMethod, $notes);
            
            if ($result['success']) {
                header('Location: order_success.php?order=' . $result['order_number']);
                exit;
            } else {
                $error = $result['error'];
            }
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
    <title>Checkout - BLAZT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 400px; gap: 30px; }
        .checkout-form { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .form-section { margin-bottom: 30px; }
        .form-section h3 { font-size: 20px; margin-bottom: 15px; color: #222; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .payment-methods { display: grid; gap: 10px; }
        .payment-option { display: flex; align-items: center; padding: 15px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; }
        .payment-option input[type="radio"] { margin-right: 10px; }
        .payment-option:has(input:checked) { border-color: #4A90E2; background: #f0f7ff; }
        .order-summary { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 20px; }
        .summary-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
        .summary-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px; }
        .summary-total { font-size: 20px; font-weight: 700; padding-top: 15px; border-top: 2px solid #eee; margin-top: 15px; }
        .submit-btn { width: 100%; padding: 15px; background: #27AE60; color: #fff; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 20px; }
        .submit-btn:hover { background: #229954; }
        .error-msg { background: #FFE7E7; color: #C0392B; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        @media (max-width: 768px) {
            .checkout-container { grid-template-columns: 1fr; }
            .order-summary { position: static; }
        }
    </style>
</head>
<body>
    <section id="header">
        <a href="home.php"><img src="img/logo1.png" class="logo" alt=""></a>
        <div>
            <ul id="navbar">
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-bag-shopping"></i></a></li>
            </ul>
        </div>
    </section>

    <div class="checkout-container">
        <div class="checkout-form">
            <h2 style="margin-bottom: 20px;">Checkout</h2>
            
            <?php if ($error): ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                
                <div class="form-section">
                    <h3>Alamat Pengiriman</h3>
                    <div class="form-group">
                        <label>Alamat Lengkap *</label>
                        <textarea name="address" required placeholder="Jalan, No. Rumah, RT/RW"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Kota *</label>
                        <input type="text" name="city" required placeholder="Contoh: Jakarta">
                    </div>
                    <div class="form-group">
                        <label>Kode Pos *</label>
                        <input type="text" name="postal_code" required placeholder="12345">
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon *</label>
                        <input type="tel" name="phone" required placeholder="+62 812-3456-7890">
                    </div>
                </div>

                <div class="form-section">
                    <h3>Metode Pembayaran</h3>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="transfer_bca" required>
                            <span>Transfer Bank BCA</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="transfer_mandiri">
                            <span>Transfer Bank Mandiri</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="gopay">
                            <span>GoPay</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="ovo">
                            <span>OVO</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod">
                            <span>COD (Bayar di Tempat)</span>
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Catatan (Opsional)</h3>
                    <div class="form-group">
                        <textarea name="notes" placeholder="Tambahkan catatan untuk pesanan Anda..."></textarea>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Buat Pesanan</button>
            </form>
        </div>

        <div class="order-summary">
            <h3 style="margin-bottom: 20px;">Ringkasan Pesanan</h3>
            <?php foreach ($cartItems as $item): ?>
                <div class="summary-item">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600;"><?= htmlspecialchars($item['product_name']) ?></div>
                        <div style="font-size: 12px; color: #666;">Qty: <?= $item['quantity'] ?> | Size: <?= $item['size'] ?></div>
                    </div>
                    <div style="font-weight: 600;"><?= formatRupiah($item['price'] * $item['quantity']) ?></div>
                </div>
            <?php endforeach; ?>
            
            <div class="summary-total">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Subtotal:</span>
                    <span><?= formatRupiah($cartTotal) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Ongkir:</span>
                    <span>Gratis</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 22px; color: #27AE60;">
                    <span>Total:</span>
                    <span><?= formatRupiah($cartTotal) ?></span>
                </div>
            </div>
        </div>
    </div>

    <footer id="foot" class="section-p1">
        <div class="cpr">
            <p>© 2024 BLAZT. All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>