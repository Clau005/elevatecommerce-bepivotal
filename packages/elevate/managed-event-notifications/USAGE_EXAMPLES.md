# Usage Examples

## Installation

1. Add the package to your Laravel application:

```bash
composer require elevate/managed-event-notifications
```

2. Publish configuration and templates:

```bash
php artisan vendor:publish --tag=managed-notifications-config
php artisan vendor:publish --tag=managed-notifications-templates
```

3. Run migrations:

```bash
php artisan migrate
```

4. Configure your `.env`:

```env
MAIL_MAILER=resend
RESEND_API_KEY=re_your_api_key_here
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Store Name"

# Staff notification recipients
STORE_OWNER_EMAIL=owner@yourdomain.com
STAFF_ORDER_EMAILS=staff1@yourdomain.com,staff2@yourdomain.com
STAFF_NOTIFICATION_EMAILS=admin@yourdomain.com
```

## Basic Usage

### Sending Customer Notifications

```php
use Elevate\ManagedEventNotifications\Facades\ManagedNotifications;

// When an order is placed
ManagedNotifications::send('order.confirmation', $order, $customer);

// When an order ships
ManagedNotifications::send('shipping.confirmation', [
    'number' => $order->number,
    'tracking_number' => $shipment->tracking_number,
    'tracking_url' => $shipment->tracking_url,
    'estimated_delivery' => $shipment->estimated_delivery_date,
    'shipping_method' => $shipment->method,
    'order_url' => route('orders.show', $order),
], $customer);

// When order is ready for pickup
ManagedNotifications::send('pickup.ready', [
    'number' => $order->number,
    'pickup_location' => $store->address,
    'pickup_hours' => $store->hours,
    'pickup_instructions' => 'Please bring a valid ID',
    'order_url' => route('orders.show', $order),
], $customer);
```

### Sending Staff Notifications

```php
// When a new order is received
ManagedNotifications::sendToStaff('staff.order.new', [
    'number' => $order->number,
    'created_at' => $order->created_at,
    'total' => $order->formatted_total,
    'customer_name' => $order->customer->name,
    'customer_email' => $order->customer->email,
    'items_count' => $order->items->count(),
    'payment_method' => $order->payment_method,
    'shipping_method' => $order->shipping_method,
    'admin_order_url' => route('admin.orders.show', $order),
]);

// When payment fails
ManagedNotifications::sendToStaff('staff.payment.failure', [
    'number' => $order->number,
    'amount' => $order->formatted_total,
    'customer_name' => $order->customer->name,
    'customer_email' => $order->customer->email,
    'payment_method' => $order->payment_method,
    'error_message' => $paymentError->message,
    'attempt_number' => $paymentAttempt->number,
    'admin_order_url' => route('admin.orders.show', $order),
]);
```

## Integration with Order Events

### Using Laravel Events

```php
// In your EventServiceProvider
protected $listen = [
    OrderPlaced::class => [
        SendOrderConfirmation::class,
        NotifyStaffOfNewOrder::class,
    ],
    OrderShipped::class => [
        SendShippingConfirmation::class,
    ],
];

// SendOrderConfirmation Listener
class SendOrderConfirmation
{
    public function handle(OrderPlaced $event): void
    {
        ManagedNotifications::send(
            'order.confirmation',
            $event->order,
            $event->order->customer
        );
    }
}

// NotifyStaffOfNewOrder Listener
class NotifyStaffOfNewOrder
{
    public function handle(OrderPlaced $event): void
    {
        ManagedNotifications::sendToStaff('staff.order.new', [
            'number' => $event->order->number,
            'created_at' => $event->order->created_at,
            'total' => $event->order->formatted_total,
            'customer_name' => $event->order->customer->name,
            'customer_email' => $event->order->customer->email,
            'items_count' => $event->order->items->count(),
            'admin_order_url' => route('admin.orders.show', $event->order),
        ]);
    }
}
```

### Using Model Observers

```php
// OrderObserver
class OrderObserver
{
    public function created(Order $order): void
    {
        // Send customer confirmation
        ManagedNotifications::send('order.confirmation', $order, $order->customer);
        
        // Notify staff
        ManagedNotifications::sendToStaff('staff.order.new', [
            'number' => $order->number,
            'total' => $order->formatted_total,
            'customer_name' => $order->customer->name,
            'admin_order_url' => route('admin.orders.show', $order),
        ]);
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->status === 'shipped') {
            ManagedNotifications::send('shipping.confirmation', [
                'number' => $order->number,
                'tracking_number' => $order->tracking_number,
                'order_url' => route('orders.show', $order),
            ], $order->customer);
        }
    }
}
```

## Customizing Templates

Templates are published to `resources/views/vendor/managed-notifications/`. You can edit them to match your brand:

```blade
{{-- resources/views/vendor/managed-notifications/customer/order/confirmation.blade.php --}}
@component('mail::message')
# 🎉 Thanks for Your Order!

Hi {{ $notifiable->name }},

We're excited to get your order ready!

**Order #{{ $data->number }}**

{{-- Add your custom content here --}}

@component('mail::button', ['url' => $data->order_url])
Track Your Order
@endcomponent

Questions? Reply to this email anytime.

Cheers,<br>
The {{ config('app.name') }} Team
@endcomponent
```

## Testing Notifications

```bash
# List all available notifications
php artisan notifications:list

# Send a test notification
php artisan notifications:test order.confirmation test@example.com
php artisan notifications:test staff.order.new admin@example.com
```

## Advanced Configuration

### Disable Specific Notifications

Edit `config/managed-notifications.php`:

```php
'customer_notifications' => [
    'order.confirmation' => [
        'enabled' => false, // Disable this notification
        // ...
    ],
],
```

### Change Notification Channels

```php
'customer_notifications' => [
    'order.confirmation' => [
        'enabled' => true,
        'channels' => ['mail', 'database'], // Store in database too
        // ...
    ],
],
```

### Customize Subject Lines

```php
'customer_notifications' => [
    'order.confirmation' => [
        'subject' => 'Your Order #:number is Confirmed! 🎉',
        // Use :placeholder for dynamic values
        // ...
    ],
],
```

## Queuing Notifications

Notifications are queued by default. To send immediately:

```php
// In config/managed-notifications.php
'queue' => false,
```

Or per-notification in your code:

```php
config(['managed-notifications.queue' => false]);
ManagedNotifications::send('order.confirmation', $order, $customer);
```

## Checking if Notifications are Enabled

```php
if (ManagedNotifications::isEnabled('order.confirmation')) {
    // Notification is enabled
}

// Get all available notifications
$notifications = ManagedNotifications::getAvailableNotifications();
// Returns: ['customer' => [...], 'staff' => [...]]
```

## Using with Different Email Providers

While Resend is recommended, you can use any Laravel-supported mail driver:

```env
# Using SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password

# Using Mailgun
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-secret-key
```

## Database Notifications

When using the `database` channel, notifications are stored in the `notifications` table:

```php
// Get user's notifications
$notifications = $user->notifications;

// Get unread notifications
$unread = $user->unreadNotifications;

// Mark as read
$user->unreadNotifications->markAsRead();
```
