<?php
require_once 'auth.php';
require_once 'cart_functions.php';

$userId = $_SESSION['user_id'];
$orderId = intval($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

$order = getOrderDetails($orderId, $userId);
if (!$order) {
    header('Location: orders.php');
    exit;
}

$orderItems = getOrderItems($orderId);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - BLAZT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .back-btn { display: inline-flex; align-items: center; gap: 8px; color: #4A90E2; text-decoration: none; margin-bottom: 20px; font-weight: 600; padding: 10px 15px; border: 2px solid #4A90E2; border-radius: 6px; transition: all 0.3s; }
        .back-btn:hover { background: #4A90E2; color: white; }
        .detail-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 30px; margin-bottom: 20px; }
        .detail-header { display: flex; justify-content: space-between; align-items: start; padding-bottom: 20px; border-bottom: 2px solid #eee; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
        .order-number { font-size: 24px; font-weight: 700; color: #222; margin-bottom: 5px; }
        .order-date { color: #666; font-size: 14px; }
        .status-badge { padding: 10px 20px; border-radius: 25px; font-weight: 600; color: #fff; display: inline-block; font-size: 14px; }
        .status-timeline { display: flex; justify-content: space-between; margin: 30px 0; position: relative; }
        .status-timeline::before { content: ''; position: absolute; top: 20px; left: 0; right: 0; height: 2px; background: #ddd; z-index: 0; }
        .timeline-step { position: relative; z-index: 1; text-align: center; flex: 1; }
        .timeline-dot { width: 40px; height: 40px; border-radius: 50%; background: #ddd; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; }
        .timeline-step.active .timeline-dot { background: #4A90E2; }
        .timeline-step.completed .timeline-dot { background: #27AE60; }
        .timeline-label { font-size: 12px; color: #666; font-weight: 600; }
        .info-section { margin-bottom: 30px; }
        .info-section h3 { font-size: 18px; color: #222; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .info-section h3 i { color: #4A90E2; }
        .info-grid { display: grid; grid-template-columns: 180px 1fr; gap: 15px 20px; }
        .info-label { font-weight: 600; color: #666; }
        .info-value { color: #222; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th, .items-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .items-table th { background: #f9f9f9; font-weight: 600; color: #333; }
        .items-table img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
        .summary-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 8px; color: white; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px; }
        .summary-row.total { font-size: 22px; font-weight: 700; padding-top: 15px; border-top: 2px solid rgba(255,255,255,0.3); margin-top: 10px; }
        .payment-info { background: #FFF9E6; border: 2px solid #FFD700; border-radius: 8px; padding: 20px; margin-top: 20px; }
        .payment-info h4 { color: #D4AF37; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .action-buttons { display: flex; gap: 15px; margin-top: 30px; flex-wrap: wrap; }
        .btn-action { padding: 12px 24px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; border: none; cursor: pointer; transition: all 0.3s; }
        .btn-primary { background: #4A90E2; color: white; }
        .btn-primary:hover { background: #357ABD; }
        .btn-success { background: #27AE60; color: white; }
        .btn-success:hover { background: #229954; }
        .btn-danger { background: #E74C3C; color: white; }
        .btn-danger:hover { background: #C0392B; }
        @media (max-width: 768px) {
            .detail-header { flex-direction: column; }
            .info-grid { grid-template-columns: 1fr; gap: 10px; }
            .status-timeline { flex-direction: column; gap: 20px; }
            .status-timeline::before { display: none; }
            .items-table { font-size: 14px; }
            .items-table img { width: 50px; height: 50px; }
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

    <div class="detail-container">
        <a href="orders.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Pesanan
        </a>

        <div class="detail-card">
            <div class="detail-header">
                <div>
                    <div class="order-number"><?= htmlspecialchars($order['order_number']) ?></div>
                    <div class="order-date">
                        <i class="fa-regular fa-calendar"></i>
                        Dipesan pada <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                    </div>
                </div>
                <div class="status-badge" style="background: <?= getStatusColor($order['status']) ?>">
                    <?= getStatusLabel($order['status']) ?>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="status-timeline">
                <div class="timeline-step <?= in_array($order['status'], ['pending', 'processing', 'shipped', 'delivered']) ? 'completed' : '' ?>">
                    <div class="timeline-dot">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div class="timeline-label">Pesanan Dibuat</div>
                </div>
                <div class="timeline-step <?= in_array($order['status'], ['processing', 'shipped', 'delivered']) ? 'completed' : ($order['status'] === 'pending' ? 'active' : '') ?>">
                    <div class="timeline-dot">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div class="timeline-label">Diproses</div>
                </div>
                <div class="timeline-step <?= in_array($order['status'], ['shipped', 'delivered']) ? 'completed' : ($order['status'] === 'processing' ? 'active' : '') ?>">
                    <div class="timeline-dot">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div class="timeline-label">Dikirim</div>
                </div>
                <div class="timeline-step <?= $order['status'] === 'delivered' ? 'completed' : ($order['status'] === 'shipped' ? 'active' : '') ?>">
                    <div class="timeline-dot">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="timeline-label">Selesai</div>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="info-section">
                <h3><i class="fa-solid fa-truck"></i> Informasi Pengiriman</h3>
                <div class="info-grid">
                    <div class="info-label">Alamat:</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></div>
                    
                    <div class="info-label">Kota:</div>
                    <div class="info-value"><?= htmlspecialchars($order['shipping_city']) ?></div>
                    
                    <div class="info-label">Kode Pos:</div>
                    <div class="info-value"><?= htmlspecialchars($order['shipping_postal_code']) ?></div>
                    
                    <div class="info-label">Telepon:</div>
                    <div class="info-value"><?= htmlspecialchars($order['shipping_phone']) ?></div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="info-section">
                <h3><i class="fa-solid fa-credit-card"></i> Informasi Pembayaran</h3>
                <div class="info-grid">
                    <div class="info-label">Metode:</div>
                    <div class="info-value"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></div>
                    
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <?php if ($order['status'] === 'pending'): ?>
                            <span style="color: #E74C3C; font-weight: 600;">Menunggu Pembayaran</span>
                        <?php else: ?>
                            <span style="color: #27AE60; font-weight: 600;">Sudah Dibayar</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($order['status'] === 'pending'): ?>
                <div class="payment-info">
                    <h4><i class="fa-solid fa-info-circle"></i> Instruksi Pembayaran</h4>
                    <p style="margin: 0; line-height: 1.6;">
                        Silakan lakukan pembayaran ke:<br>
                        <strong>Bank BCA: 1234567890 a.n. BLAZT Store</strong><br>
                        Nominal: <strong><?= formatRupiah($order['total_amount']) ?></strong><br>
                        Setelah pembayaran, konfirmasi melalui WhatsApp: <strong>+62 812-3456-7890</strong>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($order['notes']): ?>
            <div class="info-section">
                <h3><i class="fa-solid fa-note-sticky"></i> Catatan</h3>
                <div class="info-value"><?= nl2br(htmlspecialchars($order['notes'])) ?></div>
            </div>
            <?php endif; ?>

            <!-- Order Items -->
            <div class="info-section">
                <h3><i class="fa-solid fa-box"></i> Produk Pesanan</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): 
                            // Get product image
                            try {
                                $stmt = $pdo->prepare("SELECT image_url FROM products WHERE product_id = ?");
                                $stmt->execute([$item['product_id']]);
                                $imgUrl = $stmt->fetchColumn();
                            } catch (Exception $e) {
                                $imgUrl = 'img/products/default.jpg';
                            }
                        ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="">
                                        <span style="font-weight: 600;"><?= htmlspecialchars($item['product_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($item['brand']) ?></td>
                                <td><?= htmlspecialchars($item['size']) ?></td>
                                <td><?= formatRupiah($item['price']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td style="font-weight: 700; color: #4A90E2;"><?= formatRupiah($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="summary-box">
                <div class="summary-row">
                    <span>Subtotal Produk</span>
                    <span><?= formatRupiah($order['total_amount']) ?></span>
                </div>
                <div class="summary-row">
                    <span>Ongkos Kirim</span>
                    <span>Gratis</span>
                </div>
                <div class="summary-row total">
                    <span>Total Pembayaran</span>
                    <span><?= formatRupiah($order['total_amount']) ?></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <?php if ($order['status'] === 'pending'): ?>
                    <button class="btn-action btn-success" onclick="alert('Silakan lakukan pembayaran sesuai instruksi di atas')">
                        <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                    </button>
                <?php endif; ?>
                
                <?php if ($order['status'] === 'shipped'): ?>
                    <button class="btn-action btn-primary" onclick="alert('Fitur tracking segera hadir!')">
                        <i class="fa-solid fa-truck"></i> Lacak Pengiriman
                    </button>
                <?php endif; ?>
                
                <?php if ($order['status'] === 'delivered'): ?>
                    <button class="btn-action btn-success" onclick="alert('Terima kasih telah berbelanja!')">
                        <i class="fa-solid fa-star"></i> Beri Ulasan
                    </button>
                <?php endif; ?>
                
                <button class="btn-action btn-primary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Cetak Invoice
                </button>
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