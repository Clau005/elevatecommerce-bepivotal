<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;

class CollectionRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Load collection routes from collections package
        
        // Apply web middleware group
        $registrar->middleware('web')->group(function () {
            $this->loadCollectionRoutes();
        });
    }
    
    protected function loadCollectionRoutes(): void
    {
        $routeFile = base_path('packages/elevate/collections/routes/web.php');
        
        if (file_exists($routeFile)) {
            require $routeFile;
        }
    }
}
