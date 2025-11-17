<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Collections Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Collections package
    |
    */

    'url_prefix' => 'collections',

    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
    ],

    'pagination' => [
        'per_page' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Default templates for rendering collections
    |
    */
    'templates' => [
        'default' => 'collection',
        'subcollection' => 'collection',
    ],

    // Default items per page for collection listings
    'per_page' => 20,

    // Enable/disable nested collections
    'enable_nested' => true,

    // Maximum nesting depth (0 = unlimited)
    'max_depth' => 5,

    // Collectable types that can be added to collections
    // Other packages should register their types via CollectableRegistry
    'collectable_types' => [
        'ElevateCommerce\Collections\Models\Collection' => [
            'label' => 'Collections',
            'singular' => 'Collection',
            'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        ],
        // Other packages register their types in their service providers:
        // Example: CollectableRegistry::register(Product::class, [...config...])
    ],
];
