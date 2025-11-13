<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Editor Admin Routes
|--------------------------------------------------------------------------
|
| These routes handle the admin interface for managing themes, pages,
| templates, and the visual editor.
|
*/

Route::prefix('admin')->middleware(['web', 'auth:staff'])->name('admin.')->group(function () {
    
    // Themes
    Route::resource('themes', \Elevate\Editor\Http\Controllers\Admin\ThemeController::class)->names([
        'index' => 'themes.index',
        'create' => 'themes.create',
        'store' => 'themes.store',
        'show' => 'themes.show',
        'edit' => 'themes.edit',
        'update' => 'themes.update',
        'destroy' => 'themes.destroy',
    ]);
    Route::post('themes/{theme}/activate', [\Elevate\Editor\Http\Controllers\Admin\ThemeController::class, 'activate'])->name('themes.activate');
    
    // Pages
    Route::resource('pages', \Elevate\Editor\Http\Controllers\Admin\PageController::class)->names([
        'index' => 'pages.index',
        'create' => 'pages.create',
        'store' => 'pages.store',
        'show' => 'pages.show',
        'edit' => 'pages.edit',
        'update' => 'pages.update',
        'destroy' => 'pages.destroy',
    ]);
    Route::post('pages/{page}/publish', [\Elevate\Editor\Http\Controllers\Admin\PageController::class, 'publish'])->name('pages.publish');
    
    // Templates
    Route::resource('templates', \Elevate\Editor\Http\Controllers\Admin\TemplateController::class)->names([
        'index' => 'templates.index',
        'create' => 'templates.create',
        'store' => 'templates.store',
        'show' => 'templates.show',
        'edit' => 'templates.edit',
        'update' => 'templates.update',
        'destroy' => 'templates.destroy',
    ]);
    Route::post('templates/{template}/publish', [\Elevate\Editor\Http\Controllers\Admin\TemplateController::class, 'publish']);
    Route::post('templates/{template}/set-default', [\Elevate\Editor\Http\Controllers\Admin\TemplateController::class, 'setDefault'])->name('templates.set-default');
    Route::get('templates/{template}/versions', [\Elevate\Editor\Http\Controllers\Admin\TemplateController::class, 'versions'])->name('templates.versions');
    Route::post('templates/{template}/versions/{version}/restore', [\Elevate\Editor\Http\Controllers\Admin\TemplateController::class, 'restoreVersion'])->name('templates.versions.restore');
    

    
    // Visual Editor (unified interface for both pages and templates)
    Route::prefix('themes/{theme}/visual-editor')->name('visual-editor.')->group(function () {
        Route::get('/pages/{page}', [\Elevate\Editor\Http\Controllers\Admin\VisualEditorController::class, 'editPage'])->name('pages');
        Route::get('/templates/{template}', [\Elevate\Editor\Http\Controllers\Admin\VisualEditorController::class, 'editTemplate'])->name('templates');
    });
});
