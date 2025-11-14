<?php

namespace Elevate\ManagedEventNotifications;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Elevate\ManagedEventNotifications\Mail\ResendTransport;

class ManagedEventNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/managed-notifications.php',
            'managed-notifications'
        );

        $this->app->singleton('managed-notifications', function ($app) {
            return new NotificationManager($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register route exclusion for managed-notifications prefix
        if (class_exists(\App\Routing\Services\RouteExclusionRegistry::class)) {
            \App\Routing\Services\RouteExclusionRegistry::exclude('admin/managed-notifications');
        }

        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/managed-notifications.php' => config_path('managed-notifications.php'),
        ], 'managed-notifications-config');

        // Publish templates
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/managed-notifications'),
        ], 'managed-notifications-templates');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'managed-notifications');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register admin navigation
        $this->registerNavigation();

        // Register Resend mail transport
        $this->registerResendMailer();

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\SendTestNotification::class,
                Console\Commands\ListNotifications::class,
            ]);
        }
    }

    /**
     * Register managed notifications navigation items
     */
    protected function registerNavigation(): void
    {
        if (!$this->app->bound('admin.navigation')) {
            return;
        }

        $nav = $this->app->make('admin.navigation');

        $nav->add('Managed Notifications', '/admin/managed-notifications', [
            'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
            'pattern' => 'admin/managed-notifications',
            'group' => 'settings',
            'order' => 900,
        ]);
    }

    /**
     * Register Resend mailer transport
     */
    protected function registerResendMailer(): void
    {
        Mail::extend('resend', function (array $config) {
            return new ResendTransport(
                config('managed-notifications.resend.api_key')
            );
        });
    }
}
