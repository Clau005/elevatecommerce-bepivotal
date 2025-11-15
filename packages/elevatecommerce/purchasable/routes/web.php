<?php

use ElevateCommerce\Purchasable\Http\Controllers\CartController;
use ElevateCommerce\Purchasable\Http\Controllers\WishlistController;
use ElevateCommerce\Purchasable\Http\Controllers\CheckoutController;
use ElevateCommerce\Purchasable\Http\Controllers\PaymentGateways\StripeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ElevateCommerce Purchasable - Web Routes
|--------------------------------------------------------------------------
|
| Cart, Wishlist, Checkout, and Order routes. These routes are loaded
| by the route registrar with the "web" middleware.
|
*/

Route::name('purchasable.')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | CART ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('add', [CartController::class, 'add'])->name('add');
        Route::put('items/{cartItem}/quantity', [CartController::class, 'updateQuantity'])->name('update-quantity');
        Route::delete('items/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::post('items/{cartItem}/move-to-wishlist', [CartController::class, 'moveToWishlist'])->name('move-to-wishlist');
        Route::post('move-all-to-wishlist', [CartController::class, 'moveAllToWishlist'])->name('move-all-to-wishlist');
        Route::delete('clear', [CartController::class, 'clear'])->name('clear');
    });

    /*
    |--------------------------------------------------------------------------
    | WISHLIST ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('add', [WishlistController::class, 'add'])->name('add');
        Route::delete('items/{wishlistItem}', [WishlistController::class, 'remove'])->name('remove');
        Route::post('items/{wishlistItem}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('move-to-cart');
        Route::post('move-all-to-cart', [WishlistController::class, 'moveAllToCart'])->name('move-all-to-cart');
        Route::delete('clear', [WishlistController::class, 'clear'])->name('clear');
    });

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('process', [CheckoutController::class, 'process'])->name('process');
    });

    /*
    |--------------------------------------------------------------------------
    | PAYMENT GATEWAY ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('purchase')->name('purchase.')->group(function () {
        // Stripe
        Route::prefix('stripe')->name('stripe.')->group(function () {
            Route::get('checkout', [StripeController::class, 'checkout'])->name('checkout');
            Route::get('return', [StripeController::class, 'stripeReturn'])->name('return');
        });
    });
});