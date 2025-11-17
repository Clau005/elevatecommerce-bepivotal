<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ElevateCommerce Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration adds authentication guards and providers for both
    | admin users and customer users (web).
    |
    */

    'guards' => [
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => ElevateCommerce\Core\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],
];
