<?php

use Illuminate\Support\Facades\Route;
use App\Routing\Registrars\AdminRoutesRegistrar;
use App\Routing\Registrars\PageRoutesRegistrar;
use App\Routing\Registrars\ProductRoutesRegistrar;
use App\Routing\Registrars\CollectionRoutesRegistrar;
use App\Routing\Registrars\WatchRoutesRegistrar;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Register all admin routes from packages
(new AdminRoutesRegistrar)->map(Route::getFacadeRoot());

// Register storefront routes (these should be LAST to avoid conflicts)
(new ProductRoutesRegistrar)->map(Route::getFacadeRoot());
(new CollectionRoutesRegistrar)->map(Route::getFacadeRoot());
(new WatchRoutesRegistrar)->map(Route::getFacadeRoot());
(new PageRoutesRegistrar)->map(Route::getFacadeRoot()); // Pages LAST (catch-all)
