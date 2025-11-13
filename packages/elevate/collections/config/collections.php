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
    'collectable_types' => [
        'Elevate\Product\Models\Product' => [
            'label' => 'Products',
            'singular' => 'Product',
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ],
        'Elevate\Editor\Models\Page' => [
            'label' => 'Pages',
            'singular' => 'Page',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        'Elevate\Collections\Models\Collection' => [
            'label' => 'Collections',
            'singular' => 'Collection',
            'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        ],
        // Add more types as needed
    ],
];
