<?php

declare(strict_types=1);

namespace App\Validator;

final class ProductValidator
{
    private const MAX_NAME_LENGTH = 255;
    private const MAX_SKU_LENGTH = 100;
    private const MIN_PRICE = 0.0;

    /**
     * Validate product data
     *
     * @param array{name?:string,price?:float,sku?:?string} $data
     * @param bool $isUpdate Whether this is an update operation
     * @return array{valid:bool,errors:array<string,string>}
     */
    public function validate(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        // Validate name
        if (!$isUpdate || isset($data['name'])) {
            $name = $data['name'] ?? '';
            $nameErrors = $this->validateName($name);
            if (!empty($nameErrors)) {
                $errors['name'] = implode(', ', $nameErrors);
            }
        }

        // Validate price
        if (!$isUpdate || isset($data['price'])) {
            $price = $data['price'] ?? null;
            $priceErrors = $this->validatePrice($price);
            if (!empty($priceErrors)) {
                $errors['price'] = implode(', ', $priceErrors);
            }
        }

        // Validate SKU (optional)
        if (isset($data['sku'])) {
            $sku = $data['sku'];
            $skuErrors = $this->validateSku($sku);
            if (!empty($skuErrors)) {
                $errors['sku'] = implode(', ', $skuErrors);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validate product name
     *
     * @param mixed $name
     * @return array<string>
     */
    private function validateName($name): array
    {
        $errors = [];

        if (!is_string($name)) {
            $errors[] = 'Name must be a string';
            return $errors;
        }

        $name = trim($name);

        if ($name === '') {
            $errors[] = 'Name is required and cannot be empty';
        }

        if (strlen($name) > self::MAX_NAME_LENGTH) {
            $errors[] = sprintf('Name cannot exceed %d characters', self::MAX_NAME_LENGTH);
        }

        // Check for valid characters (basic validation)
        if (!preg_match('/^[\p{L}\p{N}\s\-_\.]+$/u', $name)) {
            $errors[] = 'Name contains invalid characters';
        }

        return $errors;
    }

    /**
     * Validate product price
     *
     * @param mixed $price
     * @return array<string>
     */
    private function validatePrice($price): array
    {
        $errors = [];

        if (!is_numeric($price)) {
            $errors[] = 'Price must be a number';
            return $errors;
        }

        $price = (float) $price;

        if ($price < self::MIN_PRICE) {
            $errors[] = sprintf('Price cannot be less than %.2f', self::MIN_PRICE);
        }

        // Check for reasonable maximum price (prevent typos)
        if ($price > 999999.99) {
            $errors[] = 'Price seems unreasonably high';
        }

        // Check decimal places - allow up to 2 decimal places
        $decimalPart = strrchr((string) $price, ".");
        if ($decimalPart !== false) {
            $decimalPlaces = strlen(substr($decimalPart, 1));
            if ($decimalPlaces > 2) {
                $errors[] = 'Price can have maximum 2 decimal places';
            }
        }

        return $errors;
    }

    /**
     * Validate product SKU
     *
     * @param mixed $sku
     * @return array<string>
     */
    private function validateSku($sku): array
    {
        $errors = [];

        // SKU is optional, so null is valid
        if ($sku === null) {
            return $errors;
        }

        if (!is_string($sku)) {
            $errors[] = 'SKU must be a string';
            return $errors;
        }

        $sku = trim($sku);

        // Empty string is valid (will be stored as null)
        if ($sku === '') {
            return $errors;
        }

        if (strlen($sku) > self::MAX_SKU_LENGTH) {
            $errors[] = sprintf('SKU cannot exceed %d characters', self::MAX_SKU_LENGTH);
        }

        // Check for valid SKU format (alphanumeric, hyphens, underscores)
        if (!preg_match('/^[A-Za-z0-9\-_]+$/', $sku)) {
            $errors[] = 'SKU can only contain letters, numbers, hyphens, and underscores';
        }

        return $errors;
    }

    /**
     * Validate pagination parameters
     *
     * @param array{page?:int,limit?:int} $params
     * @return array{valid:bool,errors:array<string,string>}
     */
    public function validatePagination(array $params): array
    {
        $errors = [];

        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 20;

        if (!is_numeric($page) || (int) $page < 1) {
            $errors['page'] = 'Page must be a positive integer';
        }

        if (!is_numeric($limit) || (int) $limit < 1 || (int) $limit > 100) {
            $errors['limit'] = 'Limit must be between 1 and 100';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validate search query
     *
     * @param mixed $query
     * @return array{valid:bool,errors:array<string,string>}
     */
    public function validateSearchQuery($query): array
    {
        $errors = [];

        if ($query !== null && !is_string($query)) {
            $errors['query'] = 'Search query must be a string';
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }

        if (is_string($query) && strlen(trim($query)) > 100) {
            $errors['query'] = 'Search query cannot exceed 100 characters';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validate product ID
     *
     * @param mixed $id
     * @return array{valid:bool,errors:array<string,string>}
     */
    public function validateId($id): array
    {
        $errors = [];

        if (!is_numeric($id)) {
            $errors['id'] = 'ID must be a number';
        } elseif ((int) $id < 1) {
            $errors['id'] = 'ID must be a positive integer';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
