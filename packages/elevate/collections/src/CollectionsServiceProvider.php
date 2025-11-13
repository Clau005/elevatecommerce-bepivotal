<?php

namespace Elevate\Collections;

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
        $this->app->singleton(\Elevate\Collections\Services\CollectableRegistry::class, function ($app) {
            return new \Elevate\Collections\Services\CollectableRegistry();
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
        //         \Elevate\Collections\Console\Commands\SyncCollectionTemplatesCommand::class,
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
        if (!$this->app->bound(\Elevate\Collections\Services\CollectableRegistry::class)) {
            return;
        }

        $registry = $this->app->make(\Elevate\Collections\Services\CollectableRegistry::class);
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
        if (!$this->app->bound('admin.navigation')) {
            return;
        }

        $nav = $this->app->make('admin.navigation');

        $nav->add('Collections', '/admin/collections', [
            'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            'pattern' => 'admin/collections',
            'group' => 'online-store',
            'order' => 815,
        ]);

        $nav->add('Filters', '/admin/filters', [
            'icon' => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z',
            'pattern' => 'admin/filters',
            'group' => 'online-store',
            'order' => 816,
        ]);
    }

    /**
     * Register Collection as a templatable model
     */
    protected function registerTemplatableModel(): void
    {
        if (!$this->app->bound(\Elevate\Editor\Services\TemplateRegistry::class)) {
            return;
        }

        $registry = $this->app->make(\Elevate\Editor\Services\TemplateRegistry::class);

        $registry->register(\Elevate\Collections\Models\Collection::class, [
            'label' => 'Collection',
            'plural_label' => 'Collections',
            'icon' => 'folder',
            'description' => 'Collection pages',
            'default_route_pattern' => '/collections/{slug}',
            'preview_data_provider' => function() {
                return \Elevate\Collections\Models\Collection::with(['products'])
                    ->inRandomOrder()
                    ->first();
            },
        ]);
    }
}
