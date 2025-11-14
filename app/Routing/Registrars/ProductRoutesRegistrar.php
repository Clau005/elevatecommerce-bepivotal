<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;

class ProductRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Load product routes from product package
        
        // Apply web middleware group
        $registrar->middleware('web')->group(function () {
            $this->loadProductRoutes();
        });
    }
    
    protected function loadProductRoutes(): void
    {
        $routeFile = base_path('packages/elevate/product/routes/web.php');
        
        if (file_exists($routeFile)) {
            require $routeFile;
        }
    }
}
