# Quick Start Guide

Get up and running with Managed Event Notifications in 5 minutes.

## Step 1: Install (2 minutes)

```bash
# Add to your main composer.json repositories section
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/elevate/managed-event-notifications"
        }
    ]
}

# Install the package
composer require elevate/managed-event-notifications

# Publish assets
php artisan vendor:publish --tag=managed-notifications-config
php artisan vendor:publish --tag=managed-notifications-templates

# Run migrations
php artisan migrate
```

## Step 2: Configure Resend (1 minute)

1. Sign up at [resend.com](https://resend.com) (free tier: 3,000 emails/month)
2. Get your API key
3. Add to `.env`:

```env
MAIL_MAILER=resend
RESEND_API_KEY=re_your_api_key_here
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Store"

# Staff emails (comma-separated)
STAFF_ORDER_EMAILS=admin@yourdomain.com
```

## Step 3: Send Your First Notification (1 minute)

```php
use Elevate\ManagedEventNotifications\Facades\ManagedNotifications;

// In your order controller or service
public function placeOrder($orderData, $customer)
{
    $order = Order::create($orderData);
    
    // Send customer confirmation - that's it!
    ManagedNotifications::send('order.confirmation', $order, $customer);
    
    // Notify staff
    ManagedNotifications::sendToStaff('staff.order.new', [
        'number' => $order->number,
        'total' => $order->formatted_total,
        'customer_name' => $customer->name,
        'admin_order_url' => route('admin.orders.show', $order),
    ]);
    
    return $order;
}
```

## Step 4: Test It (1 minute)

```bash
# List all available notifications
php artisan notifications:list

# Send a test email to yourself
php artisan notifications:test order.confirmation your-email@example.com
```

## That's It! 🎉

You now have a complete notification system. Here's what you can do next:

### Customize Templates

Edit files in `resources/views/vendor/managed-notifications/`:

```blade
{{-- resources/views/vendor/managed-notifications/customer/order/confirmation.blade.php --}}
@component('mail::message')
# 🎉 Order Confirmed!

Hi {{ $notifiable->name }},

Your order #{{ $data->number }} is confirmed!

@component('mail::button', ['url' => $data->order_url])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
```

### Add More Notifications

```php
// Shipping confirmation
ManagedNotifications::send('shipping.confirmation', [
    'number' => $order->number,
    'tracking_number' => $shipment->tracking_number,
    'tracking_url' => $shipment->tracking_url,
], $customer);

// Ready for pickup
ManagedNotifications::send('pickup.ready', [
    'number' => $order->number,
    'pickup_location' => $store->address,
    'pickup_hours' => '9 AM - 5 PM',
], $customer);

// Payment failure (staff alert)
ManagedNotifications::sendToStaff('staff.payment.failure', [
    'number' => $order->number,
    'error_message' => $error->message,
    'admin_order_url' => route('admin.orders.show', $order),
]);
```

### Configure Notifications

Edit `config/managed-notifications.php`:

```php
// Enable/disable notifications
'order.confirmation' => [
    'enabled' => true,
    'subject' => 'Order #:number Confirmed! 🎉',
    'channels' => ['mail', 'database'], // Also store in database
],
```

## Available Notifications

### Customer (12 types)
- `order.confirmation` - Order placed
- `order.invoice` - Invoice ready
- `order.canceled` - Order canceled
- `shipping.confirmation` - Order shipped
- `pickup.ready` - Ready for pickup
- `delivery.out_for_delivery` - Out for delivery
- `delivery.delivered` - Delivered
- `gift_card.new` - Gift card received
- `store_credit.issued` - Store credit added
- And more...

### Staff (8 types)
- `staff.order.new` - New order received
- `staff.order.return` - Return requested
- `staff.payment.failure` - Payment failed
- `staff.inventory.failure` - Inventory issue
- And more...

## Common Patterns

### With Laravel Events

```php
// OrderPlaced event listener
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
```

### With Model Observers

```php
class OrderObserver
{
    public function created(Order $order): void
    {
        ManagedNotifications::send('order.confirmation', $order, $order->customer);
        ManagedNotifications::sendToStaff('staff.order.new', $order->toArray());
    }
}
```

## Troubleshooting

**Emails not sending?**
- Check Resend API key is correct
- Verify queue workers are running: `php artisan queue:work`
- Check logs: `tail -f storage/logs/laravel.log`

**Templates not found?**
- Run: `php artisan vendor:publish --tag=managed-notifications-templates`
- Check template path in config

**Staff not receiving emails?**
- Verify `STAFF_ORDER_EMAILS` in `.env`
- No spaces between comma-separated emails

## Next Steps

1. ✅ Read [INTEGRATION_GUIDE.md](INTEGRATION_GUIDE.md) for detailed setup
2. ✅ Check [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md) for more code examples
3. ✅ Customize templates to match your brand
4. ✅ Set up queue workers for production
5. ✅ Monitor delivery in Resend dashboard

## Support

- 📖 Full documentation in README.md
- 💡 Examples in USAGE_EXAMPLES.md
- 🔧 Integration guide in INTEGRATION_GUIDE.md
- 📋 All features in PACKAGE_SUMMARY.md

---

**You're all set!** Start sending beautiful, professional notifications to your customers. 🚀
