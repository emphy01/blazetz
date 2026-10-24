<?php
require_once 'auth.php';
require_once 'cart_functions.php';

$userId = $_SESSION['user_id'];
$orders = getUserOrders($userId);

// Filter by status if provided
$filterStatus = $_GET['status'] ?? 'all';
if ($filterStatus !== 'all') {
    $orders = array_filter($orders, function($order) use ($filterStatus) {
        return $order['status'] === $filterStatus;
    });
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - BLAZT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        .orders-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .orders-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .orders-header h2 { font-size: 28px; color: #222; margin: 0; }
        .filter-tabs { display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-tab { padding: 10px 20px; border: 2px solid #ddd; background: white; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; color: #666; transition: all 0.3s; }
        .filter-tab:hover { border-color: #4A90E2; color: #4A90E2; }
        .filter-tab.active { background: #4A90E2; color: white; border-color: #4A90E2; }
        .orders-list { display: grid; gap: 20px; }
        .order-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 20px; transition: transform 0.2s, box-shadow 0.2s; }
        .order-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .order-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .order-info h3 { font-size: 18px; color: #222; margin-bottom: 5px; }
        .order-date { font-size: 14px; color: #666; }
        .order-status { padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; color: #fff; }
        .order-details { margin-bottom: 15px; }
        .order-item { display: flex; gap: 15px; padding: 10px 0; border-bottom: 1px solid #f5f5f5; align-items: center; }
        .order-item:last-child { border-bottom: none; }
        .item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
        .item-info { flex: 1; }
        .item-name { font-size: 14px; font-weight: 600; color: #222; margin-bottom: 3px; }
        .item-details { font-size: 13px; color: #666; }
        .item-price { font-weight: 600; color: #4A90E2; }
        .order-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #eee; flex-wrap: wrap; gap: 10px; }
        .order-total { font-size: 18px; font-weight: 700; color: #222; }
        .order-actions { display: flex; gap: 10px; }
        .view-btn { padding: 10px 20px; background: #4A90E2; color: #fff; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-weight: 600; display: inline-block; }
        .view-btn:hover { background: #357ABD; }
        .track-btn { padding: 10px 20px; background: #27AE60; color: #fff; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-weight: 600; display: inline-block; }
        .track-btn:hover { background: #229954; }
        .empty-state { text-align: center; padding: 80px 20px; background: white; border-radius: 8px; }
        .empty-state i { font-size: 64px; color: #ddd; margin-bottom: 20px; }
        .empty-state h3 { color: #666; margin-bottom: 10px; }
        .empty-state .btn { display: inline-block; margin-top: 20px; padding: 12px 24px; background: #4A90E2; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; }
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; }
        .stat-number { font-size: 32px; font-weight: 700; color: #4A90E2; margin-bottom: 5px; }
        .stat-label { font-size: 14px; color: #666; }
        @media (max-width: 768px) {
            .orders-header { flex-direction: column; align-items: flex-start; }
            .filter-tabs { width: 100%; }
            .filter-tab { flex: 1; text-align: center; }
            .order-footer { flex-direction: column; align-items: stretch; }
            .order-actions { width: 100%; }
            .view-btn, .track-btn { width: 100%; text-align: center; }
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
                <li><a class="active" href="orders.php">My Order</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-bag-shopping"></i></a></li>
            </ul>
        </div>
    </section>

    <div class="orders-container">
        <div class="orders-header">
            <div>
                <h2>Pesanan Saya</h2>
                <p style="color: #666; margin-top: 5px;">Total: <?= count($orders) ?> pesanan</p>
            </div>
            <div class="filter-tabs">
                <a href="orders.php?status=all" class="filter-tab <?= $filterStatus === 'all' ? 'active' : '' ?>">
                    Semua
                </a>
                <a href="orders.php?status=pending" class="filter-tab <?= $filterStatus === 'pending' ? 'active' : '' ?>">
                    Pending
                </a>
                <a href="orders.php?status=processing" class="filter-tab <?= $filterStatus === 'processing' ? 'active' : '' ?>">
                    Diproses
                </a>
                <a href="orders.php?status=shipped" class="filter-tab <?= $filterStatus === 'shipped' ? 'active' : '' ?>">
                    Dikirim
                </a>
                <a href="orders.php?status=delivered" class="filter-tab <?= $filterStatus === 'delivered' ? 'active' : '' ?>">
                    Selesai
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($orders, fn($o) => $o['status'] === 'pending')) ?></div>
                <div class="stat-label">Menunggu Pembayaran</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($orders, fn($o) => $o['status'] === 'processing')) ?></div>
                <div class="stat-label">Diproses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($orders, fn($o) => $o['status'] === 'shipped')) ?></div>
                <div class="stat-label">Dikirim</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($orders, fn($o) => $o['status'] === 'delivered')) ?></div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="order-card">
                <div class="empty-state">
                    <i class="fa-solid fa-box-open"></i>
                    <h3><?= $filterStatus === 'all' ? 'Belum Ada Pesanan' : 'Tidak Ada Pesanan dengan Status Ini' ?></h3>
                    <p>Anda belum memiliki riwayat pesanan<?= $filterStatus !== 'all' ? ' dengan status ini' : '' ?></p>
                    <a href="shop.php" class="btn">Mulai Belanja</a>
                </div>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): 
                    $orderItems = getOrderItems($order['order_id']);
                    $itemCount = count($orderItems);
                ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <h3><?= htmlspecialchars($order['order_number']) ?></h3>
                                <div class="order-date">
                                    <i class="fa-regular fa-calendar"></i>
                                    <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                                </div>
                            </div>
                            <div class="order-status" style="background: <?= getStatusColor($order['status']) ?>">
                                <?= getStatusLabel($order['status']) ?>
                            </div>
                        </div>

                        <div class="order-details">
                            <?php 
                            $displayItems = array_slice($orderItems, 0, 2);
                            foreach ($displayItems as $item): 
                                // Fetch product image
                                try {
                                    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE product_id = ?");
                                    $stmt->execute([$item['product_id']]);
                                    $productImg = $stmt->fetchColumn();
                                } catch (Exception $e) {
                                    $productImg = 'img/products/default.jpg';
                                }
                            ?>
                                <div class="order-item">
                                    <img src="<?= htmlspecialchars($productImg) ?>" alt="" class="item-img">
                                    <div class="item-info">
                                        <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                        <div class="item-details">
                                            <?= htmlspecialchars($item['brand']) ?> | 
                                            Qty: <?= $item['quantity'] ?> | 
                                            Size: <?= $item['size'] ?>
                                        </div>
                                    </div>
                                    <div class="item-price"><?= formatRupiah($item['subtotal']) ?></div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if ($itemCount > 2): ?>
                                <div style="text-align: center; padding: 10px; color: #666; font-size: 14px; background: #f9f9f9; border-radius: 4px; margin-top: 10px;">
                                    <i class="fa-solid fa-box"></i> +<?= $itemCount - 2 ?> produk lainnya
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="order-footer">
                            <div class="order-total">
                                Total: <?= formatRupiah($order['total_amount']) ?>
                            </div>
                            <div class="order-actions">
                                <a href="order_detail.php?id=<?= $order['order_id'] ?>" class="view-btn">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                                <?php if ($order['status'] === 'shipped'): ?>
                                    <a href="#" class="track-btn" onclick="alert('Fitur tracking segera hadir!'); return false;">
                                        <i class="fa-solid fa-truck"></i> Lacak
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
        <div class="cpr">
            <p>© 2024 BLAZT. All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>