# Elevate Editor - Integration Guide

## 📦 Installation

### 1. Add to composer.json

```json
{
    "require": {
        "elevate/editor": "*"
    },
    "repositories": [
        {
            "type": "path",
            "url": "./packages/elevate/*"
        }
    ]
}
```

### 2. Install the package

```bash
composer require elevate/editor
```

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Publish config (optional)

```bash
php artisan vendor:publish --tag=editor-config
```

---

## 🚀 Quick Start

### Making a Model Templatable

**Step 1: Add migration for `template_id`**

```php
// In your model's migration
Schema::table('products', function (Blueprint $table) {
    $table->foreignId('template_id')->nullable()->constrained('templates');
});
```

**Step 2: Add `HasTemplate` trait to your model**

```php
use Elevate\Editor\Traits\HasTemplate;

class Product extends Model
{
    use HasTemplate;
    
    protected $fillable = ['name', 'slug', 'price', 'template_id'];
}
```

**Step 3: Register the model in your service provider**

```php
use Elevate\Editor\Services\TemplateRegistry;

class ProductServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $registry = app(TemplateRegistry::class);
        
        $registry->register(Product::class, [
            'label' => 'Product',
            'plural_label' => 'Products',
            'icon' => 'shopping-bag',
            'description' => 'Product detail pages',
            'default_route_pattern' => '/products/{slug}',
            'preview_data_provider' => fn() => Product::inRandomOrder()->first(),
        ]);
    }
}
```

**That's it!** Your model now has templates. ✅

---

## 🎨 Creating Templates in Admin

### 1. Go to Admin → Templates → Create

### 2. Fill in details:
- **Name**: "Modern Product Layout"
- **Model Type**: Select "Product" from dropdown
- **Description**: "Clean, modern product page"

### 3. Click "Edit Visual" to open the editor

### 4. Drag sections from the sidebar:
- Hero Section
- Product Info
- Related Products
- Reviews

### 5. Configure each section with data

### 6. Preview with real product data

### 7. Publish when ready

---

## 🔧 Using Templates in Your Code

### Render a Model with its Template

```php
// In your controller
public function show(Product $product)
{
    return $product->render(); // Uses assigned or default template
}
```

### Get Available Templates for a Model

```php
// In your product edit form
$templates = Product::getTemplateOptions();

// Returns:
// [
//     ['value' => 1, 'label' => 'Modern Product Layout', 'is_default' => true],
//     ['value' => 2, 'label' => 'Minimal Product Page', 'is_default' => false],
// ]
```

### Assign a Template to a Model

```php
$product->assignTemplate($templateId);

// Or in form submission
$product->update([
    'name' => $request->name,
    'template_id' => $request->template_id,
]);
```

### Check if Model Has Template

```php
if ($product->hasTemplate()) {
    // Model has a specific template assigned
}

$resolvedTemplate = $product->getResolvedTemplate();
// Returns assigned template or default template for Product
```

---

## 📄 Creating Pages (Unique Content)

### 1. Go to Admin → Pages → Create

### 2. Fill in details:
- **Title**: "About Us"
- **Slug**: "about"
- **Theme**: Select active theme

### 3. Click "Edit Visual"

### 4. Build page with sections

### 5. Publish

### 6. Page is now available at `/about`

---

## 🎭 Creating Themes

### Theme Structure

```
resources/views/themes/
└── modern/
    ├── layouts/
    │   └── default.blade.php
    ├── sections/
    │   ├── hero/
    │   │   ├── index.blade.php
    │   │   └── schema.json
    │   ├── product-grid/
    │   │   ├── index.blade.php
    │   │   └── schema.json
    │   └── testimonials/
    │       ├── index.blade.php
    │       └── schema.json
    └── snippets/
        ├── header.blade.php
        └── footer.blade.php
```

### Layout Example (`layouts/default.blade.php`)

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta_title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $meta_description ?? '' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('themes.modern.snippets.header')
    
    <main>
        {!! $sectionsHtml !!}
    </main>
    
    @include('themes.modern.snippets.footer')
</body>
</html>
```

### Section Example (`sections/hero/index.blade.php`)

```blade
<section class="hero" style="background-image: url('{{ $background_image ?? '' }}')">
    <div class="container">
        <h1>{{ $heading ?? 'Welcome' }}</h1>
        <p>{{ $subheading ?? '' }}</p>
        
        @if($button_text ?? false)
            <a href="{{ $button_url ?? '#' }}" class="btn">
                {{ $button_text }}
            </a>
        @endif
    </div>
</section>
```

### Section Schema (`sections/hero/schema.json`)

```json
{
    "name": "Hero Section",
    "description": "Large hero banner with heading and CTA",
    "category": "hero",
    "thumbnail": "/images/sections/hero.png",
    "fields": [
        {
            "name": "heading",
            "label": "Heading",
            "type": "text",
            "default": "Welcome to our store"
        },
        {
            "name": "subheading",
            "label": "Subheading",
            "type": "textarea",
            "default": ""
        },
        {
            "name": "background_image",
            "label": "Background Image",
            "type": "image",
            "default": ""
        },
        {
            "name": "button_text",
            "label": "Button Text",
            "type": "text",
            "default": "Shop Now"
        },
        {
            "name": "button_url",
            "label": "Button URL",
            "type": "text",
            "default": "/products"
        }
    ]
}
```

---

## 🔗 Using Model Data in Sections

### In Template Sections

When rendering a template with a model, the model is available as `$model`:

```blade
{{-- sections/product-info/index.blade.php --}}
<div class="product-info">
    <h1>{{ $model->name }}</h1>
    <p class="price">${{ number_format($model->price, 2) }}</p>
    <p class="description">{{ $model->description }}</p>
    
    @if($model->images->count() > 0)
        <img src="{{ $model->images->first()->url }}" alt="{{ $model->name }}">
    @endif
</div>
```

### Template Variables

You can also use template variables in section configuration:

```json
{
    "heading": "{{model.name}}",
    "price": "{{model.price}}",
    "image": "{{model.featured_image}}"
}
```

These will be automatically replaced with actual model data.

---

## 🎯 Advanced Usage

### Custom Template Data

Override `getTemplateData()` in your model to customize what's available:

```php
class Product extends Model
{
    use HasTemplate;
    
    public function getTemplateData(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'formatted_price' => '$' . number_format($this->price, 2),
            'images' => $this->images,
            'category' => $this->category,
            'related_products' => $this->relatedProducts()->take(4)->get(),
            'reviews_count' => $this->reviews()->count(),
            'average_rating' => $this->reviews()->avg('rating'),
        ];
    }
}
```

### Preview Data Provider

Provide custom preview data for the editor:

```php
$registry->register(Product::class, [
    'label' => 'Product',
    'preview_data_provider' => function() {
        return Product::with(['images', 'category', 'reviews'])
            ->where('is_featured', true)
            ->inRandomOrder()
            ->first();
    },
]);
```

### Rendering Pages Programmatically

```php
use Elevate\Editor\Services\RenderEngine;
use Elevate\Editor\Models\Page;

$renderEngine = app(RenderEngine::class);
$page = Page::where('slug', 'about')->first();

return $renderEngine->renderPage($page, $isPreview = false);
```

### Rendering Templates Programmatically

```php
use Elevate\Editor\Services\TemplateResolver;

$resolver = app(TemplateResolver::class);
$product = Product::find(1);

return $resolver->renderModel($product, $isPreview = false);
```

---

## 🛠️ Frontend Routes

### Automatic Route Registration

Add to your `routes/web.php`:

```php
use Elevate\Editor\Models\Page;
use Elevate\Editor\Models\Template;
use Elevate\Editor\Services\RenderEngine;
use Elevate\Editor\Services\TemplateResolver;

// Pages
Route::get('/{slug}', function ($slug) {
    $page = Page::where('slug', $slug)
        ->published()
        ->firstOrFail();
    
    return app(RenderEngine::class)->renderPage($page);
})->where('slug', '^(?!admin|api).*$');

// Products (example)
Route::get('/products/{slug}', function ($slug) {
    $product = Product::where('slug', $slug)->firstOrFail();
    return app(TemplateResolver::class)->renderModel($product);
});

// Collections (example)
Route::get('/collections/{slug}', function ($slug) {
    $collection = Collection::where('slug', $slug)->firstOrFail();
    return app(TemplateResolver::class)->renderModel($collection);
});
```

---

## 📊 Admin Integration

### Add Template Dropdown to Product Edit Form

```blade
{{-- In your product edit form --}}
<div class="form-group">
    <label for="template_id">Template</label>
    <select name="template_id" id="template_id" class="form-control">
        <option value="">Use Default Template</option>
        @foreach(\App\Models\Product::getTemplateOptions() as $option)
            <option value="{{ $option['value'] }}" 
                    {{ old('template_id', $product->template_id) == $option['value'] ? 'selected' : '' }}>
                {{ $option['label'] }}
                @if($option['is_default'])
                    (Default)
                @endif
            </option>
        @endforeach
    </select>
</div>
```

---

## 🎨 Customization

### Custom Section Categories

Edit `config/editor.php`:

```php
'section_categories' => [
    'hero' => 'Hero Sections',
    'products' => 'Product Sections',
    'custom' => 'My Custom Category',
],
```

### Cache Configuration

```php
'cache_duration' => env('EDITOR_CACHE_DURATION', 3600), // 1 hour
```

### Image Upload Settings

```php
'images' => [
    'disk' => 'public',
    'path' => 'editor/images',
    'max_size' => 5120, // KB
    'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
],
```

---

## 🔍 Troubleshooting

### Templates Not Showing

1. Check model is registered in service provider
2. Run `php artisan config:clear`
3. Check database has templates for that model type

### Sections Not Rendering

1. Verify theme is active
2. Check section files exist in theme folder
3. Check section schema.json is valid JSON
4. Clear view cache: `php artisan view:clear`

### Preview Not Working

1. Check model has data
2. Verify `preview_data_provider` returns valid model
3. Check browser console for JavaScript errors

---

## 📚 API Reference

### TemplateRegistry

```php
$registry = app(TemplateRegistry::class);

// Register a model
$registry->register($modelClass, $config);

// Get all registered models
$registry->all();

// Check if registered
$registry->has($modelClass);

// Get options for dropdown
$registry->getOptions();
```

### TemplateResolver

```php
$resolver = app(TemplateResolver::class);

// Get templates for a model type
$resolver->getTemplatesForModel(Product::class);

// Get default template
$resolver->getDefaultTemplate(Product::class);

// Resolve template for model instance
$resolver->resolveTemplateForModel($product);

// Render a model
$resolver->renderModel($product, $isPreview);
```

### RenderEngine

```php
$engine = app(RenderEngine::class);

// Render a page
$engine->renderPage($page, $isPreview);

// Render a template with model
$engine->renderTemplate($template, $model, $isPreview);

// Get available sections for theme
$engine->getAvailableSections($theme);
```

### EditorService

```php
$editor = app(EditorService::class);

// Save draft
$editor->saveDraft($template, $configuration);

// Publish
$editor->publish($template, $userId, $changeNotes);

// Discard draft
$editor->discardDraft($template);
```

---

## 🎉 Complete Example

### 1. Create a "Watch" model with templates

```php
// Migration
Schema::create('watches', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->decimal('price', 10, 2);
    $table->text('description');
    $table->foreignId('template_id')->nullable()->constrained('templates');
    $table->timestamps();
});

// Model
class Watch extends Model
{
    use HasTemplate;
    
    protected $fillable = ['name', 'slug', 'price', 'description', 'template_id'];
}

// Service Provider
class WatchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $registry = app(TemplateRegistry::class);
        
        $registry->register(Watch::class, [
            'label' => 'Watch',
            'plural_label' => 'Watches',
            'icon' => 'clock',
            'description' => 'Luxury watch detail pages',
            'default_route_pattern' => '/watches/{slug}',
        ]);
    }
}

// Route
Route::get('/watches/{slug}', function ($slug) {
    $watch = Watch::where('slug', $slug)->firstOrFail();
    return $watch->render();
});
```

### 2. Create template in admin

- Go to Admin → Templates → Create
- Select "Watch" model
- Build with sections
- Publish

### 3. Done! 🎉

Watches now render with beautiful templates!

---

## 📞 Support

For issues or questions, please refer to the main documentation or contact the development team.
