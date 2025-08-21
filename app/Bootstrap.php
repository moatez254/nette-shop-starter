<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;
use Nette\Bootstrap\Configurator;

final class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator();

        $root = dirname(__DIR__);

        $varDir  = $root . '/var';
        $tempDir = $varDir . '/temp';
        $logDir  = $varDir . '/log';

        @mkdir($tempDir, 0777, true);
        @mkdir($logDir, 0777, true);

        $configurator->setTempDirectory($tempDir);
        $configurator->enableTracy($logDir);

        Dotenv::createImmutable($root)->safeLoad();

        $configurator->addParameters([
            'rootDir' => $root,
            'appDir'  => $root . '/app',
        ]);

        $configurator->addConfig($root . '/app/config/common.neon');

        return $configurator;
    }
}
