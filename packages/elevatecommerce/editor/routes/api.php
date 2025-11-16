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
| Note: These routes are loaded in bootstrap/app.php with 'web' middleware and 'api' prefix
| This allows session-based authentication (auth:admin) to work properly
|
*/

// Pages and Templates (at root API level)
Route::middleware(['auth:admin'])->group(function () {
    // Pages for a theme
    Route::get('themes/{theme}/pages', [\ElevateCommerce\Editor\Http\Controllers\Api\EditorApiController::class, 'getThemePages'])->name('api.themes.pages');
    
    // All templates (global, not theme-specific)
    Route::get('templates', [\ElevateCommerce\Editor\Http\Controllers\Api\EditorApiController::class, 'getAllTemplates'])->name('api.templates');
});

Route::prefix('editor')->middleware(['auth:admin'])->name('api.editor.')->group(function () {
    
    // Draft management (auto-save)
    Route::post('save-draft', [\ElevateCommerce\Editor\Http\Controllers\Api\EditorApiController::class, 'saveDraft'])->name('save-draft');
    
    // Live preview update (real-time as you edit)
    Route::post('update-preview', [\ElevateCommerce\Editor\Http\Controllers\Api\EditorApiController::class, 'updatePreview'])->name('update-preview');
    
    // Sections
    Route::get('themes/{theme}/sections', [\ElevateCommerce\Editor\Http\Controllers\Api\EditorApiController::class, 'getSections'])->name('themes.sections');
    Route::get('sections/{slug}/schema', [\ElevateCommerce\Editor\Http\Controllers\Api\EditorApiController::class, 'getSectionSchema'])->name('sections.schema');
    
    // Model data for template preview
    Route::get('models/{modelType}/preview-data', [\ElevateCommerce\Editor\Http\Controllers\Api\EditorApiController::class, 'getPreviewData'])->name('models.preview-data');
});


