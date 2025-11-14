<?php

use Illuminate\Support\Facades\Route;
use Elevate\ManagedEventNotifications\Http\Controllers\NotificationController;

Route::prefix('admin/managed-notifications')
    ->name('admin.managed-notifications.')
    ->middleware(['web', 'auth'])
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/settings', [NotificationController::class, 'settings'])->name('settings');
        Route::post('/settings', [NotificationController::class, 'updateSettings'])->name('settings.update');
        Route::get('/history', [NotificationController::class, 'history'])->name('history');
        Route::get('/preview/{type}', [NotificationController::class, 'preview'])->name('preview');
        Route::post('/test/{type}', [NotificationController::class, 'sendTest'])->name('test');
    });
