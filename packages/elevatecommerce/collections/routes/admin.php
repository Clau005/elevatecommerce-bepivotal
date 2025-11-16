<?php

use Illuminate\Support\Facades\Route;
use ElevateCommerce\Collections\Http\Controllers\Admin\CollectionController;
use ElevateCommerce\Collections\Http\Controllers\Admin\FilterController;

/*
|--------------------------------------------------------------------------
| Collections Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:admin'])->group(function () {
    Route::prefix('collections')->name('admin.collections.')->group(function () {
        Route::get('/', [CollectionController::class, 'index'])->name('index');
        Route::get('/create', [CollectionController::class, 'create'])->name('create');
        Route::post('/', [CollectionController::class, 'store'])->name('store');
        Route::get('/{collection}/edit', [CollectionController::class, 'edit'])->name('edit');
        Route::put('/{collection}', [CollectionController::class, 'update'])->name('update');
        Route::delete('/{collection}', [CollectionController::class, 'destroy'])->name('destroy');
        
        // Collection items management
        Route::get('/items/available', [CollectionController::class, 'getAvailableItems'])->name('items.available');
        Route::get('/items/by-tags', [CollectionController::class, 'getItemsByTags'])->name('items.by-tags');
        Route::post('/{collection}/items', [CollectionController::class, 'addItem'])->name('items.add');
        Route::delete('/{collection}/items/bulk-remove', [CollectionController::class, 'bulkRemoveItems'])->name('items.bulk-remove');
        Route::delete('/{collection}/items/{collectable}', [CollectionController::class, 'removeItem'])->name('items.remove');
        Route::post('/{collection}/items/sort', [CollectionController::class, 'updateSort'])->name('items.sort');
    });

    // Filter management routes
    Route::prefix('filters')->name('admin.filters.')->group(function () {
        Route::get('/', [FilterController::class, 'index'])->name('index');
        Route::get('/create', [FilterController::class, 'create'])->name('create');
        Route::post('/', [FilterController::class, 'store'])->name('store');
        Route::get('/{filter}/edit', [FilterController::class, 'edit'])->name('edit');
        Route::put('/{filter}', [FilterController::class, 'update'])->name('update');
        Route::delete('/{filter}', [FilterController::class, 'destroy'])->name('destroy');
        
        // Sync filter values (discover from database)
        Route::post('/{filter}/sync-values', [FilterController::class, 'syncValues'])->name('sync-values');
    });
});
