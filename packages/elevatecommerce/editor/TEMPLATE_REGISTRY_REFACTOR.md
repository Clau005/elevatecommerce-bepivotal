# Template Registry Refactoring

## Overview

Refactored the model variable name mapping system to use the centralized `TemplateRegistry` instead of hardcoded arrays in `PageRenderService`.

---

## What Changed

### Before: Hardcoded Model Variable Map

**PageRenderService.php**
```php
class PageRenderService
{
    protected array $modelVariableMap = [
        \ElevateCommerce\Collections\Models\Collection::class => 'collection',
        \Elevate\Product\Models\Product::class => 'product',
    ];

    public function getModelVariableName($model): ?string
    {
        if (!$model) return null;
        
        $modelClass = get_class($model);
        
        if (isset($this->modelVariableMap[$modelClass])) {
            return $this->modelVariableMap[$modelClass];
        }
        
        // Check parent classes...
        return null;
    }

    public function registerModelType(string $modelClass, string $variableName): void
    {
        $this->modelVariableMap[$modelClass] = $variableName;
    }
}
```

**AppServiceProvider.php**
```php
// Had to register in TWO places!
$templateRegistry->register(TestingPurchasable::class, [
    'label' => 'Product',
    'plural_label' => 'Products',
    // ...
]);

// Separate registration for variable name
$renderService->registerModelType(TestingPurchasable::class, 'product');
```

**Problems:**
- ❌ Duplicate configuration in two places
- ❌ Easy to forget one registration
- ❌ Hardcoded model classes in service
- ❌ Not extensible without modifying core code

---

### After: Centralized Template Registry

**TemplateRegistry.php** (Enhanced)
```php
class TemplateRegistry
{
    public function register(string $modelClass, array $config): void
    {
        $basename = class_basename($modelClass);
        
        $this->registeredModels[$modelClass] = array_merge([
            'label' => $basename,
            'plural_label' => str($basename)->plural()->toString(),
            'variable_name' => str($basename)->lower()->toString(), // ✅ Auto-generated
            'icon' => null,
            'description' => null,
            'default_route_pattern' => '/' . str($basename)->lower()->plural()->toString() . '/{slug}',
            'preview_data_provider' => null,
        ], $config);
    }

    public function getVariableNameForInstance($model): ?string
    {
        if (!$model) return null;

        $modelClass = get_class($model);
        
        // Check direct mapping
        if ($this->has($modelClass)) {
            return $this->getVariableName($modelClass);
        }

        // Check parent classes
        foreach ($this->registeredModels as $class => $config) {
            if ($model instanceof $class) {
                return $config['variable_name'];
            }
        }

        return null;
    }
}
```

**PageRenderService.php** (Simplified)
```php
class PageRenderService
{
    public function __construct(
        protected TemplateRegistry $templateRegistry
    ) {}

    public function getModelVariableName($model): ?string
    {
        return $this->templateRegistry->getVariableNameForInstance($model);
    }
}
```

**AppServiceProvider.php** (Single Registration)
```php
// Everything in ONE place! ✅
$templateRegistry->register(TestingPurchasable::class, [
    'label' => 'Product',
    'plural_label' => 'Products',
    'variable_name' => 'product', // ✅ Explicit variable name
    'icon' => 'shopping-bag',
    'description' => 'Product detail pages',
    'default_route_pattern' => '/products/{slug}',
    'preview_data_provider' => function() {
        return TestingPurchasable::where('is_active', true)
            ->inRandomOrder()
            ->first();
    },
]);
```

**Benefits:**
- ✅ Single source of truth
- ✅ All model configuration in one place
- ✅ Auto-generated variable names (can be overridden)
- ✅ Fully extensible via service providers
- ✅ No hardcoded model classes in core

---

## How It Works

### 1. Model Registration

When you register a model in the `TemplateRegistry`, you can optionally specify a `variable_name`:

```php
$templateRegistry->register(Product::class, [
    'label' => 'Product',
    'variable_name' => 'product', // Optional - auto-generated if omitted
    // ... other config
]);
```

**Auto-generation:**
- `Product` → `product`
- `Collection` → `collection`
- `BlogPost` → `blogpost`
- `TestingPurchasable` → `testingpurchasable`

### 2. Variable Name Retrieval

When rendering a template, the `PageRenderService` asks the registry:

```php
$model = $collection; // Collection instance
$variableName = $this->templateRegistry->getVariableNameForInstance($model);
// Returns: 'collection'
```

### 3. Template Rendering

The variable is then passed to Blade templates:

```php
$viewData = [
    'page' => $page,
    'model' => $model,
    'collection' => $model, // ✅ Specific variable name
];

return view('themes.default.sections.collection-hero.index', $viewData);
```

Now in your Blade template:

```blade
<h1>{{ $collection->name }}</h1>

@foreach($collection->products as $product)
    <div>{{ $product->name }}</div>
@endforeach
```

---

## Migration Guide

### For Existing Models

If you already have models registered in the `TemplateRegistry`, add the `variable_name` field:

**Before:**
```php
$templateRegistry->register(MyModel::class, [
    'label' => 'My Model',
    'plural_label' => 'My Models',
]);
```

**After:**
```php
$templateRegistry->register(MyModel::class, [
    'label' => 'My Model',
    'plural_label' => 'My Models',
    'variable_name' => 'myModel', // ✅ Add this
]);
```

### For New Models

Just register once in your service provider:

```php
public function boot(): void
{
    if ($this->app->bound(TemplateRegistry::class)) {
        $registry = $this->app->make(TemplateRegistry::class);
        
        $registry->register(YourModel::class, [
            'label' => 'Your Model',
            'plural_label' => 'Your Models',
            'variable_name' => 'yourModel', // Variable name in templates
            'icon' => 'icon-name',
            'description' => 'Description for admin UI',
            'default_route_pattern' => '/your-models/{slug}',
            'preview_data_provider' => function() {
                return YourModel::inRandomOrder()->first();
            },
        ]);
    }
}
```

### Remove Old Registrations

**Delete these lines** from your service providers:

```php
// ❌ DELETE THIS - No longer needed
if ($this->app->bound(PageRenderService::class)) {
    $renderService = $this->app->make(PageRenderService::class);
    $renderService->registerModelType(YourModel::class, 'yourModel');
}
```

---

## Updated Model Registrations

### Collection (Built-in)

**Location:** `packages/elevatecommerce/collections/src/CollectionsServiceProvider.php`

```php
$registry->register(Collection::class, [
    'label' => 'Collection',
    'plural_label' => 'Collections',
    'variable_name' => 'collection', // ✅ Added
    'icon' => 'folder',
    'description' => 'Collection pages',
    'default_route_pattern' => '/collections/{slug}',
    'preview_data_provider' => function() {
        return Collection::with(['products'])
            ->inRandomOrder()
            ->first();
    },
]);
```

### TestingPurchasable (Example)

**Location:** `app/Providers/AppServiceProvider.php`

```php
$templateRegistry->register(TestingPurchasable::class, [
    'label' => 'Product',
    'plural_label' => 'Products',
    'variable_name' => 'product', // ✅ Added
    'icon' => 'shopping-bag',
    'description' => 'Product detail pages',
    'default_route_pattern' => '/products/{slug}',
    'preview_data_provider' => function() {
        return TestingPurchasable::where('is_active', true)
            ->inRandomOrder()
            ->first();
    },
]);
```

---

## Template Registry API

### Registration

```php
$registry->register(string $modelClass, array $config): void
```

**Config Options:**
- `label` (string) - Display name (e.g., "Product")
- `plural_label` (string) - Plural display name (e.g., "Products")
- `variable_name` (string) - Variable name in templates (e.g., "product")
- `icon` (string|null) - Icon identifier
- `description` (string|null) - Description for admin UI
- `default_route_pattern` (string) - Default route pattern (e.g., "/products/{slug}")
- `preview_data_provider` (callable|null) - Function to get preview data

### Retrieval

```php
// Get all registered models
$registry->all(): array

// Get config for specific model
$registry->get(string $modelClass): ?array

// Check if model is registered
$registry->has(string $modelClass): bool

// Get variable name for model class
$registry->getVariableName(string $modelClass): ?string

// Get variable name for model instance
$registry->getVariableNameForInstance($model): ?string

// Get preview data
$registry->getPreviewData(string $modelClass)

// Get options for dropdowns
$registry->getOptions(): array
```

---

## Benefits Summary

### 1. **Single Source of Truth**
All model configuration in one place - the `TemplateRegistry`

### 2. **Cleaner Code**
No more duplicate registrations or hardcoded arrays

### 3. **Better DX**
Developers only need to register once in their service provider

### 4. **Auto-generation**
Variable names auto-generated from class name (can be overridden)

### 5. **Extensibility**
Easy to add new models without touching core code

### 6. **Type Safety**
Full IDE autocomplete and type hinting

### 7. **Consistency**
Same pattern for all templatable models

---

## Testing

### Verify Variable Names

```php
// In tinker or test
$registry = app(TemplateRegistry::class);

// Check Collection
$collection = Collection::first();
$varName = $registry->getVariableNameForInstance($collection);
// Should return: 'collection'

// Check Product
$product = TestingPurchasable::first();
$varName = $registry->getVariableNameForInstance($product);
// Should return: 'product'
```

### Verify Template Rendering

```php
// Render a collection page
$response = $this->get('/collections/featured');

// Check that $collection variable is available in template
// (View the rendered HTML or use Blade debugging)
```

---

## Future Enhancements

### 1. **Cache Variable Name Lookups**
```php
public function getVariableNameForInstance($model): ?string
{
    $modelClass = get_class($model);
    
    return Cache::remember("template.var.{$modelClass}", 3600, function () use ($model) {
        // ... existing logic
    });
}
```

### 2. **Validation**
```php
public function register(string $modelClass, array $config): void
{
    // Validate variable_name format
    if (isset($config['variable_name']) && !preg_match('/^[a-z][a-zA-Z0-9]*$/', $config['variable_name'])) {
        throw new \InvalidArgumentException("Invalid variable_name format");
    }
    
    // ... existing logic
}
```

### 3. **IDE Helper Generation**
```php
// Generate IDE helper file with all registered variables
php artisan ide-helper:templates
```

---

## Troubleshooting

### Variable not available in template

**Problem:** `$product` is undefined in Blade template

**Solution:** Check that:
1. Model is registered in `TemplateRegistry`
2. `variable_name` is set correctly
3. Template is using the correct model type

### Wrong variable name

**Problem:** Expected `$product` but got `$testingpurchasable`

**Solution:** Explicitly set `variable_name` in registration:
```php
$registry->register(TestingPurchasable::class, [
    'variable_name' => 'product', // ✅ Explicit override
    // ...
]);
```

### Model not found in registry

**Problem:** `getVariableNameForInstance()` returns `null`

**Solution:** Ensure model is registered in a service provider that runs before rendering

---

**Last Updated:** November 2025
