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
            (new \App\Routing\Registrars\AdminRoutesRegistrar)->map(app('router'));
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
                return route('customer.login');
            }
            
            // Default to customer login
            return route('customer.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
