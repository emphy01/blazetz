<?php
require_once 'auth.php';
require_once 'functions.php';
require_once 'cart_functions.php';

$userId    = $_SESSION['user_id'];
$cartItems = getCartItems($userId);
$cartTotal = getCartTotal($userId);
$cartCount = count($cartItems);
$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kelola keranjang belanja Anda di BLAZT – sneaker original Nike, Adidas, dan Puma.">
    <title>Keranjang Belanja – BLAZT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .cart-header { margin-bottom: 30px; }
        .cart-header h2 { font-size: 28px; color: #222; margin-bottom: 10px; }
        .cart-items { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .cart-item { display: flex; align-items: center; padding: 20px; border-bottom: 1px solid #eee; }
        .cart-item:last-child { border-bottom: none; }
        .item-image { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-right: 20px; }
        .item-details { flex: 1; }
        .item-name { font-size: 16px; font-weight: 600; color: #222; margin-bottom: 5px; }
        .item-brand { font-size: 14px; color: #666; text-transform: uppercase; }
        .item-size { font-size: 13px; color: #888; margin-top: 5px; }
        .item-price { font-size: 18px; font-weight: 700; color: #4A90E2; margin: 0 20px; }
        .item-quantity { display: flex; align-items: center; gap: 10px; }
        .qty-btn { width: 32px; height: 32px; border: 1px solid #ddd; background: #fff; cursor: pointer; border-radius: 4px; font-size: 16px; }
        .qty-btn:hover { background: #f5f5f5; }
        .qty-input { width: 50px; text-align: center; border: 1px solid #ddd; padding: 6px; border-radius: 4px; }
        .item-remove { margin-left: 20px; color: #E74C3C; cursor: pointer; font-size: 20px; }
        .item-remove:hover { color: #C0392B; }
        .cart-empty { text-align: center; padding: 60px 20px; }
        .cart-empty i { font-size: 64px; color: #ddd; margin-bottom: 20px; }
        .cart-summary { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 25px; margin-top: 30px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 16px; }
        .summary-total { font-size: 20px; font-weight: 700; color: #222; padding-top: 15px; border-top: 2px solid #eee; }
        .checkout-btn { width: 100%; padding: 15px; background: #27AE60; color: #fff; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 20px; }
        .checkout-btn:hover { background: #229954; }
        .continue-shopping { display: inline-block; margin-top: 20px; color: #4A90E2; text-decoration: none; font-weight: 600; }
        .continue-shopping:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <section id="header">
        <a href="home.php"><img src="img/logo1.png" class="logo" alt="BLAZT logo"></a>
        <div>
            <ul id="navbar">
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="orders.php">My Order</a></li>
                <li><a class="active" href="cart.php"><i class="fa-solid fa-bag-shopping"></i></a></li>
            </ul>
        </div>
    </section>

    <div class="cart-container">
        <div class="cart-header">
            <h2>Keranjang Belanja</h2>
            <p><?= $cartCount ?> item dalam keranjang</p>
        </div>

        <?php if (empty($cartItems)): ?>
            <div class="cart-items">
                <div class="cart-empty">
                    <i class="fa-solid fa-shopping-cart"></i>
                    <h3>Keranjang Anda Kosong</h3>
                    <p>Belum ada produk dalam keranjang belanja Anda</p>
                    <a href="shop.php" class="continue-shopping">Mulai Belanja</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-items" id="cartItemsContainer">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item" data-cart-id="<?= $item['cart_id'] ?>">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="item-image">
                        <div class="item-details">
                            <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                            <div class="item-brand"><?= htmlspecialchars($item['brand']) ?></div>
                            <?php if ($item['size']): ?>
                                <div class="item-size">Ukuran: <?= htmlspecialchars($item['size']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="item-price"><?= formatRupiah($item['price']) ?></div>
                        <div class="item-quantity">
                            <button class="qty-btn" onclick="updateQty(<?= $item['cart_id'] ?>, -1)">-</button>
                            <input type="number" class="qty-input" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" readonly>
                            <button class="qty-btn" onclick="updateQty(<?= $item['cart_id'] ?>, 1)">+</button>
                        </div>
                        <i class="fa-solid fa-trash item-remove" onclick="removeItem(<?= $item['cart_id'] ?>)"></i>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotalAmount"><?= formatRupiah($cartTotal) ?></span>
                </div>
                <div class="summary-row">
                    <span>Ongkir</span>
                    <span>Gratis</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span id="totalAmount"><?= formatRupiah($cartTotal) ?></span>
                </div>
                <button class="checkout-btn" onclick="window.location.href='checkout.php'">
                    Lanjut ke Pembayaran
                </button>
                <a href="shop.php" class="continue-shopping">← Lanjut Belanja</a>
            </div>
        <?php endif; ?>
    </div>

    <section id="newsletter" class="section-p1 section-m1">
        <div class="newstext">
            <h4>#STAY_SAFE</h4>
            <p>Shop from home and get<span> special offers.</span></p>
        </div>
    </section>

    <footer id="foot" class="section-p1">
        <div class="col">
            <img class="logo" src="img/logo2.jpg" alt="BLAZT">
            <h4>Contact</h4>
            <p><strong>Insta:</strong> @crownless.zt</p>
            <p><strong>Email:</strong> milkycofee69@gmail.com</p>
            <p><strong>Phone:</strong> +62 819 999 999</p>
        </div>
        <div class="col">
            <h4>My Account</h4>
            <a href="logout.php">Log Out</a>
            <a href="cart.php">View Cart</a>
            <a href="orders.php">My Orders</a>
        </div>
        <div class="cpr">
            <p>© 2024 BLAZT. All Rights Reserved</p>
        </div>
    </footer>

    <script>
        // CSRF token injected from PHP session
        const CSRF_TOKEN = '<?= htmlspecialchars($csrfToken) ?>';

        function updateQty(cartId, change) {
            const item   = document.querySelector(`[data-cart-id="${cartId}"]`);
            const input  = item.querySelector('.qty-input');
            const newQty = parseInt(input.value) + change;

            if (newQty < 1) return;

            fetch('cart_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&cart_id=${cartId}&quantity=${newQty}&csrf_token=${encodeURIComponent(CSRF_TOKEN)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Gagal update keranjang');
                }
            })
            .catch(() => alert('Terjadi kesalahan jaringan.'));
        }

        function removeItem(cartId) {
            if (!confirm('Hapus item dari keranjang?')) return;

            fetch('cart_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=remove&cart_id=${cartId}&csrf_token=${encodeURIComponent(CSRF_TOKEN)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Gagal menghapus item');
                }
            })
            .catch(() => alert('Terjadi kesalahan jaringan.'));
        }
    </script>
</body>
</html>