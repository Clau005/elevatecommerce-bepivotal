<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\Purchasable\Http\Controllers\Admin\OrderController;

/*
|--------------------------------------------------------------------------
| ElevateCommerce Purchasable - Admin Routes
|--------------------------------------------------------------------------
|
| Admin routes for order management. These routes are loaded by the
| route registrar with the "web" and "auth:admin" middleware.
|
*/

// Order Management
Route::middleware(['auth:admin'])->group(function () {
    Route::prefix('orders')->name('admin.orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        
        // Order actions
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('update-payment-status');
        Route::post('/{order}/tracking', [OrderController::class, 'updateTracking'])->name('update-tracking');
        Route::post('/{order}/note', [OrderController::class, 'addNote'])->name('add-note');
    });
});
