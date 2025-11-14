<?php

use Illuminate\Support\Facades\Route;
use Elevate\Collections\Http\Controllers\CollectionWebController;

/*
|--------------------------------------------------------------------------
| Collections Storefront Routes
|--------------------------------------------------------------------------
|
| These routes handle nested collection pages.
| Single-level collections are handled by the PageController fallback.
|
*/

// // Nested collections (subcollections) - e.g., /men/shoes
// // Global pattern for 'parent' and 'slug' excludes reserved paths (admin, api, etc.)
// Route::get('/{parent}/{slug}', [CollectionWebController::class, 'show'])
//     ->name('collections.subcollection');


Route::get('/{parent}/{child}/{filters?}', [CollectionWebController::class, 'show'])
    ->where('filters', '.*')
    ->name('collections.subcollection');