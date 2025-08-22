<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Presenter;
use Nette\Application\Responses\JsonResponse;
use Nette\Http\IResponse;

final class HealthPresenter extends Presenter
{
    public function __construct(
        private Nette\Database\Explorer $explorer,
    ) {
        parent::__construct();
    }

    /**
     * Health check endpoint
     * GET /api/health
     */
    public function actionDefault(): void
    {
        $res = $this->getHttpResponse();
        $res->setHeader('Content-Type', 'application/json');
        $res->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $res->setHeader('Pragma', 'no-cache');
        $res->setHeader('Expires', '0');

        try {
            // Check database connection
            $dbStatus = $this->checkDatabaseHealth();

            $healthData = [
                'status' => $dbStatus ? 'ok' : 'degraded',
                'timestamp' => date('c'),
                'version' => '1.0.0',
                'services' => [
                    'database' => $dbStatus ? 'healthy' : 'unhealthy',
                    'api' => 'healthy'
                ],
                'uptime' => $this->getUptime(),
                'memory_usage' => $this->getMemoryUsage()
            ];

            if (!$dbStatus) {
                $res->setCode(IResponse::S503_SERVICE_UNAVAILABLE);
                $healthData['status'] = 'degraded';
            }

            $this->sendResponse(new JsonResponse($healthData));
        } catch (\Exception $e) {
            $res->setCode(IResponse::S503_SERVICE_UNAVAILABLE);
            $this->sendResponse(new JsonResponse([
                'status' => 'error',
                'timestamp' => date('c'),
                'error' => 'Health check failed',
                'message' => $e->getMessage()
            ]));
        }
    }

    /**
     * Check database connection health
     */
    private function checkDatabaseHealth(): bool
    {
        try {
            $this->explorer->query('SELECT 1')->fetch();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get application uptime
     */
    private function getUptime(): string
    {
        $startTime = defined('APP_START_TIME') ? APP_START_TIME : time();
        $uptime = time() - $startTime;

        $days = floor($uptime / 86400);
        $hours = floor(($uptime % 86400) / 3600);
        $minutes = floor(($uptime % 3600) / 60);
        $seconds = $uptime % 60;

        if ($days > 0) {
            return sprintf('%dd %dh %dm %ds', $days, $hours, $minutes, $seconds);
        } elseif ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }

    /**
     * Get memory usage information
     */
    private function getMemoryUsage(): array
    {
        return [
            'current' => $this->formatBytes(memory_get_usage(true)),
            'peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'limit' => $this->formatBytes($this->parseBytes(ini_get('memory_limit') ?: '128M'))
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;
        
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }
        
        return round($bytes, 2) . ' ' . $units[$index];
    }

    /**
     * Parse memory limit string to bytes
     */
    private function parseBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);
        $number = (int) $value;
        
        switch ($last) {
            case 'g':
                $number *= 1024;
            case 'm':
                $number *= 1024;
            case 'k':
                $number *= 1024;
        }
        
        return $number;
    }
} 