<?php

use Illuminate\Support\Facades\Route;
use Elevate\Blogs\Http\Controllers\Admin\PostController;

/*
|--------------------------------------------------------------------------
| Blog Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth:staff'])->prefix('admin')->group(function () {
    Route::resource('posts', PostController::class)->names([
        'index' => 'admin.posts.index',
        'create' => 'admin.posts.create',
        'store' => 'admin.posts.store',
        'edit' => 'admin.posts.edit',
        'update' => 'admin.posts.update',
        'destroy' => 'admin.posts.destroy',
    ]);
});
