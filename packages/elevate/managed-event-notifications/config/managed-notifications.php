<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Notification Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default notification channel that will be used
    | when sending notifications. You can use 'mail', 'database', or both.
    |
    */
    'default_channel' => env('NOTIFICATION_CHANNEL', 'mail'),

    /*
    |--------------------------------------------------------------------------
    | Email Provider
    |--------------------------------------------------------------------------
    |
    | The email provider to use. Options: 'resend', 'smtp', 'mailgun', etc.
    |
    */
    'email_provider' => env('MAIL_MAILER', 'resend'),

    /*
    |--------------------------------------------------------------------------
    | Resend Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Resend email service
    |
    */
    'resend' => [
        'api_key' => env('RESEND_API_KEY'),
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'name' => env('MAIL_FROM_NAME', 'ElevateCommerce'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer Notifications
    |--------------------------------------------------------------------------
    |
    | Configure which customer notifications are enabled and their settings
    |
    */
    'customer_notifications' => [
        // Order Processing
        'order.confirmation' => [
            'enabled' => true,
            'subject' => 'Order Confirmation - Order #:number',
            'template' => 'customer.order.confirmation',
            'channels' => ['mail', 'database'],
        ],
        'order.invoice' => [
            'enabled' => true,
            'subject' => 'Invoice for Order #:number',
            'template' => 'customer.order.invoice',
            'channels' => ['mail'],
        ],
        'order.edited' => [
            'enabled' => true,
            'subject' => 'Your Order Has Been Updated - Order #:number',
            'template' => 'customer.order.edited',
            'channels' => ['mail', 'database'],
        ],
        'order.canceled' => [
            'enabled' => true,
            'subject' => 'Order Canceled - Order #:number',
            'template' => 'customer.order.canceled',
            'channels' => ['mail', 'database'],
        ],
        'draft_order.invoice' => [
            'enabled' => true,
            'subject' => 'Invoice for Draft Order',
            'template' => 'customer.draft_order.invoice',
            'channels' => ['mail'],
        ],

        // Shipping & Fulfillment
        'shipping.confirmation' => [
            'enabled' => true,
            'subject' => 'Your Order Has Shipped - Order #:number',
            'template' => 'customer.shipping.confirmation',
            'channels' => ['mail', 'database'],
        ],

        // Local Pickup
        'pickup.ready' => [
            'enabled' => true,
            'subject' => 'Your Order is Ready for Pickup - Order #:number',
            'template' => 'customer.pickup.ready',
            'channels' => ['mail', 'database'],
        ],
        'pickup.confirmed' => [
            'enabled' => true,
            'subject' => 'Pickup Confirmed - Order #:number',
            'template' => 'customer.pickup.confirmed',
            'channels' => ['mail', 'database'],
        ],

        // Local Delivery
        'delivery.out_for_delivery' => [
            'enabled' => true,
            'subject' => 'Your Order is Out for Delivery - Order #:number',
            'template' => 'customer.delivery.out_for_delivery',
            'channels' => ['mail', 'database'],
        ],
        'delivery.delivered' => [
            'enabled' => true,
            'subject' => 'Your Order Has Been Delivered - Order #:number',
            'template' => 'customer.delivery.delivered',
            'channels' => ['mail', 'database'],
        ],
        'delivery.missed' => [
            'enabled' => true,
            'subject' => 'Delivery Attempt - Order #:number',
            'template' => 'customer.delivery.missed',
            'channels' => ['mail', 'database'],
        ],

        // Gift Cards
        'gift_card.new' => [
            'enabled' => true,
            'subject' => 'You Received a Gift Card!',
            'template' => 'customer.gift_card.new',
            'channels' => ['mail'],
        ],
        'gift_card.receipt' => [
            'enabled' => true,
            'subject' => 'Gift Card Purchase Receipt',
            'template' => 'customer.gift_card.receipt',
            'channels' => ['mail'],
        ],

        // Store Credit
        'store_credit.issued' => [
            'enabled' => true,
            'subject' => 'Store Credit Added to Your Account',
            'template' => 'customer.store_credit.issued',
            'channels' => ['mail', 'database'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Staff Notifications
    |--------------------------------------------------------------------------
    |
    | Configure which staff notifications are enabled and their settings
    |
    */
    'staff_notifications' => [
        'staff.order.new' => [
            'enabled' => true,
            'subject' => 'New Order Received - Order #:number',
            'template' => 'staff.order.new',
            'channels' => ['mail', 'database'],
            'recipients' => 'all_orders', // or specific emails
        ],
        'staff.order.return' => [
            'enabled' => true,
            'subject' => 'New Return Request - Order #:number',
            'template' => 'staff.order.return',
            'channels' => ['mail', 'database'],
            'recipients' => 'all_orders',
        ],
        'staff.order.draft' => [
            'enabled' => false,
            'subject' => 'New Draft Order Created',
            'template' => 'staff.order.draft',
            'channels' => ['mail'],
            'recipients' => 'store_owner',
        ],
        'staff.subscription.new' => [
            'enabled' => true,
            'subject' => 'New Subscription Order - Order #:number',
            'template' => 'staff.subscription.new',
            'channels' => ['mail', 'database'],
            'recipients' => 'all_orders',
        ],
        'staff.subscription.canceled' => [
            'enabled' => true,
            'subject' => 'Subscription Canceled',
            'template' => 'staff.subscription.canceled',
            'channels' => ['mail', 'database'],
            'recipients' => 'all_orders',
        ],
        'staff.payment.failure' => [
            'enabled' => true,
            'subject' => 'Payment Failure - Order #:number',
            'template' => 'staff.payment.failure',
            'channels' => ['mail', 'database'],
            'recipients' => 'all_orders',
        ],
        'staff.inventory.failure' => [
            'enabled' => true,
            'subject' => 'Inventory Failure - Order #:number',
            'template' => 'staff.inventory.failure',
            'channels' => ['mail', 'database'],
            'recipients' => 'all_orders',
        ],
        'staff.attribution.edited' => [
            'enabled' => false,
            'subject' => 'Sales Attribution Edited - Order #:number',
            'template' => 'staff.attribution.edited',
            'channels' => ['mail'],
            'recipients' => 'notification_subscribers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Staff Recipients
    |--------------------------------------------------------------------------
    |
    | Define staff member email addresses for different notification types
    |
    */
    'staff_recipients' => [
        'store_owner' => env('STORE_OWNER_EMAIL'),
        'all_orders' => explode(',', env('STAFF_ORDER_EMAILS', '')),
        'notification_subscribers' => explode(',', env('STAFF_NOTIFICATION_EMAILS', '')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Path
    |--------------------------------------------------------------------------
    |
    | The path where notification templates are stored
    |
    */
    'template_path' => resource_path('views/vendor/managed-notifications'),

    /*
    |--------------------------------------------------------------------------
    | Queue Notifications
    |--------------------------------------------------------------------------
    |
    | Whether to queue notifications for asynchronous sending
    |
    */
    'queue' => env('NOTIFICATION_QUEUE', true),

    /*
    |--------------------------------------------------------------------------
    | Queue Connection
    |--------------------------------------------------------------------------
    |
    | The queue connection to use for queued notifications
    |
    */
    'queue_connection' => env('NOTIFICATION_QUEUE_CONNECTION', 'default'),
];
