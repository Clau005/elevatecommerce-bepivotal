<?php

use Illuminate\Support\Facades\Route;
use Elevate\Payments\Http\Controllers\Admin\PaymentGatewayController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth:staff'])->group(function () {
    Route::prefix('payment-gateways')->name('payment-gateways.')->group(function () {
        Route::get('/', [PaymentGatewayController::class, 'index'])->name('index');
        Route::patch('/{gateway}', [PaymentGatewayController::class, 'update'])->name('update');
        Route::patch('/{gateway}/toggle', [PaymentGatewayController::class, 'toggle'])->name('toggle');
    });
});
