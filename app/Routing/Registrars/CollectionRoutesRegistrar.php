<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;
use Elevate\Collections\Http\Controllers\CollectionWebController;

class CollectionRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Collection routes with optional filters
        // Must be registered BEFORE single-level slug route
        // Apply web middleware for sessions, CSRF, etc.
        $registrar->middleware('web')->group(function () {
            // Nested collection with optional filters: /{parent}/{child}/{filters?}
            Route::get('/{parent}/{child}/{filters?}', [CollectionWebController::class, 'show'])
                ->where('filters', '.*')
                ->name('collections.subcollection');
        });
    }
}
