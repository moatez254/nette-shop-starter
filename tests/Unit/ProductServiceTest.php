<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Model\ProductRepository;
use App\Service\ProductService;
use App\Validator\ProductValidator;
use PHPUnit\Framework\TestCase;
use Nette\Database\Connection;
use Nette\Database\Explorer;
use Nette\Database\Structure;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Caching\Storages\DevNullStorage;

final class ProductServiceTest extends TestCase
{
    private ProductService $service;
    private ProductRepository $repository;
    private ProductValidator $validator;
    private Explorer $explorer;

    protected function setUp(): void
    {
        // Setup in-memory SQLite database
        $db = new Connection('sqlite::memory:');
        $cache = new DevNullStorage();
        $structure = new Structure($db, $cache);
        $conventions = new DiscoveredConventions($structure);
        $this->explorer = new Explorer($db, $structure, $conventions, $cache);

        // Create test table
        $this->explorer->query('
            CREATE TABLE products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                price REAL NOT NULL,
                sku TEXT
            )
        ');

        $this->repository = new ProductRepository($this->explorer);
        $this->validator = new ProductValidator();
        $this->service = new ProductService($this->repository, $this->validator);
    }

    public function testCreateProductWithValidData(): void
    {
        $data = [
            'name' => 'Test Product',
            'price' => 29.99,
            'sku' => 'TEST-001'
        ];

        $result = $this->service->createProduct($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
        $this->assertGreaterThan(0, $result['id']);
    }

    public function testCreateProductWithoutSku(): void
    {
        $data = [
            'name' => 'Product Without SKU',
            'price' => 19.99
        ];

        $result = $this->service->createProduct($data);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('id', $result);
    }

    public function testCreateProductWithInvalidName(): void
    {
        $data = [
            'name' => '', // Empty name
            'price' => 29.99
        ];

        $result = $this->service->createProduct($data);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    public function testCreateProductWithInvalidPrice(): void
    {
        $data = [
            'name' => 'Valid Product',
            'price' => -10.00 // Negative price
        ];

        $result = $this->service->createProduct($data);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('price', $result['errors']);
    }

    public function testCreateProductWithZeroPrice(): void
    {
        $data = [
            'name' => 'Free Product',
            'price' => 0.00
        ];

        $result = $this->service->createProduct($data);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('id', $result);
    }

    public function testCreateProductWithVeryLongName(): void
    {
        $data = [
            'name' => str_repeat('A', 300), // Name too long
            'price' => 29.99
        ];

        $result = $this->service->createProduct($data);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
    }

    public function testCreateProductWithInvalidSku(): void
    {
        $data = [
            'name' => 'Valid Product',
            'price' => 29.99,
            'sku' => str_repeat('A', 150) // SKU too long
        ];

        $result = $this->service->createProduct($data);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('sku', $result['errors']);
    }

    public function testGetProductsWithPagination(): void
    {
        // Create some test products
        $this->createTestProducts();

        $result = $this->service->getProducts(['page' => 1, 'limit' => 5]);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertCount(5, $result['data']);
        $this->assertEquals(10, $result['count']);
    }

    public function testGetProductsWithSearch(): void
    {
        // Create some test products
        $this->createTestProducts();

        $result = $this->service->getProducts(['q' => 'lamp']);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertGreaterThan(0, count($result['data']));
    }

    public function testGetProductsWithInvalidPagination(): void
    {
        $result = $this->service->getProducts(['page' => -1, 'limit' => 0]);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']); // Service normalizes invalid values
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);

        // Check that values were normalized
        $this->assertEquals(1, $result['pagination']['page']); // -1 becomes 1
        $this->assertEquals(1, $result['pagination']['limit']); // 0 becomes 1
    }

    public function testGetProductById(): void
    {
        // Create a test product
        $productData = ['name' => 'Test Product', 'price' => 29.99];
        $created = $this->service->createProduct($productData);
        $productId = $created['id'];

        $result = $this->service->getProductById($productId);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals($productId, $result['data']['id']);
        $this->assertEquals('Test Product', $result['data']['name']);
    }

    public function testGetProductByInvalidId(): void
    {
        $result = $this->service->getProductById(99999);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Product not found', $result['error']);
    }

    private function createTestProducts(): void
    {
        $products = [
            ['name' => 'Desk Lamp', 'price' => 49.99, 'sku' => 'LAMP-001'],
            ['name' => 'Office Chair', 'price' => 199.99, 'sku' => 'CHAIR-001'],
            ['name' => 'Table Lamp', 'price' => 39.99, 'sku' => 'LAMP-002'],
            ['name' => 'Computer Desk', 'price' => 299.99, 'sku' => 'DESK-001'],
            ['name' => 'Wall Lamp', 'price' => 79.99, 'sku' => 'LAMP-003'],
            ['name' => 'Gaming Chair', 'price' => 249.99, 'sku' => 'CHAIR-002'],
            ['name' => 'Floor Lamp', 'price' => 89.99, 'sku' => 'LAMP-004'],
            ['name' => 'Standing Desk', 'price' => 399.99, 'sku' => 'DESK-002'],
            ['name' => 'Ceiling Lamp', 'price' => 129.99, 'sku' => 'LAMP-005'],
            ['name' => 'Ergonomic Chair', 'price' => 349.99, 'sku' => 'CHAIR-003'],
        ];

        foreach ($products as $product) {
            $this->service->createProduct($product);
        }
    }
}
