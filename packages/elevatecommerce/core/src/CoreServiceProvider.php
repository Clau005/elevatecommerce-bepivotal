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
    }
}
