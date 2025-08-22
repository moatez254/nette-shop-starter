<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\ProductRepository;
use App\Service\ProductService;
use App\Validator\ProductValidator;
use Nette;
use Nette\Application\UI\Presenter;
use Nette\Application\Responses\JsonResponse;
use Nette\Http\IResponse;

final class ApiPresenter extends Presenter
{
    public function __construct(
        private ProductRepository $repo,
        private Nette\Database\Explorer $explorer,
        private ProductService $productService,
        private ProductValidator $validator,
    ) {
        parent::__construct();
    }

    /**
     * Health check endpoint
     * GET /api/health
     */
    public function actionHealth(): void
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
     * /api/products
     * GET  -> قائمة المنتجات
     * POST -> إضافة منتج {name, price, sku?}
     */
    public function actionProducts(): void
    {
        $req = $this->getHttpRequest();
        $res = $this->getHttpResponse();
        $res->setHeader('Content-Type', 'application/json');

        if ($req->getMethod() === 'POST') {
            $this->handleCreateProduct($req, $res);
            return;
        }

        // GET - List products
        $this->handleListProducts($req, $res);
    }

    /**
     * /api/products/{id}
     * GET -> Get product by ID
     */
    public function actionProduct(int $id): void
    {
        $res = $this->getHttpResponse();
        $res->setHeader('Content-Type', 'application/json');

        $result = $this->productService->getProductById($id);

        if (!$result['success']) {
            if (isset($result['error']) && $result['error'] === 'Product not found') {
                $res->setCode(IResponse::S404_NOT_FOUND);
                $this->sendResponse(new JsonResponse([
                    'error' => 'Product not found',
                    'code' => 404,
                    'timestamp' => date('c')
                ]));
                return;
            }

            $res->setCode(IResponse::S422_UNPROCESSABLE_ENTITY);
            $this->sendResponse(new JsonResponse([
                'error' => 'Validation failed',
                'code' => 422,
                'details' => $result['errors'] ?? ['general' => 'Unknown error'],
                'timestamp' => date('c')
            ]));
            return;
        }

        $this->sendResponse(new JsonResponse([
            'success' => true,
            'data' => $result['data']
        ]));
    }

    /**
     * Handle product creation
     */
    private function handleCreateProduct(Nette\Http\Request $req, Nette\Http\Response $res): void
    {
        $payload = json_decode((string) $req->getRawBody(), true);

        if (!is_array($payload)) {
            $res->setCode(IResponse::S400_BAD_REQUEST);
            $this->sendResponse(new JsonResponse([
                'error' => 'Invalid payload',
                'code' => 400,
                'timestamp' => date('c')
            ]));
            return;
        }

        $result = $this->productService->createProduct($payload);

        if (!$result['success']) {
            $res->setCode(IResponse::S422_UNPROCESSABLE_ENTITY);
            $this->sendResponse(new JsonResponse([
                'error' => 'Validation failed',
                'code' => 422,
                'details' => $result['errors'],
                'timestamp' => date('c')
            ]));
            return;
        }

        $res->setCode(IResponse::S201_CREATED);
        $this->sendResponse(new JsonResponse([
            'success' => true,
            'id' => $result['id'],
            'message' => 'Product created successfully'
        ]));
    }

    /**
     * Handle product listing
     */
    private function handleListProducts(Nette\Http\Request $req, Nette\Http\Response $res): void
    {
        $params = [
            'page' => $req->getQuery('page'),
            'limit' => $req->getQuery('limit'),
            'q' => $req->getQuery('q')
        ];

        $result = $this->productService->getProducts($params);

        if (!$result['success']) {
            $res->setCode(IResponse::S422_UNPROCESSABLE_ENTITY);
            $this->sendResponse(new JsonResponse([
                'error' => 'Validation failed',
                'code' => 422,
                'details' => $result['errors'],
                'timestamp' => date('c')
            ]));
            return;
        }

        $this->sendResponse(new JsonResponse($result));
    }

    /**
     * Check database health
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
            'limit' => $this->formatBytes(ini_get('memory_limit') ?: '128M')
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(string|int $bytes): string
    {
        if (is_string($bytes)) {
            $bytes = (int) $bytes;
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /** /api/_debug/db — فحص اتصال SQLite وملف قاعدة البيانات */
    public function actionDebugDb(): void
    {
        $dsn = $this->explorer->getConnection()->getDsn();
        $file = parse_url($dsn, PHP_URL_PATH) ?: '';
        $this->sendResponse(new JsonResponse([
            'dsn'      => $dsn,
            'file'     => $file,
            'exists'   => $file ? file_exists($file) : false,
            'writable' => $file ? is_writable($file) : false,
        ]));
    }
}
