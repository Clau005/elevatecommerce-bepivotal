<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ElevateCommerce Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration adds the staff authentication guard and provider
    | for admin/staff users, separate from customer users.
    |
    */

    'guards' => [
        'staff' => [
            'driver' => 'session',
            'provider' => 'staff',
        ],
    ],

    'providers' => [
        'staff' => [
            'driver' => 'eloquent',
            'model' => Elevate\CommerceCore\Models\Staff::class,
        ],
    ],

    'passwords' => [
        'staff' => [
            'provider' => 'staff',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],
];
