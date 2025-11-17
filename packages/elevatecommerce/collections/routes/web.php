<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\Collections\Http\Controllers\CollectionWebController;
use ElevateCommerce\Collections\Services\CollectionSlugRegistry;

/*
|--------------------------------------------------------------------------
| Collections Storefront Routes
|--------------------------------------------------------------------------
|
| These routes handle collection pages (both single-level and nested).
| Slugs are constrained to actual collections in database (cached).
|
*/

// Single-level collections - e.g., /catalog, /products
// Constrained to actual collection slugs (cached)
Route::get('/{slug}/{filters?}', [CollectionWebController::class, 'show'])
    ->where('slug', CollectionSlugRegistry::getPattern())
    ->where('filters', '.*')
    ->name('collections.show');

// Nested collections (subcollections) - e.g., /men/shoes
Route::get('/{parent}/{child}/{filters?}', [CollectionWebController::class, 'show'])
    ->where('filters', '.*')
    ->name('collections.subcollection');