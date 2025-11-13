<?php

use Illuminate\Support\Facades\Route;
use Elevate\Product\Http\Controllers\ProductWebController;

/*
|--------------------------------------------------------------------------
| Product Web Routes
|--------------------------------------------------------------------------
|
| These routes handle the public-facing product pages
|
*/

Route::get('/products/{slug}', [ProductWebController::class, 'show'])
    ->name('products.show');
