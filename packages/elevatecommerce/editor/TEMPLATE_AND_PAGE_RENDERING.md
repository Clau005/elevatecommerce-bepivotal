# Template & Page Rendering System

Complete guide to how the ElevateCommerce Visual Editor handles template rendering, page configuration, and section management.

---

## 📋 Table of Contents

- [Architecture Overview](#architecture-overview)
- [Section Structure](#section-structure)
- [Page Configuration](#page-configuration)
- [Rendering Flow](#rendering-flow)
- [Adding Sections to Pages](#adding-sections-to-pages)
- [Data Flow](#data-flow)
- [Examples](#examples)

---

## Architecture Overview

The editor uses a **component-based architecture** where:

1. **Sections** are reusable UI components with configurable fields
2. **Pages** are collections of sections with specific data
3. **Templates** are dynamic page layouts for models (products, collections, etc.)
4. **Themes** contain the section definitions and layouts

### Key Models

```
Theme
├── Pages (many)
│   ├── configuration (JSON)
│   └── draft_configuration (JSON)
└── Sections (file-based)
    ├── configuration.json (schema)
    └── index.blade.php (template)
```

---

## Section Structure

Each section lives in a folder with two files:

```
resources/themes/default/sections/hero/
├── configuration.json    # Field schema and metadata
└── index.blade.php       # Blade template for rendering
```

### configuration.json

Defines the section's **metadata** and **editable fields**:

```json
{
  "name": "Hero Section",
  "description": "A prominent hero section with title, subtitle, and CTA",
  "category": "headers",
  "fields": [
    {
      "name": "title",
      "label": "Title",
      "type": "text",
      "required": true,
      "default": "Welcome to Our Site",
      "placeholder": "Enter hero title"
    },
    {
      "name": "background_image",
      "label": "Background Image",
      "type": "image",
      "required": false
    },
    {
      "name": "height",
      "label": "Section Height",
      "type": "range",
      "min": 300,
      "max": 800,
      "step": 50,
      "unit": "px",
      "default": 500
    }
  ]
}
```

**Field Types Available:**
- `text` - Single-line text
- `textarea` - Multi-line text
- `richtext` / `html` - HTML editor
- `number` - Numeric input
- `select` - Dropdown
- `checkbox` - Boolean toggle
- `image` - Media picker
- `url` - URL input
- `color` - Color picker
- `range` - Slider
- `repeater` - Nested repeating fields

See [SECTION_FIELD_TYPES.md](./SECTION_FIELD_TYPES.md) for complete reference.

### index.blade.php

The **Blade template** that renders the section with the configured data:

```blade
<section class="hero-section" 
         style="background-color: {{ $background_color ?? '#1f2937' }}; 
                min-height: {{ $height ?? 500 }}px;">
    <div class="hero-content">
        @if(isset($title))
            <h1>{{ $title }}</h1>
        @endif
        
        @if(isset($button_text) && isset($button_url))
            <a href="{{ $button_url }}">{{ $button_text }}</a>
        @endif
    </div>
</section>
```

**Variables Available in Templates:**
- All field values from `configuration.json` (e.g., `$title`, `$background_color`)
- `$data` - Array of all section data
- `$sectionId` - Unique section ID
- `$isPreview` - Boolean indicating preview mode
- `$model` - For dynamic templates (product, collection, etc.)

---

## Page Configuration

Pages store their configuration in a **JSON structure** in the database:

### Database Schema

```php
// pages table
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('theme_id');
    $table->string('title');
    $table->string('slug');
    $table->json('configuration');          // Published config
    $table->json('draft_configuration');    // Draft being edited
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->enum('status', ['draft', 'published']);
    $table->boolean('is_active');
    $table->timestamps();
});
```

### Configuration Structure

```json
{
  "basic_info": {
    "layout": "default"
  },
  "sections": [
    {
      "id": "hero-1234567890",
      "component": "hero",
      "visible": true,
      "data": {
        "title": "Welcome to Our Store",
        "subtitle": "Discover amazing products",
        "button_text": "Shop Now",
        "button_url": "/products",
        "background_image": "https://example.com/hero.jpg",
        "background_color": "#1f2937",
        "text_color": "#ffffff",
        "height": 600
      }
    },
    {
      "id": "features-9876543210",
      "component": "features",
      "visible": true,
      "data": {
        "title": "Our Features",
        "subtitle": "What makes us special",
        "background_color": "#ffffff",
        "features": [
          {
            "title": "Fast Shipping",
            "description": "Get your order in 2-3 days",
            "icon": "lightning"
          },
          {
            "title": "Quality Products",
            "description": "Only the best for our customers",
            "icon": "star"
          }
        ]
      }
    }
  ],
  "seo": {
    "meta_title": "Home - My Store",
    "meta_description": "Welcome to our amazing store"
  }
}
```

### Draft vs Published

- **`configuration`** - The live, published version shown to visitors
- **`draft_configuration`** - The version being edited in the visual editor
- When you click **Publish**, `draft_configuration` is copied to `configuration`

---

## Rendering Flow

### 1. Page Request

```
User visits /about
    ↓
Route matched to PageController
    ↓
PageRenderService::render($slug)
```

### 2. Load Page & Configuration

```php
// PageRenderService.php
public function render(string $slug)
{
    $page = Page::where('slug', $slug)
        ->where('is_active', true)
        ->where('status', 'published')
        ->firstOrFail();
    
    $theme = $page->theme;
    $configuration = $page->configuration; // Published config
    
    return $this->renderWithConfiguration(
        $theme->slug,
        $configuration,
        $pageData
    );
}
```

### 3. Render Sections

```php
protected function renderSections(string $themeSlug, array $sections): string
{
    $html = '';
    
    foreach ($sections as $sectionConfig) {
        $componentSlug = $sectionConfig['component']; // e.g., 'hero'
        $sectionData = $sectionConfig['data'];
        
        // Build view path: themes.default.sections.hero.index
        $viewPath = "themes.{$themeSlug}.sections.{$componentSlug}.index";
        
        // Render with data
        $html .= view($viewPath, $sectionData)->render();
    }
    
    return $html;
}
```

### 4. Inject into Layout

```blade
<!-- themes/default/layouts/default.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>{{ $page->title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">
</head>
<body>
    {!! $sectionsHtml !!}
</body>
</html>
```

---

## Adding Sections to Pages

### In the Visual Editor

1. **User clicks "Add Section"**
   ```javascript
   // VisualEditor.vue
   showAddSectionModal = true
   ```

2. **User selects a section type** (e.g., "Hero Section")
   ```javascript
   addSection(sectionSlug) {
     const sectionDef = this.availableSections.find(s => s.slug === sectionSlug);
     
     const newSection = {
       id: `${sectionSlug}-${Date.now()}`,
       component: sectionSlug,
       data: this.getDefaultSectionData(sectionDef.schema),
       visible: true
     };
     
     this.pageConfig.sections.push(newSection);
   }
   ```

3. **Default data is populated** from `configuration.json`
   ```javascript
   getDefaultSectionData(schema) {
     const defaultData = {};
     
     schema.fields.forEach(field => {
       defaultData[field.name] = field.default || '';
     });
     
     return defaultData;
   }
   ```

4. **User configures the section** in the Configuration Sidebar
   - Fields are rendered based on `configuration.json`
   - Changes update `pageConfig.sections[index].data`

5. **Preview updates in real-time**
   ```javascript
   updatePreview() {
     axios.post('/api/editor/update-preview', {
       type: 'page',
       id: this.page.id,
       configuration: this.pageConfig
     });
   }
   ```

6. **User saves draft**
   ```javascript
   saveDraft() {
     axios.post('/api/editor/save-draft', {
       type: 'page',
       id: this.page.id,
       configuration: this.pageConfig
     });
     // Saves to draft_configuration
   }
   ```

7. **User publishes**
   ```javascript
   publishChanges() {
     axios.post('/api/editor/publish', {
       type: 'page',
       id: this.page.id
     });
     // Copies draft_configuration → configuration
   }
   ```

---

## Data Flow

### Editor → Database → Frontend

```
┌─────────────────────────────────────────────────────────────┐
│                    Visual Editor (Vue)                       │
│                                                              │
│  pageConfig = {                                              │
│    sections: [                                               │
│      {                                                       │
│        component: 'hero',                                    │
│        data: { title: 'Welcome', ... }                       │
│      }                                                       │
│    ]                                                         │
│  }                                                           │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ Save Draft
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    Database (pages table)                    │
│                                                              │
│  draft_configuration: {                                      │
│    sections: [                                               │
│      { component: 'hero', data: {...} }                      │
│    ]                                                         │
│  }                                                           │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ Publish
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    Database (pages table)                    │
│                                                              │
│  configuration: {                                            │
│    sections: [                                               │
│      { component: 'hero', data: {...} }                      │
│    ]                                                         │
│  }                                                           │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ Render
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                  PageRenderService (PHP)                     │
│                                                              │
│  foreach section in configuration.sections:                 │
│    render themes.default.sections.{component}.index         │
│    with section.data                                         │
└─────────────────────────────────────────────────────────────┘
                            │
                            │
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    Frontend (HTML)                           │
│                                                              │
│  <section class="hero-section">                              │
│    <h1>Welcome</h1>                                          │
│  </section>                                                  │
└─────────────────────────────────────────────────────────────┘
```

---

## Examples

### Example 1: Creating a Custom Section

**1. Create section folder:**
```
resources/themes/default/sections/testimonials/
```

**2. Define schema (configuration.json):**
```json
{
  "name": "Testimonials Section",
  "description": "Display customer testimonials",
  "category": "content",
  "fields": [
    {
      "name": "title",
      "label": "Section Title",
      "type": "text",
      "default": "What Our Customers Say"
    },
    {
      "name": "testimonials",
      "label": "Testimonial",
      "type": "repeater",
      "fields": [
        {
          "name": "name",
          "label": "Customer Name",
          "type": "text"
        },
        {
          "name": "quote",
          "label": "Quote",
          "type": "textarea"
        },
        {
          "name": "avatar",
          "label": "Avatar",
          "type": "image"
        }
      ]
    }
  ]
}
```

**3. Create template (index.blade.php):**
```blade
<section class="testimonials-section">
    @if(isset($title))
        <h2>{{ $title }}</h2>
    @endif
    
    @if(isset($testimonials) && is_array($testimonials))
        <div class="testimonials-grid">
            @foreach($testimonials as $testimonial)
                <div class="testimonial">
                    @if(isset($testimonial['avatar']))
                        <img src="{{ $testimonial['avatar'] }}" alt="{{ $testimonial['name'] }}">
                    @endif
                    <p>"{{ $testimonial['quote'] }}"</p>
                    <strong>{{ $testimonial['name'] }}</strong>
                </div>
            @endforeach
        </div>
    @endif
</section>
```

**4. Use in editor:**
- Section automatically appears in "Add Section" modal
- User can add it to any page
- Configure testimonials via repeater field
- Preview updates in real-time

### Example 2: Page Configuration in Database

After adding sections in the editor, the page's `configuration` looks like:

```json
{
  "basic_info": {
    "layout": "default"
  },
  "sections": [
    {
      "id": "hero-1699876543210",
      "component": "hero",
      "visible": true,
      "data": {
        "title": "Welcome to Our Store",
        "subtitle": "Premium products for everyone",
        "button_text": "Shop Now",
        "button_url": "/products",
        "background_color": "#1f2937",
        "height": 600
      }
    },
    {
      "id": "testimonials-1699876543211",
      "component": "testimonials",
      "visible": true,
      "data": {
        "title": "What Our Customers Say",
        "testimonials": [
          {
            "name": "John Doe",
            "quote": "Amazing products and service!",
            "avatar": "https://example.com/john.jpg"
          },
          {
            "name": "Jane Smith",
            "quote": "I love shopping here!",
            "avatar": "https://example.com/jane.jpg"
          }
        ]
      }
    }
  ],
  "seo": {
    "meta_title": "Home - My Store",
    "meta_description": "Welcome to our store"
  }
}
```

### Example 3: Rendering Process

When a user visits the page:

```php
// 1. PageRenderService loads the page
$page = Page::where('slug', 'home')->first();
$configuration = $page->configuration;

// 2. Extracts sections array
$sections = $configuration['sections'];
// [
//   { component: 'hero', data: {...} },
//   { component: 'testimonials', data: {...} }
// ]

// 3. Renders each section
foreach ($sections as $section) {
    $viewPath = "themes.default.sections.{$section['component']}.index";
    // themes.default.sections.hero.index
    // themes.default.sections.testimonials.index
    
    $html .= view($viewPath, $section['data'])->render();
}

// 4. Injects into layout
return view('themes.default.layouts.default', [
    'sectionsHtml' => $html,
    'page' => $page
]);
```

---

## Key Concepts Summary

### Section Definition (File-Based)
- **Location:** `resources/themes/default/sections/{section-name}/`
- **Files:** `configuration.json` + `index.blade.php`
- **Purpose:** Define reusable UI components with configurable fields

### Page Configuration (Database)
- **Storage:** `pages.configuration` (JSON column)
- **Structure:** Array of section instances with specific data
- **Purpose:** Store which sections appear on a page and their values

### Rendering Pipeline
1. Load page configuration from database
2. Loop through `sections` array
3. For each section, render `themes.{theme}.sections.{component}.index`
4. Pass section's `data` as Blade variables
5. Inject rendered HTML into layout

### Editor Workflow
1. **Add Section** → Creates new entry in `pageConfig.sections`
2. **Configure** → Updates `section.data` via sidebar
3. **Save Draft** → Stores in `draft_configuration`
4. **Publish** → Copies to `configuration`
5. **Render** → Uses `configuration` for live site

---

## Related Documentation

- [SECTION_FIELD_TYPES.md](./SECTION_FIELD_TYPES.md) - Complete field type reference
- [QUICKSTART.md](./QUICKSTART.md) - Getting started guide
- [Theme Development Guide](#) - Creating custom themes

---

## Questions & Troubleshooting

### How do I add a new section?
Create a folder in `resources/themes/default/sections/` with `configuration.json` and `index.blade.php`. It will automatically appear in the editor.

### Where is page data stored?
In the `pages` table, `configuration` column (JSON). Draft changes are in `draft_configuration`.

### How do sections receive their data?
The `PageRenderService` extracts `section.data` from the configuration and passes it as Blade variables to the section template.

### Can I use dynamic data (products, collections)?
Yes! Templates (not pages) can access models. Use `$product`, `$collection`, etc. in your section templates.

### How do I customize a section?
Edit the `index.blade.php` file in the section's folder. Changes apply to all instances of that section.

### How do I change section fields?
Edit `configuration.json` in the section's folder. Existing page data may need migration if you remove fields.

---

**Last Updated:** November 2025
