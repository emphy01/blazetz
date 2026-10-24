<?php
header('Content-Type: application/json');
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT product_id, product_name, brand, image_url FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
    
    $result = array_map(function($p) {
        return [
            'id' => $p['product_id'],
            'title' => $p['product_name'],
            'brand' => $p['brand'],
            'img' => $p['image_url']
        ];
    }, $products);
    
    echo json_encode([
        'success' => true,
        'products' => $result
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}