<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

class CustomerRouteRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Load all customer-facing routes from packages
        // These are registered in hierarchical order
        
        // Apply web middleware group to all customer routes
        $registrar->middleware('web')->group(function () {
            $this->loadCustomerRoutes();
        });
    }
    
    protected function loadCustomerRoutes(): void
    {
        $customerRouteFiles = [
            base_path('packages/elevatecommerce/core/routes/account.php'),      // Customer account routes
            base_path('packages/elevatecommerce/core/routes/web.php'),          // Core web routes
            base_path('packages/elevatecommerce/purchasable/routes/web.php'),   // Purchasable routes (cart, checkout, payments)
            base_path('routes/web.php'),                                        // Main web routes
        ];
        
        foreach ($customerRouteFiles as $file) {
            if (file_exists($file)) {
                require $file;
            }
        }
    }
}
