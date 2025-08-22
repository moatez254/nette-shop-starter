<?php

declare(strict_types=1);

namespace Tests\Bootstrap;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener as PHPUnitTestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

final class TestListener implements PHPUnitTestListener
{
    use TestListenerDefaultImplementation;

    private string $appDir;

    public function __construct(array $options = [])
    {
        $this->appDir = $options['appDir'] ?? 'app';
    }

    public function startTestSuite(TestSuite $suite): void
    {
        // Setup test environment
        if (!defined('APP_DIR')) {
            define('APP_DIR', $this->appDir);
        }

        if (!defined('TEMP_DIR')) {
            define('TEMP_DIR', sys_get_temp_dir() . '/nette-tests');
        }

        if (!defined('LOG_DIR')) {
            define('LOG_DIR', TEMP_DIR . '/log');
        }

        // Create necessary directories
        if (!is_dir(TEMP_DIR)) {
            mkdir(TEMP_DIR, 0777, true);
        }

        if (!is_dir(LOG_DIR)) {
            mkdir(LOG_DIR, 0777, true);
        }
    }

    public function endTestSuite(TestSuite $suite): void
    {
        // Cleanup if needed
    }

    public function startTest(Test $test): void
    {
        // Setup before each test
    }

    public function endTest(Test $test, float $time): void
    {
        // Cleanup after each test
    }
}
