<?php

namespace Elevate\Blogs\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class BlogsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'blogs');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');

        // Register navigation
        $this->registerNavigation();

        // Register Post as templatable
        $this->registerTemplatableModel();

        // Publish assets if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/blogs'),
            ], 'blogs-views');
        }
    }

    /**
     * Register navigation items
     */
    protected function registerNavigation(): void
    {
        $nav = $this->app->make('admin.navigation');

        $nav->add('Blog', '/admin/posts', [
            'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
            'pattern' => 'admin/posts',
            'order' => 55,
        ]);
    }

    /**
     * Register Post as a templatable model
     */
    protected function registerTemplatableModel(): void
    {
        if (!$this->app->bound(\Elevate\Editor\Services\TemplateRegistry::class)) {
            return;
        }

        $registry = $this->app->make(\Elevate\Editor\Services\TemplateRegistry::class);

        $registry->register(\Elevate\Blogs\Models\Post::class, [
            'label' => 'Blog Post',
            'plural_label' => 'Blog Posts',
            'icon' => 'document-text',
            'description' => 'Blog post pages',
            'default_route_pattern' => '/blog/{slug}',
            'preview_data_provider' => function() {
                return \Elevate\Blogs\Models\Post::with(['author'])
                    ->published()
                    ->inRandomOrder()
                    ->first();
            },
        ]);
    }
}
