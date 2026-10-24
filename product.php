<?php
require_once 'auth.php';
require_once 'db.php';

$productId = $_GET['id'] ?? '';

if (empty($productId)) {
    header('Location: shop.php');
    exit;
}

// Fetch product from database
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? LIMIT 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header('Location: shop.php');
        exit;
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Image mapping
$imageMapping = [
    'sabrina3' => ['img/products/p1.jpg', 'img/products/p1s/1.jpg', 'img/products/p1s/2.jpg', 'img/products/p1s/3.jpg', 'img/products/p1s/4.jpg'],
    'jelly' => ['img/products/p2.jpg', 'img/products/p2/1.jpg', 'img/products/p2/2.jpg', 'img/products/p2/3.jpg', 'img/products/p2/4.jpg'],
    'subzone' => ['img/products/p3.jpg', 'img/products/p3s/1.jpg', 'img/products/p3s/2.jpg', 'img/products/p3s/3.jpg', 'img/products/p3s/4.jpg'],
    'radiant' => ['img/products/p4.jpg', 'img/products/p4s/1.jpg', 'img/products/p4s/2.jpg', 'img/products/p4s/3.jpg', 'img/products/p4s/4.jpg'],
    'mvp' => ['img/products/p5.jpg', 'img/products/p5/1.jpg', 'img/products/p5/2.jpg', 'img/products/p5/3.jpg', 'img/products/p5/4.jpg'],
    'world' => ['img/products/p6.jpg', 'img/products/p6/1.jpg', 'img/products/p6/2.jpg', 'img/products/p6/3.jpg', 'img/products/p6/4.jpg'],
    'reaper' => ['img/products/p7.jpg', 'img/products/p7/1.jpg', 'img/products/p7/2.jpg', 'img/products/p7/3.jpg', 'img/products/p7/4.jpg'],
    'alien' => ['img/products/p8.jpg', 'img/products/p8/1.jpg', 'img/products/p8/2.jpg', 'img/products/p8/3.jpg', 'img/products/p8/4.jpg'],
    'vintage' => ['img/products/q1.jpg', 'img/products/q1/1.jpg', 'img/products/q1/2.jpg', 'img/products/q1/3.jpg', 'img/products/q1/4.jpg'],
    'ballet' => ['img/products/q2.jpg', 'img/products/q2/1.jpg', 'img/products/q2/2.jpg', 'img/products/q2/3.jpg', 'img/products/q2/4.jpg'],
    'edge' => ['img/products/q3.jpg', 'img/products/q3/1.jpg', 'img/products/q3/2.jpg', 'img/products/q3/3.jpg', 'img/products/q3/4.jpg'],
    'max' => ['img/products/q4.jpg', 'img/products/q4/1.jpg', 'img/products/q4/2.jpg', 'img/products/q4/3.jpg', 'img/products/q4/4.jpg'],
    'kitty' => ['img/products/q5.jpg', 'img/products/q5/1.jpg', 'img/products/q5/2.jpg', 'img/products/q5/3.jpg', 'img/products/q5/4.jpg'],
    'talk' => ['img/products/q6.jpg', 'img/products/q6/1.jpg', 'img/products/q6/2.jpg', 'img/products/q6/3.jpg', 'img/products/q6/4.jpg'],
    'luxe' => ['img/products/q7.jpg', 'img/products/q7/1.jpg', 'img/products/q7/2.jpg', 'img/products/q7/3.jpg', 'img/products/q7/4.jpg'],
    'galaxy' => ['img/products/q8.jpg', 'img/products/q8/1.jpg', 'img/products/q8/2.jpg', 'img/products/q8/3.jpg', 'img/products/q8/4.jpg']
];

$images = $imageMapping[$productId] ?? [$product['image_url']];

function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

// Get related products
$stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND product_id != ? LIMIT 3");
$stmt->execute([$product['category'], $productId]);
$relatedProducts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']) ?> - BLAZT</title>
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
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-bag-shopping"></i></a></li>
            </ul>
        </div>
    </section>

    <section id="sp">
        <button onclick="goBack()">← Back</button>
    </section>

    <section id="prodetails" class="section-p1">
        <!-- Product Images -->
        <div class="single-pro-im">
            <img src="<?= htmlspecialchars($images[0]) ?>" width="100%" id="mainimg" alt="<?= htmlspecialchars($product['product_name']) ?>">
            <div class="small-im-g">
                <?php foreach ($images as $img): ?>
                    <div class="small-im-c">
                        <img src="<?= htmlspecialchars($img) ?>" width="100%" class="smallimg" alt="">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div class="single-pro-d">
            <h6><?= htmlspecialchars($product['category']) ?></h6>
            <h4><?= strtoupper(htmlspecialchars($product['brand'])) ?> - <?= htmlspecialchars($product['product_name']) ?></h4>
            <h2><?= formatPrice($product['price']) ?></h2>
            
            <select id="sizeSelect">
                <option value="">Select Size</option>
                <option value="39">39</option>
                <option value="40">40</option>
                <option value="41">41</option>
                <option value="42">42</option>
                <option value="43">43</option>
                <option value="44">44</option>
            </select>
            
            <input type="number" id="quantityInput" value="1" min="1" max="<?= $product['stock'] ?>">
            
            <button class="normal" id="addToCartBtn" onclick="addToCartFromProduct()">Add To Cart</button>
            
            <h4>Product Details</h4>
            <span><?= nl2br(htmlspecialchars($product['description'])) ?></span>
            
            <?php if ($product['stock'] > 0): ?>
                <p style="color: #27AE60; margin-top: 15px;">
                    <i class="fa-solid fa-check-circle"></i> Stok tersedia: <?= $product['stock'] ?> unit
                </p>
            <?php else: ?>
                <p style="color: #E74C3C; margin-top: 15px;">
                    <i class="fa-solid fa-times-circle"></i> Stok habis
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Related Products -->

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
        const productId = '<?= $productId ?>';
        const productStock = <?= $product['stock'] ?>;

        // Gallery functionality
        const mainImg = document.getElementById('mainimg');
        const smallImgs = document.querySelectorAll('.smallimg');
        smallImgs.forEach(img => {
            img.onclick = function() {
                mainImg.src = this.src;
            };
        });

        // Add to cart
        function addToCartFromProduct() {
            const size = document.getElementById('sizeSelect').value;
            const quantity = parseInt(document.getElementById('quantityInput').value);
            const btn = document.getElementById('addToCartBtn');
            
            // Validasi
            if (!size || size === '') {
                alert('⚠️ Pilih ukuran terlebih dahulu!');
                document.getElementById('sizeSelect').focus();
                return;
            }
            
            if (quantity < 1 || isNaN(quantity)) {
                alert('⚠️ Jumlah minimal 1');
                document.getElementById('quantityInput').focus();
                return;
            }
            
            if (quantity > productStock) {
                alert('⚠️ Stok tidak mencukupi! Tersedia: ' + productStock + ' unit');
                return;
            }
            
            // Loading state
            btn.textContent = 'Menambahkan...';
            btn.disabled = true;
            btn.style.opacity = '0.6';
            
            // AJAX request
            fetch('cart_update.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'add',
                    product_id: productId,
                    quantity: quantity,
                    size: size
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.textContent = 'Add To Cart';
                btn.disabled = false;
                btn.style.opacity = '1';
                
                if (data.success) {
                    if (confirm('✅ Produk berhasil ditambahkan ke keranjang!\n\nLihat keranjang sekarang?')) {
                        window.location.href = 'cart.php';
                    } else {
                        // Reset form
                        document.getElementById('quantityInput').value = 1;
                    }
                } else {
                    alert('❌ ' + (data.error || 'Gagal menambahkan ke keranjang'));
                }
            })
            .catch(error => {
                btn.textContent = 'Add To Cart';
                btn.disabled = false;
                btn.style.opacity = '1';
                console.error('Error:', error);
                alert('❌ Terjadi kesalahan: ' + error.message);
            });
        }

        function goBack() {
            window.history.length > 1 ? window.history.back() : window.location.href = 'shop.php';
        }
    </script>
</body>
</html>