<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;
use Elevate\Watches\Http\Controllers\WatchWebController;

class WatchRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Watch routes - /watches/{slug}
        // Apply web middleware for sessions, CSRF, etc.
        $registrar->middleware('web')->group(function () {
            Route::get('/watches/{slug}', [WatchWebController::class, 'show'])
                ->name('watches.show');
        });
    }
}
