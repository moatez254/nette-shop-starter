<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;

final class ProductRepository
{
    public function __construct(private Explorer $explorer)
    {
    }

    /**
     * Get all products with optional search and pagination
     *
     * @param array{page?:int,limit?:int,q?:string} $params
     * @return array<int, array{id:int,name:string,price:float,sku:?string}>
     */
    public function all(array $params = []): array
    {
        $query = $this->explorer->table('products');

        // Apply search filter
        if (isset($params['q']) && $params['q'] !== '') {
            $search = $params['q'];
            $query->where('name LIKE ? OR sku LIKE ?', "%$search%", "%$search%");
        }

        // Apply pagination
        if (isset($params['page']) && isset($params['limit'])) {
            $page = max(1, (int) $params['page']);
            $limit = max(1, min(100, (int) $params['limit']));
            $offset = ($page - 1) * $limit;
            $query->limit($limit, $offset);
        }

        $rows = $query->order('id DESC')->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'    => (int) $r->id,
                'name'  => (string) $r->name,
                'price' => (float) $r->price,
                'sku'   => $r->sku !== null ? (string) $r->sku : null,
            ];
        }
        return $out;
    }

    /**
     * Get total count of products
     *
     * @param string|null $search
     * @return int
     */
    public function count(?string $search = null): int
    {
        $query = $this->explorer->table('products');

        if ($search !== null && $search !== '') {
            $query->where('name LIKE ? OR sku LIKE ?', "%$search%", "%$search%");
        }

        return $query->count('*');
    }

    /**
     * Find product by ID
     *
     * @param int $id
     * @return array{id:int,name:string,price:float,sku:?string}|null
     */
    public function find(int $id): ?array
    {
        $row = $this->explorer->table('products')->get($id);

        if (!$row) {
            return null;
        }

        return [
            'id'    => (int) $row->id,
            'name'  => (string) $row->name,
            'price' => (float) $row->price,
            'sku'   => $row->sku !== null ? (string) $row->sku : null,
        ];
    }

    /**
     * Create a new product
     *
     * @param array{name:string,price:float,sku?:?string} $data
     * @return int The ID of the created product
     */
    public function create(array $data): int
    {
        $row = $this->explorer->table('products')->insert([
            'name'  => $data['name'],
            'price' => $data['price'],
            'sku'   => $data['sku'] ?? null,
        ]);

        return (int) $row->getPrimary();
    }

    /**
     * Update an existing product
     *
     * @param int $id
     * @param array{name?:string,price?:float,sku?:?string} $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['price'])) {
            $updateData['price'] = $data['price'];
        }

        if (isset($data['sku'])) {
            $updateData['sku'] = $data['sku'];
        }

        if (empty($updateData)) {
            return false;
        }

        $affected = $this->explorer->table('products')
            ->where('id', $id)
            ->update($updateData);

        return $affected > 0;
    }

    /**
     * Delete a product
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $affected = $this->explorer->table('products')
            ->where('id', $id)
            ->delete();

        return $affected > 0;
    }

    /**
     * Check if product exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->explorer->table('products')->where('id', $id)->count('*') > 0;
    }

    /**
     * Find products by SKU
     *
     * @param string $sku
     * @return array<int, array{id:int,name:string,price:float,sku:?string}>
     */
    public function findBySku(string $sku): array
    {
        $rows = $this->explorer->table('products')
            ->where('sku', $sku)
            ->order('id DESC')
            ->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'    => (int) $r->id,
                'name'  => (string) $r->name,
                'price' => (float) $r->price,
                'sku'   => $r->sku !== null ? (string) $r->sku : null,
            ];
        }
        return $out;
    }

    /**
     * Get products within price range
     *
     * @param float $minPrice
     * @param float $maxPrice
     * @return array<int, array{id:int,name:string,price:float,sku:?string}>
     */
    public function findByPriceRange(float $minPrice, float $maxPrice): array
    {
        $rows = $this->explorer->table('products')
            ->where('price >= ? AND price <= ?', $minPrice, $maxPrice)
            ->order('price ASC')
            ->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'    => (int) $r->id,
                'name'  => (string) $r->name,
                'price' => (float) $r->price,
                'sku'   => $r->sku !== null ? (string) $r->sku : null,
            ];
        }
        return $out;
    }
}
