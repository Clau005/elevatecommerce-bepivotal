<?php

namespace ElevateCommerce\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/core.php', 'core'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/core.php' => config_path('core.php'),
        ], 'core-config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'core');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/core'),
        ], 'core-views');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \ElevateCommerce\Core\Console\Commands\InstallCommand::class,
            ]);
        }

        // Register default admin navigation
        $this->registerNavigation();

        // Register default dashboard widgets
        $this->registerDashboardWidgets();

        // Register default settings pages
        $this->registerSettingsPages();
    }

    /**
     * Register default navigation items
     */
    protected function registerNavigation(): void
    {
        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Home',
            'icon' => 'fas fa-home',
            'route' => 'admin.dashboard',
            'order' => 10,
        ]);
    }

    /**
     * Register default dashboard widgets
     */
    protected function registerDashboardWidgets(): void
    {
        // Stats widgets
        \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::register('stats.orders', [
            'view' => 'core::admin.widgets.stats-card',
            'data' => [
                'title' => 'Total Orders',
                'value' => '0',
                'icon' => 'fas fa-shopping-cart',
                'iconBg' => 'bg-blue-100',
                'iconColor' => 'text-blue-600',
                'change' => 0,
            ],
            'position' => 'stats',
            'order' => 10,
            'width' => 'quarter',
        ]);

        \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::register('stats.revenue', [
            'view' => 'core::admin.widgets.stats-card',
            'data' => [
                'title' => 'Revenue',
                'value' => '$0',
                'icon' => 'fas fa-dollar-sign',
                'iconBg' => 'bg-green-100',
                'iconColor' => 'text-green-600',
                'change' => 0,
            ],
            'position' => 'stats',
            'order' => 20,
            'width' => 'quarter',
        ]);

        \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::register('stats.customers', [
            'view' => 'core::admin.widgets.stats-card',
            'data' => [
                'title' => 'Customers',
                'value' => '0',
                'icon' => 'fas fa-users',
                'iconBg' => 'bg-purple-100',
                'iconColor' => 'text-purple-600',
                'change' => 0,
            ],
            'position' => 'stats',
            'order' => 30,
            'width' => 'quarter',
        ]);

        \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::register('stats.products', [
            'view' => 'core::admin.widgets.stats-card',
            'data' => [
                'title' => 'Products',
                'value' => '0',
                'icon' => 'fas fa-box',
                'iconBg' => 'bg-yellow-100',
                'iconColor' => 'text-yellow-600',
            ],
            'position' => 'stats',
            'order' => 40,
            'width' => 'quarter',
        ]);

        // Recent activity widget
        \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::register('recent.activity', [
            'view' => 'core::admin.widgets.recent-activity',
            'data' => [
                'title' => 'Recent Activity',
                'activities' => [],
            ],
            'position' => 'main',
            'order' => 10,
            'width' => 'full',
        ]);
    }

    /**
     * Register default settings pages
     */
    protected function registerSettingsPages(): void
    {
        \ElevateCommerce\Core\Support\Settings\SettingsRegistry::register('general', [
            'title' => 'General',
            'description' => 'Manage your store name, logo, timezone, and other basic settings',
            'icon' => 'fas fa-store',
            'route' => 'admin.settings.general',
            'group' => 'general',
            'order' => 10,
            'color' => 'blue',
        ]);
    }
}
