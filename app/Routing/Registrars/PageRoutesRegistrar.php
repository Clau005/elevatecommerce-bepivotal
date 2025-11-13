<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use App\Routing\Services\RouteExclusionRegistry;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;
use Elevate\Editor\Http\Controllers\PageController;

class PageRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Apply web middleware for sessions, CSRF, etc.
        $registrar->middleware('web')->group(function () {
            // Homepage
            Route::get('/', [PageController::class, 'show'])
                ->defaults('slug', 'homepage')
                ->name('home');

            // Preview routes
            Route::get('/preview/{theme?}', [PageController::class, 'preview'])->name('preview');
            Route::post('/preview/{theme}/update', [PageController::class, 'updatePreview'])->name('preview.update');
            Route::get('/preview/{theme}/check', [PageController::class, 'checkPreviewChanges'])->name('preview.check');
            Route::get('/preview/{theme}/data', [PageController::class, 'getPreviewData'])->name('preview.data');
            
            // Template preview routes
            Route::get('/preview/{theme}/template/{template}/{type}/{id}', [PageController::class, 'previewTemplate'])->name('preview.template');

            // Catch-all for collections and pages - /{slug}/{filters?}
            // This is registered LAST (lowest priority)
            // Supports optional filter segments for single-level collections
            // Excluded prefixes are managed by RouteExclusionRegistry
            Route::get('/{slug}/{filters?}', [PageController::class, 'show'])
                ->where('slug', RouteExclusionRegistry::getWherePattern())
                ->where('filters', '.*')
                ->name('page.show');
        });
    }
}
