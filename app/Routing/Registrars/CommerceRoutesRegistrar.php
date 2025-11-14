<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;
use Elevate\CommerceCore\Http\Controllers\StorefrontCustomerController;
use Elevate\CommerceCore\Http\Controllers\CheckoutController;
use Elevate\CommerceCore\Http\Controllers\Storefront\AddressController;

class CommerceRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Apply web middleware for sessions, CSRF, etc.
        $registrar->middleware('web')->group(function () {
            
            /*
            |--------------------------------------------------------------------------
            | Customer Authentication Routes
            |--------------------------------------------------------------------------
            */
            
            Route::get('/login', [StorefrontCustomerController::class, 'login'])->name('storefront.login');
            Route::post('/login', [StorefrontCustomerController::class, 'loginPost'])->name('storefront.login.post');
            Route::get('/register', [StorefrontCustomerController::class, 'register'])->name('storefront.register');
            Route::post('/register', [StorefrontCustomerController::class, 'registerPost'])->name('storefront.register.post');
            Route::post('/logout', [StorefrontCustomerController::class, 'logout'])->name('storefront.logout');
            
            /*
            |--------------------------------------------------------------------------
            | Cart Routes (Guest & Authenticated)
            |--------------------------------------------------------------------------
            */
            
            Route::prefix('cart')->name('storefront.cart.')->group(function () {
                Route::get('/', [StorefrontCustomerController::class, 'cart'])->name('index');
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
                Route::get('/', [StorefrontCustomerController::class, 'wishlist'])->name('index');
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
                Route::get('/', [CheckoutController::class, 'index'])->name('index');
                Route::post('/calculate-rates', [CheckoutController::class, 'calculateRates'])->name('calculate-rates');
                Route::post('/process', [CheckoutController::class, 'process'])->name('process');
                Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
            });
            
            /*
            |--------------------------------------------------------------------------
            | Customer Account Routes (Requires Authentication)
            |--------------------------------------------------------------------------
            */
            
            Route::middleware('auth:web')->prefix('account')->name('storefront.')->group(function () {
                Route::get('/', [StorefrontCustomerController::class, 'account'])->name('account');
                Route::get('/profile', [StorefrontCustomerController::class, 'profile'])->name('profile');
                Route::post('/profile', [StorefrontCustomerController::class, 'profileUpdate'])->name('profile.update');
                
                // Address management
                Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
                Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
                Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
                Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
                Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.set-default');
                
                // Order management
                Route::get('/orders', [StorefrontCustomerController::class, 'orders'])->name('account.orders');
                Route::get('/orders/{id}', [StorefrontCustomerController::class, 'orderShow'])->name('account.orders.show');
            });
        });
    }
}
