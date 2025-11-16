# Elevate Editor

A powerful, extensible visual page builder and template system for Laravel applications.

## ✨ Features

- 🎨 **Visual Page Builder** - Drag-and-drop interface for building pages
- 📋 **Global Templates** - Reusable templates for any model type
- 🎭 **Theme System** - File-based themes with layouts, sections, and snippets
- 🔌 **Fully Extensible** - Easy integration with any model
- 📱 **Responsive Preview** - Real-time preview across devices
- 💾 **Draft/Publish Workflow** - Edit safely without affecting live content
- 📚 **Version History** - Track changes and rollback if needed
- ⚡ **Performance Optimized** - Cached rendering for speed
- 🔍 **SEO Friendly** - Built-in meta tags and optimization

## 📦 What's Included

### Database Tables
- `themes` - Theme metadata
- `pages` - Unique pages (About, Contact, etc.)
- `templates` - Reusable templates for models
- `editor_sessions` - Active editing sessions
- `template_versions` - Version history

### Core Services
- **TemplateRegistry** - Register templatable models
- **TemplateResolver** - Resolve and render templates
- **RenderEngine** - Core rendering logic
- **EditorService** - Draft management and publishing

### Traits & Contracts
- **HasTemplate** - Add to any model for template support
- **Templatable** - Interface for templatable models

## 🚀 Quick Start

### 1. Install

```bash
composer require elevate/editor
php artisan migrate
```

### 2. Make a Model Templatable

```php
use Elevate\Editor\Traits\HasTemplate;

class Product extends Model
{
    use HasTemplate;
}
```

### 3. Register in Service Provider

```php
use Elevate\Editor\Services\TemplateRegistry;

$registry = app(TemplateRegistry::class);
$registry->register(Product::class, [
    'label' => 'Product',
    'plural_label' => 'Products',
]);
```

### 4. Use in Routes

```php
Route::get('/products/{slug}', function ($slug) {
    $product = Product::where('slug', $slug)->firstOrFail();
    return $product->render(); // Magic! ✨
});
```

## 📖 Documentation

See [INTEGRATION.md](INTEGRATION.md) for complete documentation including:

- Creating themes and sections
- Building templates in the admin
- Advanced customization
- API reference
- Troubleshooting

## 🏗️ Architecture

```
Editor Package
├── Models (Theme, Page, Template, etc.)
├── Services (Registry, Resolver, RenderEngine)
├── Traits (HasTemplate)
├── Controllers (Admin & API)
└── Views (Admin interface)

Your Application
├── Models (Product, Post, etc.)
│   └── use HasTemplate
├── Service Providers
│   └── Register models
└── Themes (File-based)
    ├── Layouts
    ├── Sections
    └── Snippets
```

## 🎯 Use Cases

- **E-commerce**: Product pages, collection pages
- **Blogs**: Post templates, author pages
- **Marketing**: Landing pages, campaign pages
- **Corporate**: About, services, team pages
- **Custom**: Any model that needs a frontend view

## 🔧 Requirements

- PHP 8.2+
- Laravel 11.0+
- MySQL/PostgreSQL

## 📝 License

Proprietary - Elevate Commerce

## 🤝 Contributing

This is an internal package. For questions or issues, contact the development team.

---

**Built with ❤️ by the Elevate team**
