<?php

use Illuminate\Support\Facades\Route;
use Elevate\Watches\Http\Controllers\Admin\WatchController;

Route::prefix('admin')->middleware(['web', 'auth:staff'])->name('admin.')->group(function () {
    Route::resource('watches', WatchController::class);
});
