<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active Theme
    |--------------------------------------------------------------------------
    |
    | This value determines which theme is currently active for the storefront.
    | Themes are located in resources/views/themes/{theme-name}/
    |
    */

    'active' => env('THEME_ACTIVE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Theme Path
    |--------------------------------------------------------------------------
    |
    | The base path where themes are stored.
    |
    */

    'path' => resource_path('views/themes'),

    /*
    |--------------------------------------------------------------------------
    | Available Themes
    |--------------------------------------------------------------------------
    |
    | List of available themes in your application.
    |
    */

    'available' => [
        'default' => [
            'name' => 'Default Theme',
            'description' => 'The default ElevateCommerce theme',
            'version' => '1.0.0',
        ],
    ],

];
