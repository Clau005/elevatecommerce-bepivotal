<?php

declare(strict_types=1);

namespace App\Routing\Contracts;

use Illuminate\Contracts\Routing\Registrar;

interface RouteRegistrar
{
    public function map(Registrar $registrar): void;
}
