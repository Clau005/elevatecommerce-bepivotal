<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

class AdminRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Load all admin routes from packages
        // These are registered FIRST (highest priority)
        
        // Apply web middleware group and /admin prefix to all admin routes
        $registrar->middleware('web')->prefix('admin')->group(function () {
            $this->loadAdminRoutes();
        });
    }
    
    protected function loadAdminRoutes(): void
    {
        $adminRouteFiles = [
            base_path('packages/elevatecommerce/core/routes/admin.php'),
            base_path('packages/elevatecommerce/collections/routes/admin.php'),
            base_path('packages/elevatecommerce/editor/routes/admin.php'),
            base_path('packages/elevatecommerce/purchasable/routes/admin.php'),
            base_path('routes/admin.php'), // Main admin routes if any
        ];
        
        foreach ($adminRouteFiles as $file) {
            if (file_exists($file)) {
                require $file;
            }
        }
    }
}
