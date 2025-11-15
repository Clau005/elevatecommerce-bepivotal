# Settings Registry

The Settings Registry allows packages to register settings pages that appear on the admin settings index page as a grid of cards.

## Basic Usage

Register settings pages in your package's `ServiceProvider`:

```php
use ElevateCommerce\Core\Support\Settings\SettingsRegistry;

public function boot()
{
    SettingsRegistry::register('currencies', [
        'title' => 'Currencies',
        'description' => 'Manage currencies and exchange rates',
        'icon' => 'fas fa-dollar-sign',
        'route' => 'admin.settings.currencies',
        'group' => 'localization',
        'order' => 20,
        'color' => 'green',
    ]);
}
```

## Configuration Options

| Option | Type | Description | Default |
|--------|------|-------------|---------|
| `title` | string | Settings page title | Required |
| `description` | string | Brief description of the page | Required |
| `icon` | string | Font Awesome icon class | `'fas fa-cog'` |
| `route` | string | Laravel route name | `null` |
| `url` | string | Direct URL (alternative to route) | `null` |
| `group` | string | Group/category name | `'general'` |
| `order` | int | Sort order within group | `100` |
| `permissions` | array | Required permissions | `[]` |
| `badge` | string | Optional badge (e.g., "New") | `null` |
| `color` | string | Card color theme | `'blue'` |

## Available Colors

- `blue` - Default, general settings
- `green` - Financial, currencies
- `purple` - Users, customers
- `yellow` - Products, inventory
- `red` - Security, alerts
- `gray` - System, technical
- `indigo` - Advanced features
- `pink` - Marketing, promotions

## Available Groups

Groups organize settings pages into sections. Common groups:

- `general` - Basic store settings
- `localization` - Languages, currencies, regions
- `sales` - Orders, payments, shipping
- `products` - Catalog, inventory
- `customers` - Customer management
- `marketing` - SEO, promotions
- `system` - Technical, advanced

## Examples

### Basic Settings Page

```php
SettingsRegistry::register('general', [
    'title' => 'General',
    'description' => 'Manage your store name, logo, timezone, and other basic settings',
    'icon' => 'fas fa-store',
    'route' => 'admin.settings.general',
    'group' => 'general',
    'order' => 10,
    'color' => 'blue',
]);
```

### Settings Page with Badge

```php
SettingsRegistry::register('payments', [
    'title' => 'Payment Methods',
    'description' => 'Configure payment gateways and options',
    'icon' => 'fas fa-credit-card',
    'route' => 'admin.settings.payments',
    'group' => 'sales',
    'order' => 10,
    'color' => 'green',
    'badge' => '3 Active',
]);
```

### Settings Page with Permissions

```php
SettingsRegistry::register('advanced', [
    'title' => 'Advanced Settings',
    'description' => 'Configure advanced system settings and features',
    'icon' => 'fas fa-sliders-h',
    'route' => 'admin.settings.advanced',
    'group' => 'system',
    'order' => 90,
    'color' => 'gray',
    'permissions' => ['manage.advanced.settings'],
]);
```

### Multiple Settings in Same Group

```php
// Localization group
SettingsRegistry::register('languages', [
    'title' => 'Languages',
    'description' => 'Manage store languages and translations',
    'icon' => 'fas fa-language',
    'route' => 'admin.settings.languages',
    'group' => 'localization',
    'order' => 10,
    'color' => 'purple',
]);

SettingsRegistry::register('currencies', [
    'title' => 'Currencies',
    'description' => 'Manage currencies and exchange rates',
    'icon' => 'fas fa-dollar-sign',
    'route' => 'admin.settings.currencies',
    'group' => 'localization',
    'order' => 20,
    'color' => 'green',
]);

SettingsRegistry::register('regions', [
    'title' => 'Regions',
    'description' => 'Configure countries and regions',
    'icon' => 'fas fa-globe',
    'route' => 'admin.settings.regions',
    'group' => 'localization',
    'order' => 30,
    'color' => 'blue',
]);
```

## Creating Settings Pages

### 1. Register the Settings Page

In your `ServiceProvider`:

```php
use ElevateCommerce\Core\Support\Settings\SettingsRegistry;

public function boot()
{
    SettingsRegistry::register('my-settings', [
        'title' => 'My Settings',
        'description' => 'Configure my package settings',
        'icon' => 'fas fa-puzzle-piece',
        'route' => 'admin.settings.my-settings',
        'group' => 'general',
        'order' => 50,
        'color' => 'indigo',
    ]);
}
```

### 2. Create the Route

In your package's routes file:

```php
Route::middleware(['web', 'auth:admin'])->prefix('admin/settings')->group(function () {
    Route::get('/my-settings', [MySettingsController::class, 'index'])
        ->name('admin.settings.my-settings');
    
    Route::put('/my-settings', [MySettingsController::class, 'update'])
        ->name('admin.settings.my-settings.update');
});
```

### 3. Create the Controller

```php
namespace YourPackage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MySettingsController extends Controller
{
    public function index()
    {
        return view('your-package::settings.index');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'setting_key' => 'required|string',
            // ... other validation rules
        ]);

        // Save settings logic here

        return redirect()
            ->route('admin.settings.my-settings')
            ->with('success', 'Settings updated successfully!');
    }
}
```

### 4. Create the View

```blade
{{-- resources/views/settings/index.blade.php --}}
@extends('core::admin.layouts.app')

@section('title', 'My Settings')

@section('content')
<div class="space-y-6">
    <!-- Page Header with Back Button -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.settings.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">My Settings</h1>
            </div>
            <p class="mt-1 text-sm text-gray-600">Configure your package settings</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.my-settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Settings Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Settings</h2>
            
            <!-- Your form fields here -->
            
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end space-x-4">
            <a 
                href="{{ route('admin.settings.index') }}"
                class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </a>
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
            >
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
```

## Managing Settings Pages

### Get All Pages by Group

```php
$generalSettings = SettingsRegistry::getByGroup('general');
$localizationSettings = SettingsRegistry::getByGroup('localization');
```

### Get All Groups

```php
$allGroups = SettingsRegistry::getGroups();
// Returns: ['general' => [...], 'localization' => [...], ...]
```

### Get Specific Page

```php
$page = SettingsRegistry::get('currencies');
```

### Remove a Page

```php
SettingsRegistry::remove('my-settings');
```

### Check if Page Exists

```php
if (SettingsRegistry::has('currencies')) {
    // Page exists
}
```

## Best Practices

1. **Unique Keys**: Use descriptive, unique keys like `package.setting-name`
2. **Groups**: Use existing groups when possible for consistency
3. **Order**: Use increments of 10 (10, 20, 30) for easy insertion
4. **Icons**: Choose relevant Font Awesome icons
5. **Colors**: Match colors to the setting type (green for money, blue for general)
6. **Descriptions**: Keep descriptions concise but informative
7. **Permissions**: Add permissions for sensitive settings
8. **Back Button**: Always include a back button to settings index
9. **Success Messages**: Show feedback after saving settings
10. **Validation**: Validate all form inputs properly

## Common Icons

- `fas fa-store` - General/Store
- `fas fa-dollar-sign` - Currencies/Money
- `fas fa-language` - Languages
- `fas fa-globe` - Regions/Countries
- `fas fa-credit-card` - Payments
- `fas fa-truck` - Shipping
- `fas fa-envelope` - Email/Notifications
- `fas fa-shield-alt` - Security
- `fas fa-users` - Customers
- `fas fa-box` - Products
- `fas fa-chart-line` - Analytics
- `fas fa-sliders-h` - Advanced/System

## Complete Example

```php
namespace YourPackage\Providers;

use Illuminate\Support\ServiceProvider;
use ElevateCommerce\Core\Support\Settings\SettingsRegistry;

class YourPackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerSettingsPages();
    }

    protected function registerSettingsPages()
    {
        // Register main settings page
        SettingsRegistry::register('yourpackage.main', [
            'title' => 'Your Package',
            'description' => 'Configure your package main settings',
            'icon' => 'fas fa-puzzle-piece',
            'route' => 'admin.settings.yourpackage',
            'group' => 'general',
            'order' => 50,
            'color' => 'indigo',
        ]);

        // Register advanced settings
        SettingsRegistry::register('yourpackage.advanced', [
            'title' => 'Advanced Options',
            'description' => 'Configure advanced features and options',
            'icon' => 'fas fa-sliders-h',
            'route' => 'admin.settings.yourpackage.advanced',
            'group' => 'system',
            'order' => 80,
            'color' => 'gray',
            'permissions' => ['manage.yourpackage.advanced'],
        ]);
    }
}
```

## Settings Index Page

The settings index page (`/admin/settings`) automatically displays all registered settings pages in a responsive grid layout, grouped by their `group` property. Each card shows:

- Icon with color theme
- Title and description
- Optional badge
- Hover effects with arrow indicator

No additional configuration needed - just register your settings pages and they'll appear automatically!
