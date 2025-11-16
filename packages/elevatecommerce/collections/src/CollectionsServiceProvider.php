<?php

namespace ElevateCommerce\Collections;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CollectionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/collections.php',
            'collections'
        );

        // Register CollectableRegistry as singleton
        $this->app->singleton(\ElevateCommerce\Collections\Services\CollectableRegistry::class, function ($app) {
            return new \ElevateCommerce\Collections\Services\CollectableRegistry();
        });
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Routes are now loaded via RouteServiceProvider using Route Registrars
        // This ensures proper route priority and prevents conflicts

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'collections');

        // Register commands
        // Disabled: SyncCollectionTemplatesCommand uses ThemeService from removed themes package
        // if ($this->app->runningInConsole()) {
        //     $this->commands([
        //         \ElevateCommerce\Collections\Console\Commands\SyncCollectionTemplatesCommand::class,
        //     ]);
        // }

        // Register default collectable types
        $this->registerDefaultCollectableTypes();

        // Register admin navigation
        $this->registerNavigation();

        // Register Collection as templatable
        $this->registerTemplatableModel();

        // Publish config
        $this->publishes([
            __DIR__.'/../config/collections.php' => config_path('collections.php'),
        ], 'collections-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'collections-migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/collections'),
        ], 'collections-views');
    }

    /**
     * Register default collectable types from config
     */
    protected function registerDefaultCollectableTypes(): void
    {
        if (!$this->app->bound(\ElevateCommerce\Collections\Services\CollectableRegistry::class)) {
            return;
        }

        $registry = $this->app->make(\ElevateCommerce\Collections\Services\CollectableRegistry::class);
        $types = config('collections.collectable_types', []);

        foreach ($types as $class => $config) {
            $registry->register($class, $config);
        }
    }

    /**
     * Register collections navigation items
     */
    protected function registerNavigation(): void
    {
        // Collections
        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Collections',
            'icon' => 'fas fa-layer-group',
            'route' => 'admin.collections.index',
            'order' => 20,
        ]);

        // Filters
        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Filters',
            'icon' => 'fas fa-filter',
            'route' => 'admin.filters.index',
            'order' => 21,
        ]);
    }

    /**
     * Register Collection as a templatable model
     */
    protected function registerTemplatableModel(): void
    {
        if (!$this->app->bound(\ElevateCommerce\Editor\Services\TemplateRegistry::class)) {
            return;
        }

        $registry = $this->app->make(\ElevateCommerce\Editor\Services\TemplateRegistry::class);

        $registry->register(\ElevateCommerce\Collections\Models\Collection::class, [
            'label' => 'Collection',
            'plural_label' => 'Collections',
            'icon' => 'folder',
            'description' => 'Collection pages',
            'default_route_pattern' => '/collections/{slug}',
            'preview_data_provider' => function() {
                return \ElevateCommerce\Collections\Models\Collection::with(['products'])
                    ->inRandomOrder()
                    ->first();
            },
        ]);
    }
}
