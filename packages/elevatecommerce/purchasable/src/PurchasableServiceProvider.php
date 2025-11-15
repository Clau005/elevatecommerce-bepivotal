<?php

namespace ElevateCommerce\Purchasable;

use Illuminate\Support\ServiceProvider;

class PurchasableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/purchasable.php', 'purchasable'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/purchasable.php' => config_path('purchasable.php'),
        ], 'purchasable-config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'purchasable');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/purchasable'),
        ], 'purchasable-views');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Add commands here
            ]);
        }

        // Register navigation
        $this->registerNavigation();
    }

    /**
     * Register navigation items
     */
    protected function registerNavigation(): void
    {
        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Orders',
            'icon' => 'fas fa-shopping-cart',
            'route' => 'admin.orders.index',
            'order' => 15,
        ]);
    }
}
