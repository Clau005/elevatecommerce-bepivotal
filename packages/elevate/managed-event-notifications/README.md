# Managed Event Notifications

A comprehensive notification system for ElevateCommerce that allows you to send customizable notifications to customers and staff members about various events.

## Features

- 🔔 **Customer Notifications**: Order confirmations, shipping updates, local pickup, gift cards, etc.
- 👥 **Staff Notifications**: New orders, returns, payment failures, inventory issues, etc.
- 📧 **Resend Integration**: Modern email delivery with Resend
- 📝 **File-based Templates**: Easily customizable Blade templates stored in files
- 🎨 **Template Customization**: Edit templates to match your brand
- 🔌 **Multiple Channels**: Email, database, and extensible for SMS/Slack
- ⚙️ **Configurable**: Enable/disable specific notifications
- 🎯 **Event-driven**: Automatic notifications based on system events
- 🖥️ **Admin Interface**: Full-featured admin UI for managing, previewing, and testing notifications
- 🔍 **Live Preview**: See exactly how emails will look before sending
- ✉️ **Test Sending**: Send test emails to any address with one click

## Installation

```bash
composer require elevate/managed-event-notifications
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=managed-notifications-config
```

Publish the templates:

```bash
php artisan vendor:publish --tag=managed-notifications-templates
```

## Usage

### Admin Interface

Once installed, access the admin interface at `/admin/managed-notifications` where you can:

- **View all notifications** - Browse customer and staff notification types
- **Preview emails** - See exactly how notifications will look with sample data
- **Send test emails** - Test any notification by sending to your email
- **View settings** - Check email configuration and staff recipients
- **See history** - View all sent notifications

See [ADMIN_INTERFACE.md](ADMIN_INTERFACE.md) for complete admin documentation.

### Triggering Notifications

```php
use Elevate\ManagedEventNotifications\Facades\ManagedNotifications;

// Send order confirmation
ManagedNotifications::send('order.confirmation', $order, $customer);

// Send staff notification
ManagedNotifications::sendToStaff('staff.order.new', $order);
```

### Available Customer Notifications

- `order.confirmation` - Order confirmation
- `order.invoice` - Order invoice
- `order.edited` - Order edited
- `order.canceled` - Order canceled
- `shipping.confirmation` - Shipping confirmation
- `pickup.ready` - Ready for local pickup
- `pickup.confirmed` - Picked up by customer
- `delivery.out_for_delivery` - Out for local delivery
- `delivery.delivered` - Locally delivered
- `delivery.missed` - Missed local delivery
- `gift_card.new` - New gift card
- `gift_card.receipt` - Gift card receipt
- `store_credit.issued` - Store credit issued
- `draft_order.invoice` - Draft order invoice

### Available Staff Notifications

- `staff.order.new` - New order
- `staff.order.return` - New return request
- `staff.order.draft` - New draft order
- `staff.subscription.new` - New subscription order
- `staff.subscription.canceled` - Canceled subscription
- `staff.payment.failure` - Payment failure
- `staff.inventory.failure` - Inventory failure
- `staff.attribution.edited` - Sales attribution edited

### Customizing Templates

Templates are stored in `resources/views/vendor/managed-notifications/`. Each template is a Blade file that receives relevant data.

Example template structure:
```blade
@component('mail::message')
# Order Confirmation

Hi {{ $customer->name }},

Thank you for your order #{{ $order->number }}!

@component('mail::button', ['url' => $orderUrl])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
```

## Resend Configuration

Add your Resend API key to `.env`:

```env
RESEND_API_KEY=re_your_api_key_here
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## License

MIT
