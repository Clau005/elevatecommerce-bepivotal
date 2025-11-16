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
        
        $this->mergeConfigFrom(
            __DIR__.'/../config/stripe.php', 'stripe'
        );
        
        $this->mergeConfigFrom(
            __DIR__.'/../config/paypal.php', 'paypal'
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
            __DIR__.'/../config/stripe.php' => config_path('stripe.php'),
            __DIR__.'/../config/paypal.php' => config_path('paypal.php'),
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
                \ElevateCommerce\Purchasable\Console\Commands\InstallCommand::class,
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

        // Register settings page
        \ElevateCommerce\Core\Support\Settings\SettingsRegistry::register('payments', [
            'title' => 'Payment Gateways',
            'description' => 'Configure payment methods and credentials',
            'icon' => 'fas fa-credit-card',
            'route' => 'admin.settings.payments',
            'group' => 'general',
            'order' => 10,
        ]);
    }
}
