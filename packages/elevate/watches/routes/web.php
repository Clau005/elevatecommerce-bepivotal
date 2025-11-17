<?php

use Elevate\Watches\Http\Controllers\WatchWebController;
use Illuminate\Support\Facades\Route;
// Frontend routes will go here when WatchWebController is created


Route::get('/watches/{slug}', [WatchWebController::class, 'show'])
    ->name('watches.show');
