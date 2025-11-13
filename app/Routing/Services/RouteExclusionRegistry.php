<?php

declare(strict_types=1);

namespace App\Routing\Services;

class RouteExclusionRegistry
{
    /**
     * Reserved route prefixes that should NOT be caught by the page catch-all
     * 
     * These prefixes are handled by specific route registrars or controllers
     */
    protected static array $excludedPrefixes = [
        'admin',      // Admin panel routes
        'api',        // API routes
        'account',    // Customer account routes
        'cart',       // Shopping cart routes
        'wishlist',   // Wishlist routes
        'checkout',   // Checkout routes
        'login',      // Authentication routes
        'register',   // Registration routes
        'logout',     // Logout routes
        'password',   // Password reset routes
        'preview',    // Editor preview routes
        'products',   // Product detail pages
        'watches',    // Watch detail pages
    ];

    /**
     * Register a new excluded prefix
     */
    public static function exclude(string $prefix): void
    {
        if (!in_array($prefix, static::$excludedPrefixes)) {
            static::$excludedPrefixes[] = $prefix;
        }
    }

    /**
     * Register multiple excluded prefixes
     */
    public static function excludeMany(array $prefixes): void
    {
        foreach ($prefixes as $prefix) {
            static::exclude($prefix);
        }
    }

    /**
     * Get all excluded prefixes
     */
    public static function getExcludedPrefixes(): array
    {
        return static::$excludedPrefixes;
    }

    /**
     * Get regex pattern for route where clause
     * 
     * Returns: ^(?!admin|api|products|watches|...).*$
     */
    public static function getWherePattern(): string
    {
        $prefixes = implode('|', static::$excludedPrefixes);
        return "^(?!{$prefixes}).*$";
    }

    /**
     * Check if a slug should be excluded from page routing
     */
    public static function isExcluded(string $slug): bool
    {
        $firstSegment = explode('/', $slug)[0];
        return in_array($firstSegment, static::$excludedPrefixes);
    }
}
