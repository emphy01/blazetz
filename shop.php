<?php
require_once 'auth.php';
require_once 'db.php';

// Ambil semua produk dari db
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    $products = [];
    error_log("Error fetching products: " . $e->getMessage());
}

function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - BLAZT.com</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <section id="header">
        <a href="home.php"><img src="img/logo1.png" class="logo" alt=""></a>
        <div>
            <ul id="navbar">
                <li class="nav-search" style="position:relative;">
                    <form id="navSearchForm" onsubmit="return false;">
                        <input id="navSearchInput" type="search" placeholder="Search products..." aria-label="Search">
                        <button id="navSearchBtn" type="button" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                    <div id="navSearchResults" role="listbox"></div>
                </li>
                <li><a href="home.php">Home</a></li>
                <li><a class="active" href="shop.php">Shop</a></li>
                <li><a href="orders.php">My Order</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-bag-shopping"></i></a></li>
            </ul>
        </div>
    </section>

    <section id="page-header">
        <h2><a>#EXPLORE</a></h2>
        <p><a>get super value deals!</a></p>
    </section>

    <section id="product1" class="section-p1">
        <h2>Seasonal Product</h2>
        <a>Explore Your Seasonal Collection</a>
        <div class="pro-con">
            <?php if (empty($products)): ?>
                <div style="width:100%; text-align:center; padding:60px 20px;">
                    <i class="fa-solid fa-box-open" style="font-size:48px; color:#ddd; margin-bottom:20px;"></i>
                    <h3 style="color:#666;">Belum ada produk</h3>
                    <p style="color:#999;">Silakan jalankan seed_products.php untuk mengisi data produk</p>
                    <a href="seed_products.php" style="display:inline-block; margin-top:20px; padding:12px 24px; background:#4A90E2; color:#fff; text-decoration:none; border-radius:6px;">Seed Products</a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="pro" data-id="<?= htmlspecialchars($product['product_id']) ?>" onclick="openProduct('<?= htmlspecialchars($product['product_id']) ?>')">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                        <div class="desc">
                            <span><?= strtolower(htmlspecialchars($product['brand'])) ?></span>
                            <h5><?= htmlspecialchars($product['product_name']) ?></h5>
                            <h4><?= formatPrice($product['price']) ?></h4>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php if (count($products) > 8): ?>
    <div id="paginations" class="pagination">
        <button data-page="1">1</button>
        <button data-page="2">2</button>
        <button class="next"><i class="fa-solid fa-arrow-right"></i></button>
    </div>
    <?php endif; ?>

    <section id="newsletter" class="section-p1 section-m1">
        <div class="newstext">
            <h4>#STAY_SAFE</h4>
            <p>Shop from home and get<span> special offers.</span></p>
        </div>
    </section>  

    <footer id="foot" class="section-p1">
        <div class="col">
            <img class="logo" src="img/logo2.jpg" alt="">
            <h4>Contact</h4>
            <p><strong>Insta:</strong> @crownless.zt</p>
            <p><strong>Email:</strong> milkycofee69@gmail.com</p>
            <p><strong>Phone:</strong> +62 819 999 999</p>
        </div>
        <div class="col">
            <h4>About</h4>
            <a href="index.html">About Us</a>
            <a href="#">Delivery Information</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Condition</a> 
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

    <script src="script.js"></script>
</body>
</html>