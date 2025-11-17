<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

class ApiRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Load all API routes from packages
        // These are registered with api middleware
        
        // Apply api middleware group to all API routes
        $registrar->middleware('api')->group(function () {
            $this->loadApiRoutes();
        });
    }
    
    protected function loadApiRoutes(): void
    {
        $apiRouteFiles = [
            base_path('packages/elevatecommerce/core/routes/api.php'),          // Core API routes
            // Editor API routes loaded separately in bootstrap/app.php with web middleware
            base_path('packages/elevatecommerce/purchasable/routes/api.php'),   // Purchasable API routes
            base_path('routes/api.php'),                                        // Main API routes
        ];
        
        foreach ($apiRouteFiles as $file) {
            if (file_exists($file)) {
                require $file;
            }
        }
    }
}
