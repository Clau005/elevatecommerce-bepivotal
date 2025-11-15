<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ElevateCommerce Core - Customer Account Routes
|--------------------------------------------------------------------------
|
| Customer-facing account routes for the core package. These routes handle
| customer authentication, registration, and account management.
|
*/

// Guest routes (login, register)
Route::prefix('account')->middleware('guest')->group(function () {
    Route::get('/login', [\ElevateCommerce\Core\Http\Controllers\Account\AuthController::class, 'showLogin'])->name('account.login');
    Route::post('/login', [\ElevateCommerce\Core\Http\Controllers\Account\AuthController::class, 'login']);
    
    Route::get('/register', [\ElevateCommerce\Core\Http\Controllers\Account\AuthController::class, 'showRegister'])->name('account.register');
    Route::post('/register', [\ElevateCommerce\Core\Http\Controllers\Account\AuthController::class, 'register']);
});

// Authenticated customer routes
Route::prefix('account')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('core::account.dashboard');
    })->name('account.dashboard');
    
    Route::get('/orders', function () {
        return view('core::account.orders');
    })->name('account.orders');
    
    Route::get('/addresses', function () {
        return view('core::account.addresses');
    })->name('account.addresses');
    
    Route::get('/profile', function () {
        return view('core::account.profile');
    })->name('account.profile');
    
    Route::post('/logout', [\ElevateCommerce\Core\Http\Controllers\Account\AuthController::class, 'logout'])->name('account.logout');
});
