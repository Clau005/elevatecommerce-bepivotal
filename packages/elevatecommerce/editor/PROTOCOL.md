# Templatable Model Protocol

## 📋 Official Protocol for Making Any Model Templatable

This is the **standard checklist** for adding template support to any model in any package.

---

## ✅ Required Steps

### Step 1: Database Migration

Add `template_id` column to your model's table:

```php
Schema::table('your_table', function (Blueprint $table) {
    $table->foreignId('template_id')
        ->nullable()
        ->after('slug') // or appropriate position
        ->constrained('templates')
        ->nullOnDelete();
});
```

**Run migration:**
```bash
php artisan make:migration add_template_id_to_your_table --table=your_table
php artisan migrate
```

---

### Step 2: Update Model

#### 2.1: Add `template_id` to `$fillable`

```php
protected $fillable = [
    'name',
    'slug',
    'template_id', // ← Add this
    // ... other fields
];
```

#### 2.2: Add `HasTemplate` trait

```php
use Elevate\Editor\Traits\HasTemplate;

class YourModel extends Model
{
    use HasTemplate;
    
    // ... rest of model
}
```

#### 2.3: (Optional) Customize template data

Override `getTemplateData()` to control what data is available in templates:

```php
public function getTemplateData(): array
{
    return [
        'name' => $this->name,
        'slug' => $this->slug,
        'description' => $this->description,
        // Add relationships
        'category' => $this->category,
        'images' => $this->images,
        // Add computed properties
        'formatted_price' => '$' . number_format($this->price, 2),
        // ... custom data
    ];
}
```

---

### Step 3: Register in Service Provider

In your package's service provider:

```php
use Elevate\Editor\Services\TemplateRegistry;

public function boot(): void
{
    // ... other boot logic
    
    $this->registerTemplatable();
}

protected function registerTemplatable(): void
{
    // Check if editor package is installed
    if (!$this->app->bound(TemplateRegistry::class)) {
        return;
    }

    $registry = $this->app->make(TemplateRegistry::class);

    $registry->register(\Your\Package\Models\YourModel::class, [
        'label' => 'Product',                    // Singular name
        'plural_label' => 'Products',            // Plural name
        'icon' => 'shopping-bag',                // Icon identifier
        'description' => 'Product detail pages', // Description
        'default_route_pattern' => '/products/{slug}',
        'preview_data_provider' => function() {
            return \Your\Package\Models\YourModel::with(['images', 'category'])
                ->inRandomOrder()
                ->first();
        },
    ]);
}
```

---

### Step 4: Add Template Selector to Admin Form

In your admin edit/create form:

```blade
{{-- Template Selection --}}
<div class="form-group">
    <label for="template_id" class="block text-sm font-medium text-gray-700 mb-1">
        Template
    </label>
    <select name="template_id" 
            id="template_id" 
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        <option value="">Use Default Template</option>
        @foreach(\Your\Package\Models\YourModel::getTemplateOptions() as $option)
            <option value="{{ $option['value'] }}" 
                    {{ old('template_id', $model->template_id ?? null) == $option['value'] ? 'selected' : '' }}>
                {{ $option['label'] }}
                @if($option['is_default'])
                    (Default)
                @endif
            </option>
        @endforeach
    </select>
    <p class="mt-1 text-sm text-gray-500">
        Choose a custom template or use the default
    </p>
</div>
```

---

### Step 5: Add Frontend Route

Add route to render the model with its template:

```php
// In routes/web.php or your package's routes
Route::get('/products/{slug}', function ($slug) {
    $product = \Your\Package\Models\YourModel::where('slug', $slug)
        ->firstOrFail();
    
    return $product->render();
})->name('products.show');
```

---

## 🎨 Optional: Package-Specific Sections

Create default sections for your model type:

### Directory Structure

```
packages/your-package/resources/sections/
├── product-hero/
│   ├── index.blade.php
│   └── schema.json
├── product-info/
│   ├── index.blade.php
│   └── schema.json
└── product-gallery/
    ├── index.blade.php
    └── schema.json
```

### Section Blade Example

```blade
{{-- resources/sections/product-info/index.blade.php --}}
<div class="product-info">
    <h1>{{ $model->name }}</h1>
    
    @if($show_price ?? true)
        <p class="price">${{ number_format($model->price, 2) }}</p>
    @endif
    
    @if($show_description ?? true)
        <div class="description">
            {!! $model->description !!}
        </div>
    @endif
</div>
```

### Section Schema Example

```json
{
    "name": "Product Info",
    "description": "Display product details",
    "category": "products",
    "thumbnail": "/images/sections/product-info.png",
    "fields": [
        {
            "name": "show_price",
            "label": "Show Price",
            "type": "checkbox",
            "default": true
        },
        {
            "name": "show_description",
            "label": "Show Description",
            "type": "checkbox",
            "default": true
        }
    ]
}
```

### Publish Sections

In your service provider:

```php
public function boot(): void
{
    // Publish sections to active theme
    $this->publishes([
        __DIR__ . '/../resources/sections' => resource_path('views/themes/default/sections'),
    ], 'product-sections');
}
```

Users can then run:
```bash
php artisan vendor:publish --tag=product-sections
```

---

## 📝 Testing Checklist

After implementing, verify:

- [ ] Model has `template_id` in database
- [ ] Model has `template_id` in `$fillable`
- [ ] Model uses `HasTemplate` trait
- [ ] Model is registered in service provider
- [ ] Template dropdown appears in admin form
- [ ] Can assign template to model instance
- [ ] Frontend route renders model with template
- [ ] `$model->render()` works
- [ ] `YourModel::getTemplateOptions()` returns templates
- [ ] Default template is marked in dropdown

---

## 🔍 Verification Commands

```bash
# Check if model is registered
php artisan tinker
>>> app(\Elevate\Editor\Services\TemplateRegistry::class)->all()

# Check available templates for model
>>> \Your\Package\Models\YourModel::getAvailableTemplates()

# Check default template
>>> \Your\Package\Models\YourModel::getDefaultTemplate()

# Test rendering
>>> $model = \Your\Package\Models\YourModel::first()
>>> $model->render()
```

---

## 🐛 Common Issues & Solutions

### Issue: "Class HasTemplate not found"
**Solution:** Run `composer dump-autoload`

### Issue: "Column template_id doesn't exist"
**Solution:** Run the migration: `php artisan migrate`

### Issue: "No templates available"
**Solution:** Create a template in Admin → Templates → Create

### Issue: "Template dropdown is empty"
**Solution:** Check model is registered in service provider

### Issue: "Rendering fails"
**Solution:** 
1. Check active theme exists
2. Check template has sections
3. Check sections exist in theme folder

---

## 📚 What You Get

Once a model follows this protocol, it automatically gets:

### Static Methods:
- `YourModel::getAvailableTemplates()` - All templates for this model
- `YourModel::getDefaultTemplate()` - Default template
- `YourModel::getTemplateOptions()` - Dropdown options

### Instance Methods:
- `$model->render()` - Render with template
- `$model->template()` - Template relationship
- `$model->assignTemplate($id)` - Assign template
- `$model->hasTemplate()` - Check if has template
- `$model->getResolvedTemplate()` - Get assigned or default
- `$model->getTemplateData()` - Data for template

### Automatic Features:
- Template selection in admin
- Frontend rendering
- Preview with real data
- Draft/publish workflow
- Version history
- SEO optimization

---

## 🎯 Example: Complete Implementation

```php
// 1. Migration
Schema::table('products', function (Blueprint $table) {
    $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
});

// 2. Model
use Elevate\Editor\Traits\HasTemplate;

class Product extends Model
{
    use HasTemplate;
    
    protected $fillable = ['name', 'slug', 'price', 'template_id'];
    
    public function getTemplateData(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'images' => $this->images,
        ];
    }
}

// 3. Service Provider
protected function registerTemplatable(): void
{
    if (!$this->app->bound(TemplateRegistry::class)) {
        return;
    }

    app(TemplateRegistry::class)->register(Product::class, [
        'label' => 'Product',
        'plural_label' => 'Products',
        'default_route_pattern' => '/products/{slug}',
    ]);
}

// 4. Route
Route::get('/products/{slug}', fn($slug) => 
    Product::where('slug', $slug)->firstOrFail()->render()
);
```

**Done! ✅** Your model now has full template support.

---

## 📖 Related Documentation

- [INTEGRATION.md](INTEGRATION.md) - Detailed integration guide
- [README.md](README.md) - Package overview
- [SUMMARY.md](SUMMARY.md) - Architecture summary

---

**Last Updated:** 2025-01-01  
**Version:** 1.0.0
