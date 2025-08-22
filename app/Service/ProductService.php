<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\ProductRepository;
use App\Validator\ProductValidator;

final class ProductService
{
    public function __construct(
        private ProductRepository $repository,
        private ProductValidator $validator
    ) {
    }

    /**
     * Create a new product
     *
     * @param array{name:string,price:float,sku?:?string} $data
     * @return array{success:bool,id?:int,errors?:array<string,string>}
     */
    public function createProduct(array $data): array
    {
        $validation = $this->validator->validate($data);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors']
            ];
        }

        try {
            $id = $this->repository->create($data);

            return [
                'success' => true,
                'id' => $id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['general' => 'Failed to create product: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Get products with pagination and search
     *
     * @param array{page?:int,limit?:int,q?:string} $params
     * @return array{success:bool,data:array,count:int,pagination:array}|array{success:bool,errors:array<string,string>}
     */
    public function getProducts(array $params = []): array
    {
        // Validate pagination parameters
        $page = max(1, (int) ($params['page'] ?? 1));
        $limit = max(1, min(100, (int) ($params['limit'] ?? 20)));
        $search = trim($params['q'] ?? '');

        if ($page < 1 || $limit < 1 || $limit > 100) {
            return [
                'success' => false,
                'errors' => [
                    'page' => 'Page must be greater than 0',
                    'limit' => 'Limit must be between 1 and 100'
                ]
            ];
        }

        try {
            // Get products with pagination and search from repository
            $products = $this->repository->all([
                'page' => $page,
                'limit' => $limit,
                'q' => $search
            ]);

            // Get total count for pagination info
            $total = $this->repository->count($search);

            $totalPages = (int) ceil($total / $limit);

            return [
                'success' => true,
                'data' => $products,
                'count' => $total,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => $totalPages
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['general' => 'Failed to retrieve products: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Get product by ID
     *
     * @param int $id
     * @return array{success:bool,data?:array,error?:string}
     */
    public function getProductById(int $id): array
    {
        if ($id < 1) {
            return [
                'success' => false,
                'error' => 'Invalid product ID'
            ];
        }

        try {
            $product = $this->repository->find($id);

            if (!$product) {
                return [
                    'success' => false,
                    'error' => 'Product not found'
                ];
            }

            return [
                'success' => true,
                'data' => $product
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to retrieve product: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update product
     *
     * @param int $id
     * @param array{name?:string,price?:float,sku?:?string} $data
     * @return array{success:bool,errors?:array<string,string>}
     */
    public function updateProduct(int $id, array $data): array
    {
        if ($id < 1) {
            return [
                'success' => false,
                'errors' => ['id' => 'Invalid product ID']
            ];
        }

        // Check if product exists
        $existing = $this->getProductById($id);
        if (!$existing['success']) {
            return [
                'success' => false,
                'errors' => ['id' => 'Product not found']
            ];
        }

        // Validate update data
        $validation = $this->validator->validate($data, true);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors']
            ];
        }

        try {
            $this->repository->update($id, $data);

            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['general' => 'Failed to update product: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Delete product
     *
     * @param int $id
     * @return array{success:bool,error?:string}
     */
    public function deleteProduct(int $id): array
    {
        if ($id < 1) {
            return [
                'success' => false,
                'error' => 'Invalid product ID'
            ];
        }

        // Check if product exists
        $existing = $this->getProductById($id);
        if (!$existing['success']) {
            return [
                'success' => false,
                'error' => 'Product not found'
            ];
        }

        try {
            $this->repository->delete($id);

            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to delete product: ' . $e->getMessage()
            ];
        }
    }
}
