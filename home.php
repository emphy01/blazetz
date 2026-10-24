<?php
require_once 'auth.php';
require_once 'db.php';

// Ambil produk db
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 16");
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    $products = [];
}

function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

// Split products
$newCollection = array_slice($products, 0, 8);
$newArrival = array_slice($products, 8, 8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Belanja sneaker original Nike, Adidas, dan Puma di BLAZT. Koleksi terbaru tersedia sekarang.">
    <title>BLAZT – Toko Sneaker Original</title>
    <link rel="icon" href="img/logo1.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <section id="header">
        <a href="home.php"><img src="img/logo1.png" class="logo" alt="BLAZT logo"></a>
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
                <li><a class="active" href="home.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="orders.php">My Order</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-bag-shopping"></i></a></li>
            </ul>
        </div>
    </section>

    <section id="hero">
        <h4><a>Trade-in-offer</a></h4>
        <h2><a>Super Value Deal</a></h2>
        <h1><a>On all product</a></h1>
        <p><a>save more with coupon!</a></p>
        <a href="shop.php"><button>Shop Now</button></a>
    </section>

    <section id="feature" class="section-p1">
        <div class="fe-box">
            <img src="img/features/f1.png" alt="">
            <h6>Free Shipping</h6>
        </div>
        <div class="fe-box">
            <img src="img/features/f2.png" alt="">
            <h6>Fast Delivery</h6>
        </div>
        <div class="fe-box">
            <img src="img/features/f3.png" alt="">
            <h6>Many Coupon</h6>
        </div>
        <div class="fe-box">
            <img src="img/features/f4.png" alt="">
            <h6>Promotion</h6>
        </div>
        <div class="fe-box">
            <img src="img/features/f6.png" alt="">
            <h6>24/7 Support</h6>
        </div>
    </section>

    <section id="product1" class="section-p1">
        <h2>New NBA Collection</h2>
        <p>Your Ultimate Game Changer</p>
        <div class="pro-con">
            <?php foreach ($newCollection as $product): ?>
                <div class="pro" data-id="<?= htmlspecialchars($product['product_id']) ?>" onclick="openProduct('<?= htmlspecialchars($product['product_id']) ?>')">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    <div class="desc">
                        <span><?= strtolower(htmlspecialchars($product['brand'])) ?></span>
                        <h5><?= htmlspecialchars($product['product_name']) ?></h5>
                        <h4><?= formatPrice($product['price']) ?></h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <section id="banner" class="section-m1">
        <h4>Repair Services</h4>
        <h2>Up to <span>40% Off</span> - All Product</h2>
        <button class="normal">Explore More</button>
    </section>

    <section id="product2" class="section-p1">
        <h2>New Arrival</h2>
        <p>Ready to go Into Your Collection</p>
        <div class="pro-con">
            <?php foreach ($newArrival as $product): ?>
                <div class="pro" data-id="<?= htmlspecialchars($product['product_id']) ?>" onclick="openProduct('<?= htmlspecialchars($product['product_id']) ?>')">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    <div class="desc">
                        <span><?= strtolower(htmlspecialchars($product['brand'])) ?></span>
                        <h5><?= htmlspecialchars($product['product_name']) ?></h5>
                        <h4><?= formatPrice($product['price']) ?></h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="sm-banner" class="section-p1">
        <div class="banner-box">
            <h4>Crazy Deals</h4>
            <h2>Extra Discount</h2>
            <span>Get Big Discount Without Minimum Purchase</span>
            <button class="white">Learn More</button>
        </div>
        <div class="banner-box banner-box2">
            <h4>Upcoming Product</h4>
            <h2>Coming Soon</h2>
            <span>Are You Ready For Our Upcomming Product?</span>
            <button class="white">Learn More</button>
        </div>
    </section>

    <section id="banner3">
        <div class="banner-box">
            <h2>Seasonal Sale</h2>
            <h3>Summer Collection -10% Off</h3>
        </div>
        <div class="banner-box banner-box2">
            <h2>Collab Product</h2>
            <h3>Be Stylish like Your Fav Person</h3>
        </div>
        <div class="banner-box banner-box3">
            <h2>Limited Product</h2>
            <h3>Collect the Limited Production Items</h3>
        </div>
    </section>

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
    <script>
        // Update PRODUCTS array search
        const PRODUCTS = <?= json_encode(array_map(function($p) {
            return [
                'id' => $p['product_id'],
                'title' => $p['product_name'],
                'brand' => $p['brand'],
                'img' => $p['image_url']
            ];
        }, $products)) ?>;
    </script>
</body>
</html>