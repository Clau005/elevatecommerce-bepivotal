<?php

namespace Elevate\Product;

use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/products.php', 'products'
        );
    }

    public function boot(): void
    {
        // Register route exclusion for products prefix
        // This prevents the page catch-all from intercepting /products/* routes
        if (class_exists(\App\Routing\Services\RouteExclusionRegistry::class)) {
            \App\Routing\Services\RouteExclusionRegistry::exclude('products');
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Routes are now loaded via RouteServiceProvider using Route Registrars
        // This ensures proper route priority and prevents conflicts

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'product');

        // Register admin navigation
        $this->registerNavigation();

        // Register Product as templatable
        $this->registerTemplatableModel();

        // Register commands
        // Disabled: SyncProductTemplatesCommand uses ThemeService from removed themes package
        // if ($this->app->runningInConsole()) {
        //     $this->commands([
        //         \Elevate\Product\Console\Commands\SyncProductTemplatesCommand::class,
        //     ]);
        // }

        // Publish config
        $this->publishes([
            __DIR__.'/../config/products.php' => config_path('products.php'),
        ], 'product-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/product'),
        ], 'product-views');
    }

    /**
     * Register products navigation items
     */
    protected function registerNavigation(): void
    {
        if (!$this->app->bound('admin.navigation')) {
            return;
        }

        $nav = $this->app->make('admin.navigation');

        $nav->add('Products', '/admin/products', [
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'pattern' => 'admin/products',
            'group' => 'online-store',
            'order' => 810,
        ]);
    }

    /**
     * Register Product as a templatable model
     */
    protected function registerTemplatableModel(): void
    {
        if (!$this->app->bound(\Elevate\Editor\Services\TemplateRegistry::class)) {
            return;
        }

        $registry = $this->app->make(\Elevate\Editor\Services\TemplateRegistry::class);

        $registry->register(\Elevate\Product\Models\Product::class, [
            'label' => 'Product',
            'plural_label' => 'Products',
            'icon' => 'shopping-bag',
            'description' => 'Product detail pages',
            'default_route_pattern' => '/products/{slug}',
            'preview_data_provider' => function() {
                return \Elevate\Product\Models\Product::with(['images', 'variants'])
                    ->inRandomOrder()
                    ->first();
            },
        ]);
    }
}
