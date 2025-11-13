<?php

use Illuminate\Support\Facades\Route;
use Elevate\Product\Http\Controllers\Admin\ProductController;

/*
|--------------------------------------------------------------------------
| Product Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['web', 'auth:staff'])->group(function () {
    Route::resource('products', ProductController::class);
    
    // Variant management routes
    Route::prefix('products/{product}')->group(function () {
        Route::get('variants', [ProductController::class, 'variants'])->name('products.variants');
        Route::post('variants', [ProductController::class, 'storeVariant'])->name('products.variants.store');
        Route::put('variants/{variant}', [ProductController::class, 'updateVariant'])->name('products.variants.update');
        Route::delete('variants/{variant}', [ProductController::class, 'destroyVariant'])->name('products.variants.destroy');
    });
});
