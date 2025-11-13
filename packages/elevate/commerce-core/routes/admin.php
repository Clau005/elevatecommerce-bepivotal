<?php

use Illuminate\Support\Facades\Route;
use Elevate\CommerceCore\Http\Controllers\Admin\OrderWebController;
use Elevate\CommerceCore\Http\Controllers\Admin\DashboardController;
use Elevate\CommerceCore\Http\Controllers\Admin\CustomerWebController;
use Elevate\CommerceCore\Http\Controllers\Admin\EnquiryWebController;
use Elevate\CommerceCore\Http\Controllers\Admin\TagController;
use Elevate\CommerceCore\Http\Controllers\Admin\StaffController;
use Elevate\CommerceCore\Http\Controllers\Auth\StaffLoginController;
use Elevate\CommerceCore\Http\Controllers\Admin\CustomerGroupController;
use Elevate\CommerceCore\Http\Controllers\Admin\RoleController;
use Elevate\CommerceCore\Http\Controllers\Admin\DiscountController;
use Elevate\CommerceCore\Http\Controllers\Admin\CheckoutRuleController;
use Elevate\CommerceCore\Http\Controllers\Admin\GiftVoucherController;
use Elevate\CommerceCore\Http\Controllers\Admin\CheckoutSettingsController;
use Elevate\CommerceCore\Http\Controllers\Admin\CurrencyController;
use Elevate\CommerceCore\Http\Controllers\Admin\LanguageController;
use Elevate\CommerceCore\Http\Controllers\Admin\StateController;

/*
|--------------------------------------------------------------------------
| Commerce Admin Routes
|--------------------------------------------------------------------------
|
| Authentication, Dashboard, Order & Customer management routes for admin panel
|
*/

// Staff Authentication Routes
Route::prefix('admin')->middleware('web')->group(function () {
    // Login routes (guest only)
    Route::middleware('guest:staff')->group(function () {
        Route::get('/login', [StaffLoginController::class, 'create'])->name('login');
        Route::post('/login', [StaffLoginController::class, 'store'])->name('login.store');
    });

    // Authenticated routes
    Route::middleware('auth:staff')->group(function () {
        // Dashboard
        Route::get('', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.alt');
        Route::post('/logout', [StaffLoginController::class, 'destroy'])->name('admin.logout');

        // Order Management
    Route::resource('orders', OrderWebController::class)->only(['index', 'show', 'update'])->names([
        'index' => 'admin.orders.index',
        'show' => 'admin.orders.show',
        'update' => 'admin.orders.update',
    ]);

    // Order Timeline
    Route::post('orders/{order}/timeline', [OrderWebController::class, 'storeTimelineComment'])->name('admin.orders.timeline.store');
    Route::put('orders/{order}/timeline/{timeline}', [OrderWebController::class, 'updateTimelineComment'])->name('admin.orders.timeline.update');
    Route::delete('orders/{order}/timeline/{timeline}', [OrderWebController::class, 'destroyTimelineComment'])->name('admin.orders.timeline.destroy');

    // Customer Management
    Route::resource('customers', CustomerWebController::class)->names([
        'index' => 'admin.customers.index',
        'create' => 'admin.customers.create',
        'store' => 'admin.customers.store',
        'show' => 'admin.customers.show',
        'edit' => 'admin.customers.edit',
        'update' => 'admin.customers.update',
        'destroy' => 'admin.customers.destroy',
    ]);

    // Enquiry Management
    Route::resource('enquiries', EnquiryWebController::class)->only(['index', 'show', 'destroy'])->names([
        'index' => 'admin.enquiries.index',
        'show' => 'admin.enquiries.show',
        'destroy' => 'admin.enquiries.destroy',
    ]);

    // Enquiry Actions
    Route::prefix('enquiries')->name('admin.enquiries.')->group(function () {
        Route::put('{enquiry}/status', [EnquiryWebController::class, 'updateStatus'])->name('update-status');
    });

    // Tag Actions (must be before resource routes to avoid conflicts)
    Route::get('tags/all', [TagController::class, 'getAllTags'])->name('admin.tags.all');
    Route::post('tags/merge', [TagController::class, 'merge'])->name('admin.tags.merge');

    // Tag Management
    Route::resource('tags', TagController::class)->names([
        'index' => 'admin.tags.index',
        'create' => 'admin.tags.create',
        'store' => 'admin.tags.store',
        'edit' => 'admin.tags.edit',
        'update' => 'admin.tags.update',
        'destroy' => 'admin.tags.destroy',
    ]);

    // Settings - Staff Management
    Route::prefix('settings/staff')->name('admin.settings.staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StaffController::class, 'update'])->name('update');
        Route::delete('/{id}', [StaffController::class, 'destroy'])->name('destroy');
    });

    // Settings - Currencies
    Route::prefix('settings/currencies')->name('admin.settings.currencies.')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/create', [CurrencyController::class, 'create'])->name('create');
        Route::post('/', [CurrencyController::class, 'store'])->name('store');
        Route::get('/{currency}/edit', [CurrencyController::class, 'edit'])->name('edit');
        Route::put('/{currency}', [CurrencyController::class, 'update'])->name('update');
        Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
    });

    // Settings - Languages
    Route::prefix('settings/languages')->name('admin.settings.languages.')->group(function () {
        Route::get('/', [LanguageController::class, 'index'])->name('index');
        Route::get('/create', [LanguageController::class, 'create'])->name('create');
        Route::post('/', [LanguageController::class, 'store'])->name('store');
        Route::get('/{language}/edit', [LanguageController::class, 'edit'])->name('edit');
        Route::put('/{language}', [LanguageController::class, 'update'])->name('update');
        Route::delete('/{language}', [LanguageController::class, 'destroy'])->name('destroy');
    });

    // Settings - States & Regions
    Route::prefix('settings/states')->name('admin.settings.states.')->group(function () {
        Route::get('/', [StateController::class, 'index'])->name('index');
        Route::get('/{country}', [StateController::class, 'showCountry'])->name('country');
        Route::get('/{country}/create', [StateController::class, 'create'])->name('create');
        Route::post('/{country}', [StateController::class, 'store'])->name('store');
        Route::get('/{country}/{state}/edit', [StateController::class, 'edit'])->name('edit');
        Route::put('/{country}/{state}', [StateController::class, 'update'])->name('update');
        Route::delete('/{country}/{state}', [StateController::class, 'destroy'])->name('destroy');
    });

    // Settings - Customer Groups
    Route::prefix('settings/customer-groups')->name('admin.settings.customer-groups.')->group(function () {
        Route::get('/', [CustomerGroupController::class, 'index'])->name('index');
        Route::get('/create', [CustomerGroupController::class, 'create'])->name('create');
        Route::post('/', [CustomerGroupController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CustomerGroupController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerGroupController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerGroupController::class, 'destroy'])->name('destroy');
    });

    // Settings - Roles & Permissions
    Route::prefix('settings/roles')->name('admin.settings.roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // Settings - Discounts
    Route::prefix('settings/discounts')->name('admin.settings.discounts.')->group(function () {
        Route::get('/', [DiscountController::class, 'index'])->name('index');
        Route::get('/create', [DiscountController::class, 'create'])->name('create');
        Route::post('/', [DiscountController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DiscountController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DiscountController::class, 'update'])->name('update');
        Route::delete('/{id}', [DiscountController::class, 'destroy'])->name('destroy');
    });

    // Settings - Checkout Rules
    Route::prefix('settings/checkout-rules')->name('admin.settings.checkout-rules.')->group(function () {
        Route::get('/', [CheckoutRuleController::class, 'index'])->name('index');
        Route::get('/create', [CheckoutRuleController::class, 'create'])->name('create');
        Route::post('/', [CheckoutRuleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CheckoutRuleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CheckoutRuleController::class, 'update'])->name('update');
        Route::delete('/{id}', [CheckoutRuleController::class, 'destroy'])->name('destroy');
    });

    // Settings - Gift Vouchers
    Route::prefix('settings/gift-vouchers')->name('admin.settings.gift-vouchers.')->group(function () {
        Route::get('/', [GiftVoucherController::class, 'index'])->name('index');
        Route::get('/create', [GiftVoucherController::class, 'create'])->name('create');
        Route::post('/', [GiftVoucherController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [GiftVoucherController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GiftVoucherController::class, 'update'])->name('update');
        Route::delete('/{id}', [GiftVoucherController::class, 'destroy'])->name('destroy');
    });

    // Settings - Checkout
    Route::prefix('settings/checkout')->name('admin.settings.checkout.')->group(function () {
        Route::get('/', [CheckoutSettingsController::class, 'index'])->name('index');
        Route::post('/', [CheckoutSettingsController::class, 'update'])->name('update');
    });
    });
});
