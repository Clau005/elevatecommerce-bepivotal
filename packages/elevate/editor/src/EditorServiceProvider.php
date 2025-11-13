<?php

namespace Elevate\Editor;

use Elevate\Editor\Services\EditorService;
use Elevate\Editor\Services\RenderEngine;
use Elevate\Editor\Services\TemplateRegistry;
use Elevate\Editor\Services\TemplateResolver;
use Elevate\Editor\Services\PageRenderService;
use Elevate\Editor\Services\RouteRegistry;
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

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/admin.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'editor');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Elevate\Editor\Console\Commands\SyncThemeSectionsCommand::class,
            ]);
        }

        // Register admin navigation
        $this->registerNavigation();

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
        if (!$this->app->bound('admin.navigation')) {
            return;
        }

        $nav = $this->app->make('admin.navigation');

        // Create "Online Store" group
        $nav->group('online-store', 'Online Store', 800);

        // Themes
        $nav->add('Themes', '/admin/themes', [
            'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
            'pattern' => 'admin/themes',
            'group' => 'online-store',
            'order' => 810,
        ]);

        // Pages
        $nav->add('Pages', '/admin/pages', [
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'pattern' => 'admin/pages',
            'group' => 'online-store',
            'order' => 820,
        ]);

        // Templates
        $nav->add('Templates', '/admin/templates', [
            'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
            'pattern' => 'admin/templates',
            'group' => 'online-store',
            'order' => 830,
        ]);
    }
}
