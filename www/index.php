<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Simple working example without Nette's problematic configuration
header('Content-Type: application/json');

try {
    // Simple database connection
    $pdo = new PDO('sqlite:' . __DIR__ . '/../var/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get products - using correct column names
    $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $products,
        'count' => count($products)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
 