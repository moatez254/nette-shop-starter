<?php

declare(strict_types=1);

namespace App\Router;

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

final class RouterFactory
{
    public function create(): RouteList
    {
        $router = new RouteList();
        $router->addRoute('api/products[/<id>]', 'Api:products');
        return $router;
    }
}
