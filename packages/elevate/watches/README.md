# Elevate Watches Package

A complete e-commerce package for selling watches with template and collection support.

## Installation

1. **Require the package** (if not already in your workspace):
```bash
composer require elevate/watches
```

2. **Run migrations**:
```bash
php artisan migrate
```

3. **Install templates and sections**:
```bash
php artisan watches:install
```

4. **Seed sample watches** (optional):
```bash
php artisan watches:seed
```

## Features

### ✅ Watch Model
- Full e-commerce support with `Purchasable` trait
- Template assignment via `HasTemplate` trait
- Tag support via `HasTags` trait
- Polymorphic collections support
- Watch-specific fields:
  - Brand, Model Number
  - Movement Type (Automatic, Quartz, Manual)
  - Case Material & Diameter
  - Water Resistance
  - Strap Material
  - Pricing with sale support
  - Stock management

### ✅ Template Support
- `$watch` variable available in all templates
- Access any watch property: `$watch->brand`, `$watch->price`, etc.
- Automatic template resolution

### ✅ Collection Support
- Add watches to collections
- Mix with products in same collection
- Polymorphic relationship support

## Usage

### Creating a Watch
```php
$watch = Watch::create([
    'name' => 'Rolex Submariner',
    'slug' => 'rolex-submariner',
    'brand' => 'Rolex',
    'movement_type' => 'Automatic',
    'case_material' => 'Stainless Steel',
    'case_diameter' => 40,
    'water_resistance' => 300,
    'price' => 8500.00,
    'sku' => 'ROL-SUB-001',
    'stock_quantity' => 5,
]);
```

### In Templates
```blade
<h1>{{ $watch->name }}</h1>
<p>Brand: {{ $watch->brand }}</p>
<p>Price: {{ $watch->formatted_price }}</p>

@if($watch->is_on_sale)
    <span>Save {{ $watch->discount_percentage }}%!</span>
@endif
```

### Adding to Collections
```php
$collection->collectables()->create([
    'collectable_type' => Watch::class,
    'collectable_id' => $watch->id,
]);
```

## Files Created

### ✅ Core Files
- ✅ `src/Models/Watch.php` - Watch model with Purchasable, HasTags, HasTemplate traits
- ✅ `database/migrations/2024_11_12_000001_create_watches_table.php` - Database schema
- ✅ `src/WatchesServiceProvider.php` - Service provider with model type registration
- ✅ `src/Http/Controllers/Admin/WatchController.php` - Full CRUD admin controller
- ✅ `routes/admin.php` - Admin routes
- ✅ `routes/web.php` - Frontend routes (placeholder)

### ✅ Admin Views
- ✅ `resources/views/admin/watches/index.blade.php` - List all watches
- ✅ `resources/views/admin/watches/create.blade.php` - Create new watch
- ✅ `resources/views/admin/watches/edit.blade.php` - Edit existing watch
- ✅ `resources/views/admin/watches/_form.blade.php` - Shared form partial

### ⏳ Still Needed
- ⏳ `src/Console/Commands/InstallWatchesCommand.php` - Installation command
- ⏳ `src/Console/Commands/SeedWatchesCommand.php` - Sample data seeder
- ⏳ `src/Http/Controllers/WatchWebController.php` - Frontend display
- ⏳ `resources/templates/` - Default template JSON files
- ⏳ `resources/sections/` - Default section Blade files

## Getting Started

1. **Add to composer.json** (if not using path repository):
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/elevate/watches"
        }
    ]
}
```

2. **Install the package**:
```bash
composer require elevate/watches
```

3. **Run migrations**:
```bash
php artisan migrate
```

4. **Access admin panel**:
Navigate to `/admin/watches` to manage watches.

## What Works Now

✅ **Full Admin CRUD** - Create, read, update, delete watches
✅ **Model Type Registration** - `$watch` variable available in templates
✅ **Navigation** - "Watches" menu item in admin sidebar
✅ **Template Assignment** - Assign templates to individual watches
✅ **Collection Support** - Add watches to collections
✅ **E-commerce Ready** - Purchasable trait for cart/checkout

## Next Steps

Create the install command and seed command for easy setup, plus default sections for themes.
