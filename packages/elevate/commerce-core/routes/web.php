<?php

use Illuminate\Support\Facades\Route;
use Elevate\CommerceCore\Http\Controllers\StorefrontCustomerController;

/*
|--------------------------------------------------------------------------
| Commerce Web Routes (Storefront)
|--------------------------------------------------------------------------
|
| Authentication, Cart, Wishlist, Checkout, Account, and Order routes
|
*/

/*
|--------------------------------------------------------------------------
| Customer Authentication Routes
|--------------------------------------------------------------------------
*/

// Customer login
Route::get('/login', [StorefrontCustomerController::class, 'login'])->name('storefront.login');
Route::post('/login', [StorefrontCustomerController::class, 'loginPost'])->name('storefront.login.post');

// Customer registration
Route::get('/register', [StorefrontCustomerController::class, 'register'])->name('storefront.register');
Route::post('/register', [StorefrontCustomerController::class, 'registerPost'])->name('storefront.register.post');

// Customer logout
Route::post('/logout', [StorefrontCustomerController::class, 'logout'])->name('storefront.logout');

/*
|--------------------------------------------------------------------------
| Cart Routes (Guest & Authenticated)
|--------------------------------------------------------------------------
*/

Route::prefix('cart')->name('storefront.cart.')->group(function () {
    // View cart
    Route::get('/', [StorefrontCustomerController::class, 'cart'])->name('index');
    
    // Cart actions
    Route::post('/add', [StorefrontCustomerController::class, 'cartAdd'])->name('add');
    Route::post('/update/{purchasableType}/{purchasableId}', [StorefrontCustomerController::class, 'cartUpdate'])->name('update');
    Route::delete('/remove/{purchasableType}/{purchasableId}', [StorefrontCustomerController::class, 'cartRemove'])->name('remove');
    Route::delete('/clear', [StorefrontCustomerController::class, 'cartClear'])->name('clear');
});

/*
|--------------------------------------------------------------------------
| Wishlist Routes (Guest & Authenticated)
|--------------------------------------------------------------------------
*/

Route::prefix('wishlist')->name('storefront.wishlist.')->group(function () {
    // View wishlist
    Route::get('/', [StorefrontCustomerController::class, 'wishlist'])->name('index');
    
    // Wishlist actions
    Route::post('/add', [StorefrontCustomerController::class, 'wishlistAdd'])->name('add');
    Route::delete('/remove/{purchasableType}/{purchasableId}', [StorefrontCustomerController::class, 'wishlistRemove'])->name('remove');
    Route::post('/move-to-cart/{purchasableType}/{purchasableId}', [StorefrontCustomerController::class, 'wishlistMoveToCart'])->name('move-to-cart');
    Route::post('/move-all-to-cart', [StorefrontCustomerController::class, 'wishlistMoveAllToCart'])->name('move-all-to-cart');
    Route::delete('/clear', [StorefrontCustomerController::class, 'wishlistClear'])->name('clear');
});

/*
|--------------------------------------------------------------------------
| Checkout Routes (Guest & Authenticated)
|--------------------------------------------------------------------------
*/

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [\Elevate\CommerceCore\Http\Controllers\CheckoutController::class, 'index'])->name('index');
    Route::post('/calculate-rates', [\Elevate\CommerceCore\Http\Controllers\CheckoutController::class, 'calculateRates'])->name('calculate-rates');
    Route::post('/process', [\Elevate\CommerceCore\Http\Controllers\CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [\Elevate\CommerceCore\Http\Controllers\CheckoutController::class, 'success'])->name('success');
});

/*
|--------------------------------------------------------------------------
| Customer Account Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:web')->prefix('account')->name('storefront.')->group(function () {
    // Account dashboard
    Route::get('/', [StorefrontCustomerController::class, 'account'])->name('account');
    
    // Profile management
    Route::get('/profile', [StorefrontCustomerController::class, 'profile'])->name('profile');
    Route::post('/profile', [StorefrontCustomerController::class, 'profileUpdate'])->name('profile.update');
    
    // Address management
    Route::get('/addresses', [\App\Http\Controllers\Storefront\AddressController::class, 'index'])->name('addresses');
    Route::post('/addresses', [\App\Http\Controllers\Storefront\AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [\App\Http\Controllers\Storefront\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [\App\Http\Controllers\Storefront\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/set-default', [\App\Http\Controllers\Storefront\AddressController::class, 'setDefault'])->name('addresses.set-default');
    
    // Order management
    Route::get('/orders', [StorefrontCustomerController::class, 'orders'])->name('account.orders');
    Route::get('/orders/{id}', [StorefrontCustomerController::class, 'orderShow'])->name('account.orders.show');
});
