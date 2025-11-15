<?php

namespace ElevateCommerce\Core\Support\Dashboard;

class DashboardRegistry
{
    /**
     * Registered dashboard widgets
     */
    protected static array $widgets = [];

    /**
     * Register a dashboard widget
     * 
     * @param string $key Unique identifier for the widget
     * @param array $widget Widget configuration
     */
    public static function register(string $key, array $widget): void
    {
        static::$widgets[$key] = array_merge([
            'component' => null,      // Blade component name
            'view' => null,           // Blade view path
            'data' => [],             // Data to pass to the view
            'position' => 'main',     // 'main', 'sidebar', 'top', 'bottom'
            'order' => 100,           // Sort order
            'width' => 'full',        // 'full', 'half', 'third', 'quarter'
            'permissions' => [],      // Required permissions
            'enabled' => true,        // Whether widget is enabled
        ], $widget);
    }

    /**
     * Get widgets for a specific position
     */
    public static function getByPosition(string $position = 'main'): array
    {
        $widgets = array_filter(static::$widgets, function ($widget) use ($position) {
            return $widget['position'] === $position && $widget['enabled'];
        });

        // Sort by order
        uasort($widgets, fn($a, $b) => $a['order'] <=> $b['order']);

        return $widgets;
    }

    /**
     * Get a specific widget by key
     */
    public static function get(string $key): ?array
    {
        return static::$widgets[$key] ?? null;
    }

    /**
     * Get all registered widgets
     */
    public static function all(): array
    {
        return static::$widgets;
    }

    /**
     * Remove a widget
     */
    public static function remove(string $key): void
    {
        unset(static::$widgets[$key]);
    }

    /**
     * Clear all widgets (useful for testing)
     */
    public static function clear(): void
    {
        static::$widgets = [];
    }

    /**
     * Check if a widget exists
     */
    public static function has(string $key): bool
    {
        return isset(static::$widgets[$key]);
    }
}
