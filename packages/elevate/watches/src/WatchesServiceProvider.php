<?php

namespace Elevate\Watches;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Elevate\Watches\Models\Watch;
use Elevate\Editor\Services\PageRenderService;

class WatchesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Elevate\Watches\Console\Commands\InstallWatchesCommand::class,
                \Elevate\Watches\Console\Commands\SeedWatchesCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        // Register route exclusion for watches prefix
        // This prevents the page catch-all from intercepting /watches/* routes
        if (class_exists(\App\Routing\Services\RouteExclusionRegistry::class)) {
            \App\Routing\Services\RouteExclusionRegistry::exclude('watches');
        }

        // Register model type for template rendering
        $renderService = app(PageRenderService::class);
        $renderService->registerModelType(Watch::class, 'watch');

        // Register Watch model in template registry
        $templateRegistry = app(\Elevate\Editor\Services\TemplateRegistry::class);
        $templateRegistry->register(Watch::class, [
            'label' => 'Watch',
            'plural_label' => 'Watches',
            'icon' => 'clock',
            'description' => 'Individual watch pages',
            'default_route_pattern' => '/watches/{slug}',
        ]);

        // Register Watch variable name in PageRenderService
        $renderService = app(\Elevate\Editor\Services\PageRenderService::class);
        $renderService->registerModelType(Watch::class, 'watch');

        // Register Watch as a collectable type
        if ($this->app->bound(\Elevate\Collections\Services\CollectableRegistry::class)) {
            $collectableRegistry = app(\Elevate\Collections\Services\CollectableRegistry::class);
            $collectableRegistry->register(Watch::class, [
                'label' => 'Watches',
                'singular' => 'Watch',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ]);
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Routes are now loaded via RouteServiceProvider using Route Registrars
        // This ensures proper route priority and prevents conflicts
        // Admin routes are still loaded here as they don't conflict
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'watches');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/watches'),
        ], 'watches-views');

        // Publish sections
        $this->publishes([
            __DIR__.'/../resources/sections' => resource_path('views/themes'),
        ], 'watches-sections');

        // Register navigation
        $this->registerNavigation();
    }

    protected function registerNavigation(): void
    {
        if (!$this->app->bound('admin.navigation')) {
            return;
        }

        $nav = $this->app->make('admin.navigation');

        $nav->add('Watches', '/admin/watches', [
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'pattern' => 'admin/watches',
            'group' => 'online-store',
            'order' => 815,
        ]);
    }
}
