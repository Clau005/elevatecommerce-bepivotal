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
        
        // Currency routes
        Route::prefix('currencies')->group(function () {
            Route::get('/', [\ElevateCommerce\Core\Http\Controllers\CurrencyController::class, 'index'])->name('admin.settings.currencies.index');
            Route::get('/create', [\ElevateCommerce\Core\Http\Controllers\CurrencyController::class, 'create'])->name('admin.settings.currencies.create');
            Route::post('/', [\ElevateCommerce\Core\Http\Controllers\CurrencyController::class, 'store'])->name('admin.settings.currencies.store');
            Route::get('/{currency}/edit', [\ElevateCommerce\Core\Http\Controllers\CurrencyController::class, 'edit'])->name('admin.settings.currencies.edit');
            Route::put('/{currency}', [\ElevateCommerce\Core\Http\Controllers\CurrencyController::class, 'update'])->name('admin.settings.currencies.update');
            Route::post('/{currency}/set-default', [\ElevateCommerce\Core\Http\Controllers\CurrencyController::class, 'setDefault'])->name('admin.settings.currencies.set-default');
            Route::post('/{currency}/toggle-enabled', [\ElevateCommerce\Core\Http\Controllers\CurrencyController::class, 'toggleEnabled'])->name('admin.settings.currencies.toggle-enabled');
            Route::delete('/{currency}', [\ElevateCommerce\Core\Http\Controllers\CurrencyController::class, 'destroy'])->name('admin.settings.currencies.destroy');
        });
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\ElevateCommerce\Core\Http\Controllers\NotificationsController::class, 'index'])->name('admin.notifications.index');
        Route::get('/{id}', [\ElevateCommerce\Core\Http\Controllers\NotificationsController::class, 'show'])->name('admin.notifications.show');
        Route::post('/{id}/mark-read', [\ElevateCommerce\Core\Http\Controllers\NotificationsController::class, 'markAsRead'])->name('admin.notifications.mark-read');
        Route::post('/mark-all-read', [\ElevateCommerce\Core\Http\Controllers\NotificationsController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
        Route::delete('/{id}', [\ElevateCommerce\Core\Http\Controllers\NotificationsController::class, 'delete'])->name('admin.notifications.delete');
    });

    // Profile route (placeholder)
    Route::get('/profile', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin.profile');
    
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');
});
