<?php
require_once 'db.php';

// Ambil item cart user
function getCartItems($userId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.cart_id, c.product_id, c.quantity, c.size, 
               p.product_name, p.brand, p.price, p.image_url, p.stock
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Hitung total harga crat
function getCartTotal($userId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT SUM(c.quantity * p.price) as total
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Hitung jumlah item cart
function getCartCount($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

// Tambah item ke cart.php
function addToCart($userId, $productId, $quantity, $size) {
    global $pdo;
    
    // Cek produk size yang sama
    $stmt = $pdo->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND size = ?");
    $stmt->execute([$userId, $productId, $size]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update quantity
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        return $stmt->execute([$newQty, $existing['cart_id']]);
    } else {
        // Insert baru
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $productId, $quantity, $size]);
    }
}

// Update quantity item cart
function updateCartQuantity($cartId, $userId, $quantity) {
    global $pdo;
    if ($quantity <= 0) {
        return removeFromCart($cartId, $userId);
    }
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
    return $stmt->execute([$quantity, $cartId, $userId]);
}

// Hapus item dari cart
function removeFromCart($cartId, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    return $stmt->execute([$cartId, $userId]);
}

// Kosongkan cart
function clearCart($userId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    return $stmt->execute([$userId]);
}

// Generate unique order id
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

// Buat order baru
function createOrder($userId, $shippingData, $paymentMethod, $notes = '') {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Ambil item cart
        $cartItems = getCartItems($userId);
        if (empty($cartItems)) {
            throw new Exception('Keranjang kosong');
        }
        
        // Hitung total
        $total = getCartTotal($userId);
        
        // Generate order id
        $orderNumber = generateOrderNumber();
        
        // Insert order
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, order_number, total_amount, status, 
                              shipping_address, shipping_city, shipping_postal_code, 
                              shipping_phone, payment_method, notes)
            VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $orderNumber,
            $total,
            $shippingData['address'],
            $shippingData['city'],
            $shippingData['postal_code'],
            $shippingData['phone'],
            $paymentMethod,
            $notes
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, brand, price, quantity, size, subtotal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($cartItems as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['product_name'],
                $item['brand'],
                $item['price'],
                $item['quantity'],
                $item['size'],
                $subtotal
            ]);
        }
        
        // Kosongkan cart
        clearCart($userId);
        
        $pdo->commit();
        return ['success' => true, 'order_number' => $orderNumber, 'order_id' => $orderId];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Ambil order user
function getUserOrders($userId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Ambil detail order
function getOrderDetails($orderId, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    return $stmt->fetch();
}

// Ambil items di order
function getOrderItems($orderId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}

// Format currency
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Status order
function getStatusLabel($status) {
    $labels = [
        'pending' => 'Menunggu Pembayaran',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'delivered' => 'Selesai',
        'cancelled' => 'Dibatalkan'
    ];
    return $labels[$status] ?? $status;
}

// Status color
function getStatusColor($status) {
    $colors = [
        'pending' => '#FFA500',
        'processing' => '#4A90E2',
        'shipped' => '#9B59B6',
        'delivered' => '#27AE60',
        'cancelled' => '#E74C3C'
    ];
    return $colors[$status] ?? '#95A5A6';
}