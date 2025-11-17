<?php

declare(strict_types=1);

namespace App\Routing\Registrars;

use App\Routing\Contracts\RouteRegistrar;
use Illuminate\Contracts\Routing\Registrar;

class WatchRoutesRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        // Load watch routes from watches package
        
        // Apply web middleware group
        $registrar->middleware('web')->group(function () {
            $this->loadWatchRoutes();
        });
    }
    
    protected function loadWatchRoutes(): void
    {
        $routeFile = base_path('packages/elevate/watches/routes/web.php');
        
        if (file_exists($routeFile)) {
            require $routeFile;
        }
    }
}
