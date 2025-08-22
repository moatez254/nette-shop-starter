<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Define APP_START_TIME for uptime calculation
if (!defined('APP_START_TIME')) {
    define('APP_START_TIME', time());
}

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Bootstrap the Nette application
$configurator = new Nette\Bootstrap\Configurator;

// Enable debugger in development
$configurator->enableTracy(__DIR__ . '/../var/log');

// Set temp directory
$configurator->setTempDirectory(__DIR__ . '/../var/temp');

// Add configuration files
$configurator->addConfig(__DIR__ . '/../app/config/common.neon');

// Create DI container
$container = $configurator->createContainer();

// Run the application
$application = $container->getByType(Nette\Application\Application::class);
$application->run();
 