<?php

namespace ElevateCommerce\Core\Support\Settings;

class SettingsRegistry
{
    /**
     * Registered settings pages
     */
    protected static array $pages = [];

    /**
     * Register a settings page
     * 
     * @param string $key Unique identifier for the settings page
     * @param array $page Settings page configuration
     */
    public static function register(string $key, array $page): void
    {
        static::$pages[$key] = array_merge([
            'title' => '',            // Page title
            'description' => '',      // Page description
            'icon' => 'fas fa-cog',   // Icon for the card
            'route' => null,          // Route name
            'url' => null,            // Direct URL
            'group' => 'general',     // Group/category
            'order' => 100,           // Sort order
            'permissions' => [],      // Required permissions
            'badge' => null,          // Optional badge
            'color' => 'blue',        // Card color theme
        ], $page);
    }

    /**
     * Get settings pages by group
     */
    public static function getByGroup(string $group = 'general'): array
    {
        $pages = array_filter(static::$pages, function ($page) use ($group) {
            return $page['group'] === $group;
        });

        // Sort by order
        uasort($pages, fn($a, $b) => $a['order'] <=> $b['order']);

        return $pages;
    }

    /**
     * Get all groups
     */
    public static function getGroups(): array
    {
        $groups = [];
        foreach (static::$pages as $page) {
            if (!isset($groups[$page['group']])) {
                $groups[$page['group']] = [];
            }
            $groups[$page['group']][] = $page;
        }

        // Sort each group by order
        foreach ($groups as $group => $pages) {
            uasort($groups[$group], fn($a, $b) => $a['order'] <=> $b['order']);
        }

        return $groups;
    }

    /**
     * Get a specific settings page
     */
    public static function get(string $key): ?array
    {
        return static::$pages[$key] ?? null;
    }

    /**
     * Get all registered settings pages
     */
    public static function all(): array
    {
        return static::$pages;
    }

    /**
     * Remove a settings page
     */
    public static function remove(string $key): void
    {
        unset(static::$pages[$key]);
    }

    /**
     * Clear all settings pages (useful for testing)
     */
    public static function clear(): void
    {
        static::$pages = [];
    }

    /**
     * Check if a settings page exists
     */
    public static function has(string $key): bool
    {
        return isset(static::$pages[$key]);
    }
}
