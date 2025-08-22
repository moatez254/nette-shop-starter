<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Model\ProductRepository;
use App\Service\ProductService;
use App\Validator\ProductValidator;
use Nette\Database\Connection;
use Nette\Database\Explorer;
use Nette\Database\Structure;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Caching\Storages\DevNullStorage;

final class ProductApiIntegrationTest extends TestCase
{
    private ?ProductService $service = null;
    private ?ProductRepository $repository = null;
    private ?ProductValidator $validator = null;
    private ?Explorer $explorer = null;

    protected function setUp(): void
    {
        // Setup in-memory SQLite database for integration testing
        $db = new Connection('sqlite::memory:');
        $cache = new DevNullStorage();
        $structure = new Structure($db, $cache);
        $conventions = new DiscoveredConventions($structure);
        $this->explorer = new Explorer($db, $structure, $conventions, $cache);

        // Create test table with proper schema
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

    public function testCompleteProductWorkflow(): void
    {
        // 1. Create a product
        $productData = [
            'name' => 'Integration Test Product',
            'price' => 99.99,
            'sku' => 'INTEG-001'
        ];

        $createResult = $this->service->createProduct($productData);

        $this->assertTrue($createResult['success']);
        $this->assertArrayHasKey('id', $createResult);
        $productId = $createResult['id'];

        // 2. Retrieve the created product
        $getResult = $this->service->getProductById($productId);

        $this->assertTrue($getResult['success']);
        $this->assertArrayHasKey('data', $getResult);
        $this->assertEquals($productData['name'], $getResult['data']['name']);
        $this->assertEquals($productData['price'], $getResult['data']['price']);
        $this->assertEquals($productData['sku'], $getResult['data']['sku']);

        // 3. List all products
        $listResult = $this->service->getProducts();

        $this->assertTrue($listResult['success']);
        $this->assertArrayHasKey('data', $listResult);
        $this->assertArrayHasKey('count', $listResult);
        $this->assertArrayHasKey('pagination', $listResult);
        $this->assertGreaterThan(0, $listResult['count']);

        // 4. Update the product
        $updateData = [
            'name' => 'Updated Integration Test Product',
            'price' => 149.99
        ];

        $updateResult = $this->service->updateProduct($productId, $updateData);

        $this->assertTrue($updateResult['success']);

        // 5. Verify the update
        $updatedProduct = $this->service->getProductById($productId);

        $this->assertTrue($updatedProduct['success']);
        $this->assertEquals($updateData['name'], $updatedProduct['data']['name']);
        $this->assertEquals($updateData['price'], $updatedProduct['data']['price']);
        $this->assertEquals($productData['sku'], $updatedProduct['data']['sku']); // SKU unchanged

        // 6. Delete the product
        $deleteResult = $this->service->deleteProduct($productId);

        $this->assertTrue($deleteResult['success']);

        // 7. Verify deletion
        $deletedProduct = $this->service->getProductById($productId);

        $this->assertFalse($deletedProduct['success']);
        $this->assertEquals('Product not found', $deletedProduct['error']);
    }

    public function testProductSearchAndPagination(): void
    {
        // Create multiple test products
        $products = [
            ['name' => 'Red Lamp', 'price' => 29.99, 'sku' => 'LAMP-RED'],
            ['name' => 'Blue Lamp', 'price' => 34.99, 'sku' => 'LAMP-BLUE'],
            ['name' => 'Green Chair', 'price' => 199.99, 'sku' => 'CHAIR-GREEN'],
            ['name' => 'Yellow Table', 'price' => 299.99, 'sku' => 'TABLE-YELLOW'],
            ['name' => 'Purple Lamp', 'price' => 39.99, 'sku' => 'LAMP-PURPLE'],
        ];

        foreach ($products as $product) {
            $this->service->createProduct($product);
        }

        // Test search functionality
        $searchResult = $this->service->getProducts(['q' => 'lamp']);

        $this->assertTrue($searchResult['success']);
        $this->assertCount(3, $searchResult['data']); // Should find 3 lamps

        // Test pagination
        $paginationResult = $this->service->getProducts(['page' => 1, 'limit' => 2]);

        $this->assertTrue($paginationResult['success']);
        $this->assertCount(2, $paginationResult['data']);
        $this->assertEquals(5, $paginationResult['count']);
        $this->assertEquals(3, $paginationResult['pagination']['pages']); // 5 items, 2 per page = 3 pages

        // Test second page
        $page2Result = $this->service->getProducts(['page' => 2, 'limit' => 2]);

        $this->assertTrue($page2Result['success']);
        $this->assertCount(2, $page2Result['data']);
        $this->assertEquals(2, $page2Result['pagination']['page']);
    }

    public function testValidationIntegration(): void
    {
        // Test invalid product creation
        $invalidData = [
            'name' => '', // Empty name
            'price' => -10.00 // Negative price
        ];

        $result = $this->service->createProduct($invalidData);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('name', $result['errors']);
        $this->assertArrayHasKey('price', $result['errors']);

        // Test invalid pagination
        $invalidPagination = $this->service->getProducts(['page' => -1, 'limit' => 0]);

        $this->assertTrue($invalidPagination['success']); // Service normalizes invalid values
        $this->assertArrayHasKey('data', $invalidPagination);
        $this->assertArrayHasKey('pagination', $invalidPagination);

        // Check that values were normalized
        $this->assertEquals(1, $invalidPagination['pagination']['page']); // -1 becomes 1
        $this->assertEquals(1, $invalidPagination['pagination']['limit']); // 0 becomes 1
    }

    public function testErrorHandling(): void
    {
        // Test getting non-existent product
        $nonExistent = $this->service->getProductById(99999);

        $this->assertFalse($nonExistent['success']);
        $this->assertEquals('Product not found', $nonExistent['error']);

        // Test updating non-existent product
        $updateNonExistent = $this->service->updateProduct(99999, ['name' => 'New Name']);

        $this->assertFalse($updateNonExistent['success']);
        $this->assertArrayHasKey('errors', $updateNonExistent);

        // Test deleting non-existent product
        $deleteNonExistent = $this->service->deleteProduct(99999);

        $this->assertFalse($deleteNonExistent['success']);
        $this->assertArrayHasKey('error', $deleteNonExistent);
    }

    public function testDataConsistency(): void
    {
        // Create a product
        $productData = [
            'name' => 'Consistency Test Product',
            'price' => 75.50,
            'sku' => 'CONS-001'
        ];

        $created = $this->service->createProduct($productData);
        $productId = $created['id'];

        // Verify data types and values
        $product = $this->service->getProductById($productId);

        $this->assertIsInt($product['data']['id']);
        $this->assertIsString($product['data']['name']);
        $this->assertIsFloat($product['data']['price']);
        $this->assertIsString($product['data']['sku']);

        $this->assertEquals($productData['name'], $product['data']['name']);
        $this->assertEquals($productData['price'], $product['data']['price']);
        $this->assertEquals($productData['sku'], $product['data']['sku']);
    }

    protected function tearDown(): void
    {
        // Clean up any test data if needed
        if ($this->explorer) {
            $this->explorer->query('DELETE FROM products');
        }

        $this->explorer = null;
        $this->repository = null;
        $this->validator = null;
        $this->service = null;
    }
}
