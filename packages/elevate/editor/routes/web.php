<?php

use Illuminate\Support\Facades\Route;
use Elevate\Editor\Http\Controllers\PageController;
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
Route::middleware(['web', 'auth:staff'])->prefix('preview')->name('preview.')->group(function () {
    Route::get('/themes/{theme}/pages/{page}', [\Elevate\Editor\Http\Controllers\PreviewController::class, 'page'])->name('pages');
    Route::get('/themes/{theme}/templates/{template}', [\Elevate\Editor\Http\Controllers\PreviewController::class, 'template'])->name('templates');
});

// Catch-all route for dynamic pages (must be LAST!)
// Handles all pages created in admin: /about, /contact, /services, etc.
// Also handles collection routes with nested paths
// Excluded prefixes are managed by RouteExclusionRegistry
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', RouteExclusionRegistry::getWherePattern())
    ->name('page.show');
