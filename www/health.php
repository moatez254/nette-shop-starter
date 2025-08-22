<?php

declare(strict_types=1);

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Simple database health check
    $dbPath = __DIR__ . '/../var/database.sqlite';
    $dbStatus = false;
    
    if (file_exists($dbPath)) {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $result = $pdo->query('SELECT 1')->fetch();
        $dbStatus = $result !== false;
    }
    
    // Calculate uptime
    $startTime = defined('APP_START_TIME') ? APP_START_TIME : time();
    $uptime = time() - $startTime;
    
    $days = floor($uptime / 86400);
    $hours = floor(($uptime % 86400) / 3600);
    $minutes = floor(($uptime % 3600) / 60);
    $seconds = $uptime % 60;
    
    if ($days > 0) {
        $uptimeStr = sprintf('%dd %dh %dm %ds', $days, $hours, $minutes, $seconds);
    } elseif ($hours > 0) {
        $uptimeStr = sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
    } elseif ($minutes > 0) {
        $uptimeStr = sprintf('%dm %ds', $minutes, $seconds);
    } else {
        $uptimeStr = sprintf('%ds', $seconds);
    }
    
    // Memory usage
    $memoryUsage = [
        'current' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
        'peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
        'limit' => ini_get('memory_limit') ?: '128M'
    ];
    
    $healthData = [
        'status' => $dbStatus ? 'ok' : 'degraded',
        'timestamp' => date('c'),
        'version' => '1.0.0',
        'services' => [
            'database' => $dbStatus ? 'healthy' : 'unhealthy',
            'api' => 'healthy'
        ],
        'uptime' => $uptimeStr,
        'memory_usage' => $memoryUsage
    ];
    
    if (!$dbStatus) {
        http_response_code(503);
        $healthData['status'] = 'degraded';
    }
    
    echo json_encode($healthData, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(503);
    echo json_encode([
        'status' => 'error',
        'timestamp' => date('c'),
        'error' => 'Health check failed',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
} 