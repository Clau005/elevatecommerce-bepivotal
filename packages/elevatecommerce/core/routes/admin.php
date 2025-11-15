<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ElevateCommerce Core - Admin Routes
|--------------------------------------------------------------------------
|
| Admin routes for the core package. These routes are automatically
| prefixed with "/admin" and use the "web" middleware by AdminRoutesRegistrar.
|
*/

// Guest routes (login)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('core::admin.dashboard');
    })->name('admin.dashboard');
    
    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [\ElevateCommerce\Core\Http\Controllers\SettingsController::class, 'index'])->name('admin.settings.index');
        Route::get('/general', [\ElevateCommerce\Core\Http\Controllers\SettingsController::class, 'general'])->name('admin.settings.general');
        Route::put('/general', [\ElevateCommerce\Core\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('admin.settings.general.update');
    });
    
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');
});
