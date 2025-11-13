<?php

namespace Elevate\CommerceCore\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Elevate\CommerceCore\Support\AdminNavigation add(string $label, string $url, array $options = [])
 * @method static \Elevate\CommerceCore\Support\AdminNavigation group(string $name, string $label, int $order = 100)
 * @method static array items()
 * @method static array groups()
 * @method static \Elevate\CommerceCore\Support\AdminNavigation clear()
 * @method static bool isActive(array $item)
 *
 * @see \Elevate\CommerceCore\Support\AdminNavigation
 */
class AdminNav extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'admin.navigation';
    }
}
