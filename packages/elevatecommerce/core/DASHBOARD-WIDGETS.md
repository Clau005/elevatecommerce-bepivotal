# Dashboard Widget Registry

The Dashboard Widget Registry allows packages to register widgets that appear on the admin dashboard.

## Basic Usage

Register widgets in your package's `ServiceProvider`:

```php
use ElevateCommerce\Core\Support\Dashboard\DashboardRegistry;

public function boot()
{
    DashboardRegistry::register('my.widget', [
        'view' => 'my-package::widgets.stats',
        'data' => [
            'title' => 'Total Sales',
            'value' => '$12,345',
        ],
        'position' => 'stats',
        'order' => 10,
        'width' => 'quarter',
    ]);
}
```

## Configuration Options

| Option | Type | Description | Default |
|--------|------|-------------|---------|
| `view` | string | Blade view path | `null` |
| `component` | string | Blade component name | `null` |
| `data` | array | Data passed to view/component | `[]` |
| `position` | string | Widget position | `'main'` |
| `order` | int | Sort order (lower = higher) | `100` |
| `width` | string | Widget width | `'full'` |
| `permissions` | array | Required permissions | `[]` |
| `enabled` | bool | Whether widget is enabled | `true` |

## Available Positions

- `stats` - Top stats grid (4 columns)
- `main` - Main content area
- `sidebar` - Right sidebar (future)
- `top` - Above stats (future)
- `bottom` - Below main content (future)

## Available Widths

- `full` - 100% width
- `half` - 50% width (2 columns)
- `third` - 33.33% width (3 columns)
- `quarter` - 25% width (4 columns)

## Examples

### Stats Card Widget

```php
DashboardRegistry::register('stats.orders', [
    'view' => 'core::admin.widgets.stats-card',
    'data' => [
        'title' => 'Total Orders',
        'value' => '1,234',
        'icon' => 'fas fa-shopping-cart',
        'iconBg' => 'bg-blue-100',
        'iconColor' => 'text-blue-600',
        'change' => 12.5,
        'changeLabel' => 'from last month',
    ],
    'position' => 'stats',
    'order' => 10,
    'width' => 'quarter',
]);
```

### Custom Widget View

```php
DashboardRegistry::register('recent.orders', [
    'view' => 'my-package::widgets.recent-orders',
    'data' => [
        'orders' => Order::latest()->take(5)->get(),
    ],
    'position' => 'main',
    'order' => 20,
    'width' => 'full',
]);
```

### Activity Feed Widget

```php
DashboardRegistry::register('recent.activity', [
    'view' => 'core::admin.widgets.recent-activity',
    'data' => [
        'title' => 'Recent Activity',
        'activities' => [
            [
                'icon' => 'fas fa-shopping-cart',
                'title' => 'New order #1234',
                'description' => 'John Doe placed an order',
                'time' => '5 minutes ago',
            ],
            [
                'icon' => 'fas fa-user',
                'title' => 'New customer',
                'description' => 'Jane Smith registered',
                'time' => '1 hour ago',
            ],
        ],
    ],
    'position' => 'main',
    'order' => 30,
    'width' => 'full',
]);
```

### Widget with Permissions

```php
DashboardRegistry::register('admin.stats', [
    'view' => 'my-package::widgets.admin-stats',
    'data' => ['stats' => $adminStats],
    'position' => 'main',
    'order' => 10,
    'width' => 'half',
    'permissions' => ['view.admin.stats'],
]);
```

### Dynamic Data Widget

```php
DashboardRegistry::register('live.sales', [
    'view' => 'my-package::widgets.live-sales',
    'data' => function() {
        return [
            'sales' => Sale::today()->sum('total'),
            'count' => Sale::today()->count(),
        ];
    },
    'position' => 'stats',
    'order' => 15,
    'width' => 'quarter',
]);
```

## Built-in Widget Components

### Stats Card

Use the built-in stats card widget:

```php
'view' => 'core::admin.widgets.stats-card',
'data' => [
    'title' => 'Widget Title',
    'value' => '123',
    'icon' => 'fas fa-icon',
    'iconBg' => 'bg-blue-100',
    'iconColor' => 'text-blue-600',
    'change' => 5.2,              // Optional: percentage change
    'changeLabel' => 'vs last week', // Optional: change label
]
```

### Recent Activity

Use the built-in activity feed widget:

```php
'view' => 'core::admin.widgets.recent-activity',
'data' => [
    'title' => 'Recent Activity',
    'activities' => [
        [
            'icon' => 'fas fa-circle',
            'title' => 'Activity title',
            'description' => 'Activity description',
            'time' => '5 minutes ago',
        ],
    ],
]
```

## Creating Custom Widget Views

Create a Blade view in your package:

```blade
{{-- resources/views/widgets/my-widget.blade.php --}}
@props(['title' => 'My Widget', 'data' => []])

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    
    <div class="space-y-2">
        @foreach($data as $item)
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                <span class="text-sm font-medium text-gray-900">{{ $item['value'] }}</span>
            </div>
        @endforeach
    </div>
</div>
```

Register it:

```php
DashboardRegistry::register('my.custom.widget', [
    'view' => 'my-package::widgets.my-widget',
    'data' => [
        'title' => 'Custom Widget',
        'data' => [
            ['label' => 'Item 1', 'value' => '100'],
            ['label' => 'Item 2', 'value' => '200'],
        ],
    ],
    'position' => 'main',
    'order' => 40,
    'width' => 'half',
]);
```

## Managing Widgets

### Remove a Widget

```php
DashboardRegistry::remove('widget.key');
```

### Check if Widget Exists

```php
if (DashboardRegistry::has('widget.key')) {
    // Widget exists
}
```

### Get a Specific Widget

```php
$widget = DashboardRegistry::get('widget.key');
```

### Get All Widgets

```php
$allWidgets = DashboardRegistry::all();
```

## Best Practices

1. **Unique Keys**: Use namespaced keys like `package.widget.name`
2. **Order Numbers**: Use increments of 10 for easy insertion
3. **Positions**: Use `stats` for metrics, `main` for detailed widgets
4. **Widths**: Match width to content (stats = quarter, tables = full)
5. **Performance**: Cache expensive data queries
6. **Permissions**: Restrict sensitive widgets to authorized users
7. **Responsive**: Ensure widgets work on mobile devices

## Complete Example

```php
namespace YourPackage\Providers;

use Illuminate\Support\ServiceProvider;
use ElevateCommerce\Core\Support\Dashboard\DashboardRegistry;
use YourPackage\Models\YourModel;

class YourPackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerDashboardWidgets();
    }

    protected function registerDashboardWidgets()
    {
        // Stats widget
        DashboardRegistry::register('yourpackage.stats.total', [
            'view' => 'core::admin.widgets.stats-card',
            'data' => [
                'title' => 'Total Items',
                'value' => YourModel::count(),
                'icon' => 'fas fa-items',
                'iconBg' => 'bg-purple-100',
                'iconColor' => 'text-purple-600',
                'change' => 8.3,
            ],
            'position' => 'stats',
            'order' => 50,
            'width' => 'quarter',
        ]);

        // Custom widget
        DashboardRegistry::register('yourpackage.recent.items', [
            'view' => 'yourpackage::widgets.recent-items',
            'data' => [
                'items' => YourModel::latest()->take(10)->get(),
            ],
            'position' => 'main',
            'order' => 50,
            'width' => 'half',
            'permissions' => ['view.yourpackage'],
        ]);
    }
}
```

## Color Schemes

Use consistent Tailwind colors for widget styling:

- **Blue**: `bg-blue-100`, `text-blue-600` - General/Orders
- **Green**: `bg-green-100`, `text-green-600` - Revenue/Success
- **Purple**: `bg-purple-100`, `text-purple-600` - Customers/Users
- **Yellow**: `bg-yellow-100`, `text-yellow-600` - Products/Inventory
- **Red**: `bg-red-100`, `text-red-600` - Alerts/Issues
- **Gray**: `bg-gray-100`, `text-gray-600` - Neutral/Info
