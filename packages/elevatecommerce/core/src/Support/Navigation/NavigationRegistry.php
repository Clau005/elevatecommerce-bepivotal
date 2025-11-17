<?php

namespace ElevateCommerce\Core\Support\Navigation;

class NavigationRegistry
{
    /**
     * Registered navigation items
     */
    protected static array $items = [];

    /**
     * Register a navigation item
     */
    public static function register(string $group, array $item): void
    {
        if (!isset(static::$items[$group])) {
            static::$items[$group] = [];
        }

        static::$items[$group][] = array_merge([
            'label' => '',
            'icon' => null,
            'route' => null,
            'url' => null,
            'badge' => null,
            'children' => [],
            'order' => 100,
            'permissions' => [],
            'active' => null, // Callback or route pattern
        ], $item);

        // Sort by order
        usort(static::$items[$group], fn($a, $b) => $a['order'] <=> $b['order']);
    }

    /**
     * Get navigation items for a group
     */
    public static function get(string $group): array
    {
        return static::$items[$group] ?? [];
    }

    /**
     * Get all navigation items
     */
    public static function all(): array
    {
        return static::$items;
    }

    /**
     * Clear all navigation items (useful for testing)
     */
    public static function clear(): void
    {
        static::$items = [];
    }
}
