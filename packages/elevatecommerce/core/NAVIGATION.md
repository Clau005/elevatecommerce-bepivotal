# Navigation Registry

The Navigation Registry allows packages to register menu items that appear in the admin sidebar.

## Basic Usage

Register navigation items in your package's `ServiceProvider`:

```php
use ElevateCommerce\Core\Support\Navigation\NavigationRegistry;

public function boot()
{
    NavigationRegistry::register('admin', [
        'label' => 'Products',
        'icon' => 'fas fa-box',
        'route' => 'admin.products.index',
        'order' => 20,
    ]);
}
```

## Configuration Options

| Option | Type | Description | Default |
|--------|------|-------------|---------|
| `label` | string | Menu item text | Required |
| `icon` | string | Font Awesome icon class | `null` |
| `route` | string | Laravel route name | `null` |
| `url` | string | Direct URL (alternative to route) | `null` |
| `badge` | string | Badge text (e.g., count) | `null` |
| `children` | array | Submenu items | `[]` |
| `order` | int | Sort order (lower = higher) | `100` |
| `permissions` | array | Required permissions | `[]` |
| `active` | string\|callable | Active state pattern | `null` |

## Examples

### Simple Menu Item

```php
NavigationRegistry::register('admin', [
    'label' => 'Orders',
    'icon' => 'fas fa-shopping-cart',
    'route' => 'admin.orders.index',
    'order' => 10,
]);
```

### Menu Item with Badge

```php
NavigationRegistry::register('admin', [
    'label' => 'Notifications',
    'icon' => 'fas fa-bell',
    'route' => 'admin.notifications.index',
    'badge' => '5',
    'order' => 90,
]);
```

### Menu with Children (Dropdown)

```php
NavigationRegistry::register('admin', [
    'label' => 'Products',
    'icon' => 'fas fa-box',
    'order' => 20,
    'active' => 'admin.products.*',
    'children' => [
        [
            'label' => 'All Products',
            'route' => 'admin.products.index',
        ],
        [
            'label' => 'Add Product',
            'route' => 'admin.products.create',
        ],
        [
            'label' => 'Categories',
            'route' => 'admin.categories.index',
        ],
    ],
]);
```

### Menu Item with Permissions

```php
NavigationRegistry::register('admin', [
    'label' => 'Settings',
    'icon' => 'fas fa-cog',
    'route' => 'admin.settings.index',
    'order' => 100,
    'permissions' => ['manage.settings'],
]);
```

### Custom Active State

```php
NavigationRegistry::register('admin', [
    'label' => 'Analytics',
    'icon' => 'fas fa-chart-line',
    'route' => 'admin.analytics.index',
    'order' => 30,
    'active' => function() {
        return request()->routeIs('admin.analytics.*') 
            || request()->routeIs('admin.reports.*');
    },
]);
```

## Using Fluent Builder

You can also use the `NavigationItem` builder for a more fluent API:

```php
use ElevateCommerce\Core\Support\Navigation\NavigationItem;
use ElevateCommerce\Core\Support\Navigation\NavigationRegistry;

$item = NavigationItem::make('Products')
    ->icon('fas fa-box')
    ->route('admin.products.index')
    ->order(20)
    ->badge('12')
    ->permissions(['view.products']);

NavigationRegistry::register('admin', $item->toArray());
```

## Available Icon Sets

The admin panel uses Font Awesome 6. Common icons:

- `fas fa-home` - Home
- `fas fa-shopping-cart` - Orders/Cart
- `fas fa-box` - Products
- `fas fa-users` - Customers
- `fas fa-chart-line` - Analytics
- `fas fa-cog` - Settings
- `fas fa-bell` - Notifications
- `fas fa-dollar-sign` - Revenue/Money
- `fas fa-truck` - Shipping
- `fas fa-tags` - Categories/Tags

## Best Practices

1. **Order Numbers**: Use increments of 10 (10, 20, 30) to allow easy insertion of items between
2. **Icons**: Always provide an icon for better UX
3. **Permissions**: Add permissions for admin-only or role-specific items
4. **Active States**: Use route patterns for accurate highlighting
5. **Children**: Keep submenu depth to 1 level for simplicity

## Complete Example

```php
namespace YourPackage\Providers;

use Illuminate\Support\ServiceProvider;
use ElevateCommerce\Core\Support\Navigation\NavigationRegistry;

class YourPackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerNavigation();
    }

    protected function registerNavigation()
    {
        NavigationRegistry::register('admin', [
            'label' => 'My Package',
            'icon' => 'fas fa-puzzle-piece',
            'order' => 50,
            'active' => 'admin.mypackage.*',
            'children' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.mypackage.dashboard',
                ],
                [
                    'label' => 'Items',
                    'route' => 'admin.mypackage.items.index',
                ],
                [
                    'label' => 'Settings',
                    'route' => 'admin.mypackage.settings',
                    'permissions' => ['manage.mypackage'],
                ],
            ],
        ]);
    }
}
```
