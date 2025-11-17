<?php

namespace Elevate\Payments;

use Illuminate\Support\ServiceProvider;
use Elevate\Payments\Services\PaymentService;
use Elevate\Payments\Services\PaymentGatewayManager;

class PaymentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register gateway manager first (dependency of PaymentService)
        $this->app->singleton(PaymentGatewayManager::class);
        
        // Register payment service
        $this->app->singleton(PaymentService::class);
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load public routes (admin routes loaded via AdminRoutesRegistrar)
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'payments');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/payments'),
        ], 'payments-views');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Elevate\Payments\Console\Commands\InstallPaymentsCommand::class,
            ]);
        }
    }
}
