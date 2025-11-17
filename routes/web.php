<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingPurchasableWebController;

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

// Product detail pages - /products/{slug}
Route::get('/products/{slug}', [TestingPurchasableWebController::class, 'show'])
    ->name('products.show');

// Route::get('/', function () {
//     $products = \App\Models\TestingPurchasable::where('is_active', true)
//         ->orderBy('created_at', 'desc')
//         ->get();
    
//     return view('home', compact('products'));
// })->name('home');