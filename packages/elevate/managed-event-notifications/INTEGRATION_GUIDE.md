# Integration Guide

## Quick Start

### 1. Install the Package

Add to your main `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/elevate/managed-event-notifications"
        }
    ],
    "require": {
        "elevate/managed-event-notifications": "*"
    }
}
```

Then run:

```bash
composer update elevate/managed-event-notifications
```

### 2. Publish Assets

```bash
php artisan vendor:publish --tag=managed-notifications-config
php artisan vendor:publish --tag=managed-notifications-templates
php artisan migrate
```

### 3. Configure Resend

1. Sign up at [resend.com](https://resend.com)
2. Get your API key from the dashboard
3. Verify your domain (for production)
4. Add to `.env`:

```env
MAIL_MAILER=resend
RESEND_API_KEY=re_your_api_key_here
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Store Name"
```

### 4. Configure Staff Recipients

```env
STORE_OWNER_EMAIL=owner@yourdomain.com
STAFF_ORDER_EMAILS=staff1@yourdomain.com,staff2@yourdomain.com
```

### 5. Add to Your Order Flow

```php
use Elevate\ManagedEventNotifications\Facades\ManagedNotifications;

// In your order creation logic
public function createOrder($orderData, $customer)
{
    $order = Order::create($orderData);
    
    // Send customer confirmation
    ManagedNotifications::send('order.confirmation', $order, $customer);
    
    // Notify staff
    ManagedNotifications::sendToStaff('staff.order.new', [
        'number' => $order->number,
        'total' => $order->formatted_total,
        'customer_name' => $customer->name,
        'customer_email' => $customer->email,
        'admin_order_url' => route('admin.orders.show', $order),
    ]);
    
    return $order;
}
```

## Integration Points

### Order Lifecycle

```php
// Order Placed
ManagedNotifications::send('order.confirmation', $order, $customer);
ManagedNotifications::sendToStaff('staff.order.new', $orderData);

// Order Shipped
ManagedNotifications::send('shipping.confirmation', $shipmentData, $customer);

// Order Delivered
ManagedNotifications::send('delivery.delivered', $deliveryData, $customer);

// Order Canceled
ManagedNotifications::send('order.canceled', $cancellationData, $customer);
```

### Pickup & Delivery

```php
// Ready for Pickup
ManagedNotifications::send('pickup.ready', [
    'number' => $order->number,
    'pickup_location' => $store->full_address,
    'pickup_hours' => $store->business_hours,
    'order_url' => route('orders.show', $order),
], $customer);

// Out for Delivery
ManagedNotifications::send('delivery.out_for_delivery', [
    'number' => $order->number,
    'estimated_delivery_time' => '2-4 PM',
    'delivery_address' => $order->shipping_address,
], $customer);
```

### Gift Cards & Store Credit

```php
// New Gift Card
ManagedNotifications::send('gift_card.new', [
    'code' => $giftCard->code,
    'balance' => $giftCard->formatted_balance,
    'sender_name' => $sender->name,
    'message' => $giftCard->message,
    'shop_url' => route('shop'),
], $recipient);

// Store Credit Issued
ManagedNotifications::send('store_credit.issued', [
    'amount' => $credit->formatted_amount,
    'new_balance' => $customer->formatted_credit_balance,
    'reason' => 'Refund for order #' . $order->number,
], $customer);
```

### Staff Alerts

```php
// Payment Failure
ManagedNotifications::sendToStaff('staff.payment.failure', [
    'number' => $order->number,
    'amount' => $order->formatted_total,
    'customer_name' => $customer->name,
    'error_message' => $error->message,
    'admin_order_url' => route('admin.orders.show', $order),
]);

// Inventory Failure
ManagedNotifications::sendToStaff('staff.inventory.failure', [
    'number' => $order->number,
    'customer_name' => $customer->name,
    'out_of_stock_items' => $outOfStockItems,
    'admin_order_url' => route('admin.orders.show', $order),
]);
```

## Customization

### Brand Your Templates

Edit templates in `resources/views/vendor/managed-notifications/`:

1. Update colors and styling
2. Add your logo
3. Customize messaging
4. Add social media links
5. Include custom footer content

### Add Custom Notifications

1. Add to `config/managed-notifications.php`:

```php
'customer_notifications' => [
    'custom.notification' => [
        'enabled' => true,
        'subject' => 'Custom Subject',
        'template' => 'customer.custom.notification',
        'channels' => ['mail'],
    ],
],
```

2. Create template at `resources/views/vendor/managed-notifications/customer/custom/notification.blade.php`

3. Send it:

```php
ManagedNotifications::send('custom.notification', $data, $customer);
```

## Testing

### Send Test Emails

```bash
# Test customer notification
php artisan notifications:test order.confirmation your-email@example.com

# Test staff notification
php artisan notifications:test staff.order.new admin@example.com
```

### List All Notifications

```bash
php artisan notifications:list
```

## Production Checklist

- [ ] Verify domain with Resend
- [ ] Update `MAIL_FROM_ADDRESS` to your domain
- [ ] Configure staff email recipients
- [ ] Customize all email templates
- [ ] Test all notification types
- [ ] Enable queue workers for async sending
- [ ] Set up monitoring for failed notifications
- [ ] Configure retry logic for failed emails

## Troubleshooting

### Emails Not Sending

1. Check Resend API key is correct
2. Verify domain is authenticated with Resend
3. Check queue workers are running: `php artisan queue:work`
4. Review logs: `storage/logs/laravel.log`

### Templates Not Found

1. Ensure templates are published: `php artisan vendor:publish --tag=managed-notifications-templates`
2. Check template path in config
3. Verify template names match config

### Staff Not Receiving Emails

1. Check `STAFF_ORDER_EMAILS` in `.env`
2. Verify emails are comma-separated with no spaces
3. Check notification is enabled in config

## Best Practices

1. **Always queue notifications** - Don't block user requests
2. **Test in staging first** - Use Resend's test mode
3. **Monitor delivery rates** - Check Resend dashboard
4. **Keep templates simple** - Mobile-friendly, clear CTAs
5. **Provide unsubscribe options** - For marketing emails
6. **Log notification events** - For debugging and analytics
7. **Handle failures gracefully** - Retry logic, fallbacks
