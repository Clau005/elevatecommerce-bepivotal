<?php

namespace ElevateCommerce\Editor;

use ElevateCommerce\Editor\Services\EditorService;
use ElevateCommerce\Editor\Services\RenderEngine;
use ElevateCommerce\Editor\Services\TemplateRegistry;
use ElevateCommerce\Editor\Services\TemplateResolver;
use ElevateCommerce\Editor\Services\PageRenderService;
use ElevateCommerce\Editor\Services\RouteRegistry;
use Illuminate\Support\ServiceProvider;

class EditorServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/editor.php', 'editor');

        // Register services as singletons
        $this->app->singleton(TemplateRegistry::class, function ($app) {
            return new TemplateRegistry();
        });

        $this->app->singleton(RenderEngine::class, function ($app) {
            return new RenderEngine();
        });

        $this->app->singleton(TemplateResolver::class, function ($app) {
            return new TemplateResolver(
                $app->make(TemplateRegistry::class),
                $app->make(RenderEngine::class)
            );
        });

        $this->app->singleton(EditorService::class, function ($app) {
            return new EditorService();
        });

        $this->app->singleton(PageRenderService::class, function ($app) {
            return new PageRenderService();
        });

        $this->app->singleton(RouteRegistry::class, function ($app) {
            return new RouteRegistry();
        });

        // Register aliases
        $this->app->alias(TemplateRegistry::class, 'editor.registry');
        $this->app->alias(RenderEngine::class, 'editor.render');
        $this->app->alias(TemplateResolver::class, 'editor.resolver');
        $this->app->alias(EditorService::class, 'editor.service');
        $this->app->alias(PageRenderService::class, 'editor.page_render');
        $this->app->alias(RouteRegistry::class, 'editor.route_registry');
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'editor');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \ElevateCommerce\Editor\Console\Commands\InstallCommand::class,
                \ElevateCommerce\Editor\Console\Commands\SyncThemeSectionsCommand::class,
            ]);
        }

        // Register admin navigation
        $this->registerNavigation();
        
        // Register Pages as collectable type (if Collections package is installed)
        $this->registerCollectableTypes();

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/editor.php' => config_path('editor.php'),
        ], 'editor-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'editor-migrations');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/editor'),
        ], 'editor-views');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Add console commands here
            ]);
        }
    }

    /**
     * Register navigation items
     */
    protected function registerNavigation(): void
    {
        // Themes
        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Themes',
            'icon' => 'fas fa-palette',
            'route' => 'admin.themes.index',
            'order' => 30,
        ]);

        // Pages
        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Pages',
            'icon' => 'fas fa-file-alt',
            'route' => 'admin.pages.index',
            'order' => 20,
        ]);

        // Templates
        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Templates',
            'icon' => 'fas fa-th-large',
            'route' => 'admin.templates.index',
            'order' => 25,
        ]);
    }
    
    /**
     * Register Pages as a collectable type
     */
    protected function registerCollectableTypes(): void
    {
        // Only register if Collections package is installed
        if (!$this->app->bound(\ElevateCommerce\Collections\Services\CollectableRegistry::class)) {
            return;
        }

        $registry = $this->app->make(\ElevateCommerce\Collections\Services\CollectableRegistry::class);

        $registry->register(\ElevateCommerce\Editor\Models\Page::class, [
            'label' => 'Pages',
            'singular' => 'Page',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ]);
    }
}
