<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cart Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Number of days to keep guest carts before they are considered abandoned.
    |
    */
    'session_lifetime' => env('CART_SESSION_LIFETIME', 30),

    /*
    |--------------------------------------------------------------------------
    | Guest Cart Enabled
    |--------------------------------------------------------------------------
    |
    | Allow guest users to create and manage carts without authentication.
    |
    */
    'guest_cart_enabled' => env('GUEST_CART_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency code for new carts.
    |
    */
    'default_currency' => env('CART_DEFAULT_CURRENCY', 'GBP'),

    /*
    |--------------------------------------------------------------------------
    | Tax Included
    |--------------------------------------------------------------------------
    |
    | Whether prices include tax or tax is added at checkout.
    |
    */
    'tax_included' => env('CART_TAX_INCLUDED', true),

    /*
    |--------------------------------------------------------------------------
    | Wishlist Enabled
    |--------------------------------------------------------------------------
    |
    | Enable wishlist functionality for customers.
    |
    */
    'wishlist_enabled' => env('WISHLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Guest Wishlist Enabled
    |--------------------------------------------------------------------------
    |
    | Allow guest users to create and manage wishlists without authentication.
    |
    */
    'guest_wishlist_enabled' => env('GUEST_WISHLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Checkout Settings
    |--------------------------------------------------------------------------
    */
    'checkout' => [
        /*
        | Allow Guest Checkout
        |
        | When enabled, customers can complete purchases without creating an account.
        | They can optionally create an account after checkout.
        */
        'allow_guest_checkout' => env('ALLOW_GUEST_CHECKOUT', true),

        /*
        | Customer Information Fields
        |
        | Configure which fields to collect at checkout
        */
        'fields' => [
            'name_format' => env('CHECKOUT_NAME_FORMAT', 'first_and_last'), // 'last_only', 'first_and_last'
            'company_name' => env('CHECKOUT_COMPANY_NAME', 'optional'), // 'hidden', 'optional', 'required'
            'address_line_2' => env('CHECKOUT_ADDRESS_LINE_2', 'optional'), // 'hidden', 'optional', 'required'
            'phone_number' => env('CHECKOUT_PHONE_NUMBER', 'required'), // 'hidden', 'optional', 'required'
        ],

        /*
        | Marketing Options
        |
        | Display checkboxes for customers to sign up for marketing
        */
        'marketing' => [
            'email' => env('CHECKOUT_MARKETING_EMAIL', true),
            'sms' => env('CHECKOUT_MARKETING_SMS', false),
        ],
    ],
];
