<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\Editor\Http\Controllers\PageController;
use App\Routing\Services\RouteExclusionRegistry;

/*
|--------------------------------------------------------------------------
| Editor Frontend Routes
|--------------------------------------------------------------------------
|
| These routes handle public-facing pages created in the editor.
| Pages are rendered using the active theme's configuration.
|
*/

// Homepage (special case - always '/')
Route::get('/', [PageController::class, 'show'])
    ->defaults('slug', 'homepage')
    ->name('home');

// Preview routes for visual editor (uses draft_configuration)
Route::prefix('preview')->name('preview.')->group(function () {
    Route::get('/themes/{theme}/pages/{page}', [\ElevateCommerce\Editor\Http\Controllers\PreviewController::class, 'page'])->name('pages');
    Route::get('/themes/{theme}/templates/{template}', [\ElevateCommerce\Editor\Http\Controllers\PreviewController::class, 'template'])->name('templates');
});

    // Catch-all for collections and pages - /{slug}/{filters?}
    // This is registered LAST (lowest priority)
    // Supports optional filter segments for single-level collections
    // Excluded prefixes are managed by RouteExclusionRegistry
    Route::get('/{slug}/{filters?}', [PageController::class, 'show'])
        ->where('slug', RouteExclusionRegistry::getWherePattern())
        ->where('filters', '.*')
        ->name('page.show');