<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    use Nette\StaticClass;

    public static function create(): RouteList
    {
        $router = new RouteList;

        // --- API Routes (most specific first) ---
        $router->addRoute('api/health', 'Health:default');                    // GET /api/health
        $router->addRoute('api/products', 'Api:products');                    // GET|POST /api/products
        $router->addRoute('api/products/<id>', 'Api:product');                // GET /api/products/{id}
        $router->addRoute('api/_debug/db', 'Api:debugDb');                    // GET /api/_debug/db

        return $router;
    }
}
