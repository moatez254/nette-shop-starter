<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

/**
 * Simple repository for products
 */
final class ProductRepository
{
    public function __construct(private Explorer $db)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $rows = $this->db->table('products')->order('id')->fetchAll();
        return array_map(
            static fn($r): array => $r->toArray(),
            $rows
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $row = $this->db->table('products')->get($id);
        return $row ? $row->toArray() : null;
    }

    /**
     * @param array{name:string,price:float,sku?:string|null} $data
     */
    public function create(array $data): int
    {
        /** @var ActiveRow $row */
        $row = $this->db->table('products')->insert([
            'name'  => (string) $data['name'],
            'price' => (float) $data['price'],
            'sku'   => $data['sku'] ?? null,
        ]);

        $primary = $row->getPrimary(); // mixed
        return (int) (is_array($primary) ? reset($primary) : $primary);
    }
}
