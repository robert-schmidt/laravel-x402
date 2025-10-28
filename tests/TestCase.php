<?php

namespace X402\LaravelX402\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use X402\LaravelX402\X402ServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup
    }

    protected function getPackageProviders($app): array
    {
        return [
            X402ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Set up test environment
        $app['config']->set('x402.wallet_address', '0x1234567890123456789012345678901234567890');
        $app['config']->set('x402.defaults.network', 'base-sepolia');
        $app['config']->set('x402.logging.enabled', false);
        $app['config']->set('x402.cache.enabled', false);
    }
}
