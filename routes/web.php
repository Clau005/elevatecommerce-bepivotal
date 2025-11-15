<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| Core package routes are loaded via CustomerRouteRegistrar first,
| then these application-specific routes are loaded.
|
*/

Route::get('/', function () {
    return view('welcome');
});
