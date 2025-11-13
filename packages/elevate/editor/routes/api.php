<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Editor API Routes
|--------------------------------------------------------------------------
|
| These routes handle AJAX requests from the visual editor for real-time
| preview, auto-save, and other interactive features.
|
*/

Route::prefix('api/editor')->middleware(['web', 'auth:staff'])->name('api.editor.')->group(function () {
    
    // Draft management (auto-save)
    Route::post('save-draft', [\Elevate\Editor\Http\Controllers\Api\EditorApiController::class, 'saveDraft'])->name('save-draft');
    
    // Live preview update (real-time as you edit)
    Route::post('update-preview', [\Elevate\Editor\Http\Controllers\Api\EditorApiController::class, 'updatePreview'])->name('update-preview');
    
    // Sections
    Route::get('themes/{theme}/sections', [\Elevate\Editor\Http\Controllers\Api\EditorApiController::class, 'getSections'])->name('themes.sections');
    Route::get('sections/{slug}/schema', [\Elevate\Editor\Http\Controllers\Api\EditorApiController::class, 'getSectionSchema'])->name('sections.schema');
    
    // Model data for template preview
    Route::get('models/{modelType}/preview-data', [\Elevate\Editor\Http\Controllers\Api\EditorApiController::class, 'getPreviewData'])->name('models.preview-data');
});

// Pages and Templates (outside editor prefix to avoid /api/editor/themes/...)
Route::prefix('api')->middleware(['web', 'auth:staff'])->group(function () {
    // Pages for a theme
    Route::get('themes/{theme}/pages', [\Elevate\Editor\Http\Controllers\Api\EditorApiController::class, 'getThemePages'])->name('api.themes.pages');
    
    // All templates (global, not theme-specific)
    Route::get('templates', [\Elevate\Editor\Http\Controllers\Api\EditorApiController::class, 'getAllTemplates'])->name('api.templates');
});
