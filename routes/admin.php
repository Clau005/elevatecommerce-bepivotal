<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TestingPurchasableController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Application-specific admin routes
|
*/

Route::middleware(['auth:admin'])->group(function () {
    // Testing Purchasables Management
    Route::resource('testing-purchasables', TestingPurchasableController::class)->names([
        'index' => 'admin.testing-purchasables.index',
        'create' => 'admin.testing-purchasables.create',
        'store' => 'admin.testing-purchasables.store',
        'show' => 'admin.testing-purchasables.show',
        'edit' => 'admin.testing-purchasables.edit',
        'update' => 'admin.testing-purchasables.update',
        'destroy' => 'admin.testing-purchasables.destroy',
    ]);
});
