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
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');
});
