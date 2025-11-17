<?php

namespace ElevateCommerce\Core;

use ElevateCommerce\Core\Support\Helpers\CurrencyHelper;
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

        // Load components
        $this->loadViewComponentsAs('core', [
            \Illuminate\View\Component::class,
        ]);

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

        // Register Blade directives
        $this->registerBladeDirectives();
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

        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Media',
            'icon' => 'fas fa-images',
            'route' => 'admin.media.index',
            'order' => 20,
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
                'value' => CurrencyHelper::format(0),
                'icon' => 'fas fa-pound-sign',
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

        \ElevateCommerce\Core\Support\Settings\SettingsRegistry::register('currencies', [
            'title' => 'Currencies',
            'description' => 'Manage currencies and exchange rates',
            'icon' => 'fas fa-dollar-sign',
            'route' => 'admin.settings.currencies.index',
            'group' => 'localization',
            'order' => 10,
            'color' => 'green',
        ]);
    }

    /**
     * Register Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
            return "<?php echo \ElevateCommerce\Core\Support\Helpers\CurrencyHelper::format($expression); ?>";
        });
    }
}
