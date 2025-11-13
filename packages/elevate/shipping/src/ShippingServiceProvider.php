<?php

namespace Elevate\Shipping;

use Illuminate\Support\ServiceProvider;
use Elevate\Shipping\Services\ShippingService;

class ShippingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ShippingService::class);
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        // Load public routes (admin routes loaded via AdminRoutesRegistrar)
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'shipping');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/shipping'),
        ], 'shipping-views');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Elevate\Shipping\Console\Commands\InstallShippingCommand::class,
            ]);
        }
    }
}
