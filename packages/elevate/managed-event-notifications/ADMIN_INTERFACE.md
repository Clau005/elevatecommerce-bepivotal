# Admin Interface Documentation

## Overview

The Managed Notifications package includes a comprehensive admin interface for managing, previewing, and testing all notification types. The interface is automatically registered in your admin navigation.

## Features

### 1. **Notification Management Dashboard**
- View all customer and staff notifications
- See enabled/disabled status at a glance
- Quick preview and test buttons for each notification
- Organized by customer and staff categories

### 2. **Live Preview System**
- Preview exactly how emails will look to recipients
- Sample data automatically generated for each notification type
- Print-friendly preview mode
- View email headers (From, To, Subject)
- See the actual rendered HTML

### 3. **Test Email Functionality**
- Send test notifications to any email address
- Modal interface for quick testing
- Real-time feedback on send status
- Uses actual notification system (not mocked)

### 4. **Settings Page**
- View current email configuration
- See Resend API settings
- Check staff recipient lists
- View queue configuration
- Quick links to Resend dashboard

### 5. **Notification History**
- View all sent notifications
- Filter by type, recipient, and status
- Pagination for large datasets
- Read/unread status tracking

## Navigation Integration

The package automatically registers itself in your admin navigation using the same pattern as other packages:

```php
$nav->add('Managed Notifications', '/admin/managed-notifications', [
    'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
    'pattern' => 'admin/managed-notifications',
    'group' => 'settings',
    'order' => 900,
]);
```

## Routes

All admin routes are registered in `app/Routing/Registrars/AdminRoutesRegistrar.php` and prefixed with `/admin/managed-notifications`:

| Route | Method | Purpose |
|-------|--------|---------|
| `/` | GET | Main dashboard |
| `/settings` | GET | Settings page |
| `/history` | GET | Notification history |
| `/preview/{type}` | GET | Preview notification |
| `/test/{type}` | POST | Send test email |

Routes are defined in `packages/elevate/managed-event-notifications/routes/admin.php` and automatically loaded by the AdminRoutesRegistrar.

## Accessing the Interface

Once the package is installed, you'll find "Managed Notifications" in your admin navigation under the Settings group.

### Direct URLs:
- Dashboard: `/admin/managed-notifications`
- Settings: `/admin/managed-notifications/settings`
- History: `/admin/managed-notifications/history`

## Using the Preview Feature

1. Navigate to the main dashboard
2. Click "Preview" on any notification
3. View the rendered email with sample data
4. Check the sample data used at the bottom
5. Use the "Print" button for a clean preview

### Sample Data

The preview system automatically generates realistic sample data based on the notification type:

- **Order notifications**: Order number, items, totals, customer info
- **Shipping notifications**: Tracking numbers, delivery dates
- **Pickup notifications**: Location, hours, instructions
- **Gift cards**: Codes, balances, sender info
- **Staff notifications**: Customer details, error messages, etc.

## Sending Test Emails

1. Click "Send Test" on any notification
2. Enter an email address in the modal
3. Click "Send Test" to deliver
4. Check your inbox for the test email

**Note**: Test emails use the actual notification system, so they respect:
- Queue settings
- Email provider configuration (Resend)
- Template customizations
- All notification logic

## Settings Page

The settings page displays your current configuration:

### Email Configuration
- Email provider (Resend)
- From address
- From name

### Staff Recipients
- Store owner email
- Order notification recipients
- General notification recipients

### Queue Settings
- Queue enabled/disabled status
- Queue connection name

**Note**: Settings are read from `config/managed-notifications.php`. To change settings, edit the config file or environment variables.

## Notification History

View all notifications sent through the database channel:

- **Type**: Notification identifier
- **Recipient**: Who received it
- **Subject**: Email subject line
- **Sent**: Relative time (e.g., "2 hours ago")
- **Status**: Read/Unread

History is paginated and sorted by most recent first.

## Customization

### Styling

The admin interface uses your application's existing `<x-app>` component and styling system. All views integrate seamlessly with your admin panel's design.

To customize the notification views:

1. Publish the views:
```bash
php artisan vendor:publish --tag=managed-notifications-templates
```

2. Edit the admin views in:
```
resources/views/vendor/managed-notifications/admin/
```

The views use Tailwind CSS classes and Font Awesome icons, matching your existing admin interface.

### Adding Custom Actions

You can extend the controller to add custom functionality:

```php
// In your AppServiceProvider or custom service provider
Route::get('/admin/managed-notifications/custom', [YourController::class, 'custom'])
    ->name('admin.managed-notifications.custom');
```

## Technical Details

### Controller

`Elevate\ManagedEventNotifications\Http\Controllers\NotificationController`

Methods:
- `index()` - Main dashboard
- `settings()` - Settings page
- `history()` - Notification history
- `preview($type)` - Preview notification
- `sendTest($type)` - Send test email

### Views

All views use the `<x-app>` component from your application's layout system, ensuring:
- Consistent navigation with other admin pages
- Unified styling across the admin panel
- Access to all shared components and features
- Alpine.js for interactivity

### Route Exclusion

The package registers route exclusion to prevent conflicts:

```php
\App\Routing\Services\RouteExclusionRegistry::exclude('admin/managed-notifications');
```

## Security

- All routes require authentication (`auth` middleware)
- CSRF protection on POST requests
- Email validation on test sends
- No sensitive data exposed in previews

## Troubleshooting

### Navigation Not Showing

Ensure your application has the navigation service registered:

```php
if ($this->app->bound('admin.navigation')) {
    // Navigation will be registered
}
```

### Preview Errors

If a preview fails:
1. Check the template exists
2. Verify template path in config
3. Check Laravel logs for details
4. Use the preview-error view for debugging

### Test Emails Not Sending

1. Verify Resend API key is set
2. Check queue workers are running
3. Review `storage/logs/laravel.log`
4. Test with queue disabled temporarily

## Best Practices

1. **Test Before Production**: Always test notifications in staging first
2. **Use Preview**: Preview all notifications before enabling
3. **Monitor History**: Regularly check notification history for issues
4. **Keep Templates Updated**: Customize templates to match your brand
5. **Configure Recipients**: Ensure staff emails are correct in `.env`

## Future Enhancements

Planned features for future versions:
- Inline template editor
- A/B testing interface
- Analytics dashboard
- Bulk test sending
- Template version control
- Notification scheduling UI
