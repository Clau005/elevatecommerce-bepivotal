<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;
use Elevate\Product\Http\Controllers\ProductWebController;

class ProductRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Product routes - /products/{slug}
        // Apply web middleware for sessions, CSRF, etc.
        $registrar->middleware('web')->group(function () {
            Route::get('/products/{slug}', [ProductWebController::class, 'show'])
                ->name('products.show');
        });
    }
}
