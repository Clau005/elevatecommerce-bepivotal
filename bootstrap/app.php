<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Register routes in hierarchical order using registrars
            
            // Editor API routes FIRST (need web middleware for session auth)
            app('router')->middleware('web')->prefix('api')->group(function () {
                $editorApiFile = base_path('packages/elevatecommerce/editor/routes/api.php');
                if (file_exists($editorApiFile)) {
                    require $editorApiFile;
                }
            });
            
            // Other API routes (use api middleware)
            (new \App\Routing\Registrars\ApiRoutesRegistrar)->map(app('router'));
            // Admin routes
            (new \App\Routing\Registrars\AdminRoutesRegistrar)->map(app('router'));
            // Customer routes last (catch-all patterns)
            (new \App\Routing\Registrars\CustomerRouteRegistrar)->map(app('router'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude payment webhook routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhooks/payments/*',
        ]);

        // Configure authentication redirects per guard
        $middleware->redirectGuestsTo(function ($request) {
            // Check if this is an admin route
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            
            // Check if this is a customer account route
            if ($request->is('account') || $request->is('account/*')) {
                return route('account.login');
            }
            
            // Default to customer login
            return route('account.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
