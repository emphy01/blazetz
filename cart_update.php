<?php
/**
 * cart_update.php – AJAX endpoint for cart actions (add / update / remove).
 * CSRF token is required for every state-changing request.
 */
ob_start();
require_once 'functions.php'; // starts session with secure cookie params
ob_clean();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Login required']);
    exit;
}

// CSRF verification for every POST action
if (!verify_csrf($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

require_once 'cart_functions.php';

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $productId = intval($_POST['product_id'] ?? 0);
            $quantity  = intval($_POST['quantity'] ?? 1);
            $size      = trim($_POST['size'] ?? '');

            if ($productId <= 0)   throw new Exception('Product ID required');
            if ($quantity <= 0)    throw new Exception('Quantity must be greater than 0');
            if ($size === '')      throw new Exception('Please select a size');

            $result = addToCart($userId, $productId, $quantity, $size);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Product added to cart']);
            } else {
                throw new Exception('Failed to add to cart');
            }
            break;

        case 'update':
            $cartId   = intval($_POST['cart_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 1);

            if ($cartId <= 0 || $quantity < 0) throw new Exception('Invalid parameters');

            $result = updateCartQuantity($cartId, $userId, $quantity);
            echo json_encode(['success' => (bool)$result]);
            break;

        case 'remove':
            $cartId = intval($_POST['cart_id'] ?? 0);

            if ($cartId <= 0) throw new Exception('Invalid cart ID');

            $result = removeFromCart($cartId, $userId);
            echo json_encode(['success' => (bool)$result]);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log('cart_update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
ob_end_flush();