<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;

class PageRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Load page/editor routes from editor package
        // These are registered LAST (lowest priority) - catch-all routes
        
        // Apply web middleware group
        $registrar->middleware('web')->group(function () {
            $this->loadPageRoutes();
        });
    }
    
    protected function loadPageRoutes(): void
    {
        $routeFile = base_path('packages/elevate/editor/routes/web.php');
        
        if (file_exists($routeFile)) {
            require $routeFile;
        }
    }
}
