<?php
declare(strict_types=1);

use App\Model\ProductRepository;
use Nette\Database\Connection;
use Nette\Database\Explorer;
use Nette\Database\Structure;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Caching\Storages\DevNullStorage;
use PHPUnit\Framework\TestCase;

final class ProductRepositoryTest extends TestCase
{
    private ProductRepository $repo;

    protected function setUp(): void
    {
        $db = new Connection('sqlite::memory:');

        $cache = new DevNullStorage();
        $structure = new Structure($db, $cache);

        $conventions = new DiscoveredConventions($structure);
        $explorer = new Explorer($db, $structure, $conventions, $cache);

        $db->query('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, price REAL, sku TEXT)');
        $this->repo = new ProductRepository($explorer);
    }

    public function testCreateAndFind(): void
    {
        $id = $this->repo->create(['name' => 'Test', 'price' => 9.99, 'sku' => 'T-1']);
        $found = $this->repo->find($id);
        $this->assertIsArray($found);
        $this->assertSame('Test', $found['name']);
    }
}
