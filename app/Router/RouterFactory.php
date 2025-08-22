<?php

declare(strict_types=1);

namespace App\Router;

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

final class RouterFactory
{
    public static function create(): RouteList
    {
        $router = new RouteList();

        // --- API Routes (most specific first) ---
        $router->addRoute('api/health', 'Api:health');                     // GET  /api/health
        $router->addRoute('api/products', 'Api:products');                 // GET|POST /api/products
        $router->addRoute('api/products/<id>', 'Api:product');             // GET /api/products/{id}
        $router->addRoute('api/_debug/db', 'Api:debugDb');                 // GET /api/_debug/db

        // --- Home page → products list ---
        $router->addRoute('', 'Api:products');                             // GET  /

        // --- General fallback with clear default ---
        $router->addRoute('<presenter>/<action>[/<id>]', 'Api:products');

        return $router;
    }
}
