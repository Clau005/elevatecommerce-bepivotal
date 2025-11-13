<?php

use Illuminate\Support\Facades\Route;
use Elevate\Shipping\Http\Controllers\Admin\ShippingCarrierController;

Route::middleware('auth:staff')->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('shipping-carriers')->name('shipping-carriers.')->group(function () {
        Route::get('/', [ShippingCarrierController::class, 'index'])->name('index');
        Route::patch('/{carrier}', [ShippingCarrierController::class, 'update'])->name('update');
        Route::post('/{carrier}/toggle', [ShippingCarrierController::class, 'toggle'])->name('toggle');
    });
});
