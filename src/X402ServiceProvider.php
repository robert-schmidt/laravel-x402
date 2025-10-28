<?php

namespace X402\LaravelX402;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use X402\LaravelX402\Client\FacilitatorClient;
use X402\LaravelX402\Middleware\X402PaymentRequired;

class X402ServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/x402.php',
            'x402'
        );

        // Register the FacilitatorClient as a singleton
        $this->app->singleton(FacilitatorClient::class, function ($app) {
            return new FacilitatorClient();
        });

        // Register the facade alias
        $this->app->alias(FacilitatorClient::class, 'x402');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/x402.php' => config_path('x402.php'),
        ], 'x402-config');

        // Register middleware
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('x402', X402PaymentRequired::class);

        // Register exception handler
        if ($this->app->bound('Illuminate\Contracts\Debug\ExceptionHandler')) {
            $this->registerExceptionHandler();
        }
    }

    /**
     * Register exception handler for PaymentRequiredException.
     */
    protected function registerExceptionHandler(): void
    {
        $handler = $this->app->make('Illuminate\Contracts\Debug\ExceptionHandler');

        if (method_exists($handler, 'renderable')) {
            $handler->renderable(function (\X402\LaravelX402\Exceptions\PaymentRequiredException $e) {
                return $e->render();
            });
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            FacilitatorClient::class,
            'x402',
        ];
    }
}
