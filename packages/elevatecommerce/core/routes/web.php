<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ElevateCommerce Core - Customer Routes
|--------------------------------------------------------------------------
|
| Customer-facing routes for the core package. These routes are loaded
| by the CustomerRouteRegistrar with the "web" middleware.
|
*/

/*
|--------------------------------------------------------------------------
| Customer Account Routes
|--------------------------------------------------------------------------
*/
Route::prefix('account')->name('account.')->group(function () {
    // Guest routes
    Route::middleware('guest:web')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Account\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Account\AuthController::class, 'login']);
        Route::get('/register', [\App\Http\Controllers\Account\AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [\App\Http\Controllers\Account\AuthController::class, 'register']);
    });

    // Authenticated routes
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', function () {
            return view('account.dashboard');
        })->name('dashboard');
        Route::post('/logout', [\App\Http\Controllers\Account\AuthController::class, 'logout'])->name('logout');
    });
});
