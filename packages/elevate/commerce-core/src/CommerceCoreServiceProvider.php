<?php

namespace Elevate\CommerceCore;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class CommerceCoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge commerce configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/commerce.php',
            'commerce'
        );

        // Merge auth configuration for staff guard
        $this->mergeAuthConfig();

        // Register Admin Navigation as singleton
        $this->app->singleton('admin.navigation', function ($app) {
            return new \Elevate\CommerceCore\Support\AdminNavigation();
        });

        // Register Dashboard Registry as singleton
        $this->app->singleton(\Elevate\CommerceCore\Dashboard\DashboardRegistry::class, function ($app) {
            return new \Elevate\CommerceCore\Dashboard\DashboardRegistry();
        });

        // Register Settings Registry as singleton
        $this->app->singleton(\Elevate\CommerceCore\Settings\SettingsRegistry::class, function ($app) {
            return new \Elevate\CommerceCore\Settings\SettingsRegistry();
        });

        // Register Currency Service as singleton
        $this->app->singleton(\Elevate\CommerceCore\Services\CurrencyService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register route exclusions for commerce routes
        // This prevents the page catch-all from intercepting commerce routes
        if (class_exists(\App\Routing\Services\RouteExclusionRegistry::class)) {
            \App\Routing\Services\RouteExclusionRegistry::excludeMany([
                'cart',
                'wishlist',
                'checkout',
                'account',
            ]);
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        // - Web routes loaded via CommerceRoutesRegistrar
        // - Admin routes loaded via AdminRoutesRegistrar
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'commerce');

        // Load Blade components
        Blade::componentNamespace('Elevate\\CommerceCore\\View\\Components', 'commerce');
        
        // Register anonymous components from the package
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components');
        
        // Register Blade directives for currency formatting
        $this->registerBladeDirectives();

        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/commerce.php' => config_path('commerce.php'),
        ], 'commerce-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'commerce-migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/commerce'),
        ], 'commerce-views');

        // Register core admin navigation items
        $this->registerNavigation();

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Elevate\CommerceCore\Console\Commands\InstallCommand::class,
            ]);
        }
    }

    /**
     * Register core commerce navigation items.
     */
    protected function registerNavigation(): void
    {
        $nav = $this->app->make('admin.navigation');

        $nav->add('Dashboard', '/admin', [
            'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-4l-2-2H5a2 2 0 00-2 2z',
            'pattern' => 'admin',
            'order' => 10,
        ]);

        $nav->add('Customers', '/admin/customers', [
            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197',
            'pattern' => 'admin/customers',
            'order' => 20,
        ]);

        $nav->add('Orders', '/admin/orders', [
            'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
            'pattern' => 'admin/orders',
            'order' => 30,
        ]);

        $nav->add('Enquiries', '/admin/enquiries', [
            'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'pattern' => 'admin/enquiries',
            'order' => 40,
        ]);

        $nav->add('Tags', '/admin/tags', [
            'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            'pattern' => 'admin/tags',
            'order' => 50,
        ]);

        $nav->group('settings', 'Settings', 900);

        $nav->add('General Settings', '/admin/settings', [
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'pattern' => 'admin/settings',
            'order' => 910,
            'group' => 'settings',
        ]);
    }

    /**
     * Register Blade directives for currency formatting
     */
    protected function registerBladeDirectives(): void
    {
        // @currency directive - formats amount with symbol
        // Usage: @currency($order->total) or @currency($order->total, 'USD')
        Blade::directive('currency', function ($expression) {
            return "<?php echo app(\Elevate\CommerceCore\Services\CurrencyService::class)->format({$expression}); ?>";
        });
        
        // @currencySymbol directive - just the symbol
        // Usage: @currencySymbol or @currencySymbol('USD')
        Blade::directive('currencySymbol', function ($expression) {
            $expression = $expression ?: 'null';
            return "<?php echo app(\Elevate\CommerceCore\Services\CurrencyService::class)->symbol({$expression}); ?>";
        });
    }

    /**
     * Merge authentication configuration for staff guard.
     */
    protected function mergeAuthConfig(): void
    {
        $authConfig = require __DIR__.'/../config/auth.php';

        // Merge guards
        config([
            'auth.guards' => array_merge(
                config('auth.guards', []),
                $authConfig['guards']
            ),
        ]);

        // Merge providers
        config([
            'auth.providers' => array_merge(
                config('auth.providers', []),
                $authConfig['providers']
            ),
        ]);

        // Merge password reset configs
        config([
            'auth.passwords' => array_merge(
                config('auth.passwords', []),
                $authConfig['passwords']
            ),
        ]);
    }
}
