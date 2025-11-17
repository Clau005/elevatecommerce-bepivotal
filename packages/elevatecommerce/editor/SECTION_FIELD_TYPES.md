# Section Field Types Reference

Complete guide to all supported field types in the Visual Editor's section configuration sidebar.

## 📋 Table of Contents
- [Basic Input Types](#basic-input-types)
- [Selection Types](#selection-types)
- [Media Types](#media-types)
- [Advanced Types](#advanced-types)
- [Repeater Fields](#repeater-fields)
- [Field Properties](#field-properties)
- [Examples](#examples)

---

## Basic Input Types

### `text`
Single-line text input for short strings.

```json
{
  "name": "title",
  "type": "text",
  "label": "Title",
  "placeholder": "Enter title...",
  "default": "Welcome"
}
```

**Use cases:** Titles, names, labels, short descriptions

---

### `textarea`
Multi-line text input for longer content.

```json
{
  "name": "description",
  "type": "textarea",
  "label": "Description",
  "placeholder": "Enter description...",
  "rows": 4,
  "default": ""
}
```

**Use cases:** Descriptions, paragraphs, multi-line text

---

### `richtext` / `html`
HTML/Rich text editor with monospace font.

```json
{
  "name": "content",
  "type": "richtext",
  "label": "HTML Content",
  "placeholder": "<p>Enter HTML...</p>",
  "default": ""
}
```

**Use cases:** HTML content, rich text formatting, embedded code

---

### `number`
Numeric input with optional min/max/step constraints.

```json
{
  "name": "height",
  "type": "number",
  "label": "Height (px)",
  "min": 100,
  "max": 1000,
  "step": 50,
  "default": 500
}
```

**Use cases:** Dimensions, quantities, numeric values

---

## Selection Types

### `select`
Dropdown selection from predefined options.

```json
{
  "name": "layout",
  "type": "select",
  "label": "Layout Style",
  "options": [
    { "label": "Default", "value": "default" },
    { "label": "Wide", "value": "wide" },
    { "label": "Narrow", "value": "narrow" }
  ],
  "default": "default"
}
```

**Use cases:** Layouts, styles, predefined choices

---

### `checkbox`
Boolean toggle for enable/disable features.

```json
{
  "name": "show_title",
  "type": "checkbox",
  "label": "Display Title",
  "checkboxLabel": "Show section title",
  "default": true
}
```

**Use cases:** Feature toggles, visibility controls, boolean flags

---

## Media Types

### `image`
Image selector with media library integration.

```json
{
  "name": "background_image",
  "type": "image",
  "label": "Background Image",
  "help": "Select an image from the media library",
  "default": ""
}
```

**Features:**
- ✅ Opens media library modal
- ✅ Browse existing media
- ✅ Upload new images
- ✅ Search and filter
- ✅ Image preview
- ✅ Remove image button

**Use cases:** Background images, logos, featured images, thumbnails

---

### `url`
URL input with validation.

```json
{
  "name": "button_url",
  "type": "url",
  "label": "Button Link",
  "placeholder": "https://example.com",
  "default": ""
}
```

**Use cases:** Links, external resources, API endpoints

---

## Advanced Types

### `color`
Color picker with hex input.

```json
{
  "name": "background_color",
  "type": "color",
  "label": "Background Color",
  "default": "#ffffff"
}
```

**Features:**
- Visual color picker
- Hex code input
- Color preview

**Use cases:** Colors, backgrounds, text colors, borders

---

### `range`
Slider input for numeric values.

```json
{
  "name": "opacity",
  "type": "range",
  "label": "Opacity",
  "min": 0,
  "max": 1,
  "step": 0.1,
  "unit": "",
  "default": 1
}
```

**Features:**
- Visual slider
- Current value display
- Optional unit suffix

**Use cases:** Opacity, spacing, sizes, percentages

---

## Repeater Fields

### `repeater`
Nested repeating fields for lists and collections.

```json
{
  "name": "features",
  "type": "repeater",
  "label": "Feature",
  "fields": [
    {
      "name": "title",
      "type": "text",
      "label": "Title",
      "default": ""
    },
    {
      "name": "description",
      "type": "textarea",
      "label": "Description",
      "default": ""
    },
    {
      "name": "icon",
      "type": "image",
      "label": "Icon",
      "default": ""
    }
  ]
}
```

**Supported nested types:**
- `text`
- `textarea`
- `image` (with media picker)
- `url`
- `select`

**Features:**
- Add/remove items
- Reorder items (drag & drop)
- Each item numbered
- Nested field validation

**Use cases:** Feature lists, galleries, testimonials, team members, FAQ items

---

## Field Properties

### Common Properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `name` | string | ✅ | Unique field identifier (used in data object) |
| `type` | string | ✅ | Field type (see types above) |
| `label` | string | ✅ | Display label in sidebar |
| `default` | any | ❌ | Default value when field is empty |
| `placeholder` | string | ❌ | Placeholder text for inputs |
| `help` | string | ❌ | Help text displayed below label |
| `required` | boolean | ❌ | Shows red asterisk if true |

### Type-Specific Properties

#### Number & Range
- `min` - Minimum value
- `max` - Maximum value
- `step` - Increment step
- `unit` - Unit suffix (range only)

#### Textarea
- `rows` - Number of visible rows (default: 4)

#### Select
- `options` - Array of `{ label, value }` objects

#### Checkbox
- `checkboxLabel` - Label next to checkbox

#### Repeater
- `fields` - Array of nested field definitions

---

## Examples

### Complete Hero Section Schema

```json
{
  "name": "Hero Section",
  "description": "Full-width hero banner with image and text",
  "icon": "hero",
  "fields": [
    {
      "name": "title",
      "type": "text",
      "label": "Hero Title",
      "placeholder": "Welcome to our site",
      "required": true,
      "default": "Welcome"
    },
    {
      "name": "subtitle",
      "type": "textarea",
      "label": "Subtitle",
      "rows": 3,
      "placeholder": "Enter subtitle...",
      "default": ""
    },
    {
      "name": "background_image",
      "type": "image",
      "label": "Background Image",
      "help": "Recommended size: 1920x1080px",
      "default": ""
    },
    {
      "name": "background_color",
      "type": "color",
      "label": "Background Color",
      "default": "#1f2937"
    },
    {
      "name": "text_color",
      "type": "color",
      "label": "Text Color",
      "default": "#ffffff"
    },
    {
      "name": "height",
      "type": "number",
      "label": "Height (px)",
      "min": 300,
      "max": 1000,
      "step": 50,
      "default": 600
    },
    {
      "name": "button_text",
      "type": "text",
      "label": "Button Text",
      "default": "Learn More"
    },
    {
      "name": "button_url",
      "type": "url",
      "label": "Button URL",
      "placeholder": "https://",
      "default": "#"
    },
    {
      "name": "show_overlay",
      "type": "checkbox",
      "label": "Dark Overlay",
      "checkboxLabel": "Add dark overlay to background",
      "default": true
    },
    {
      "name": "overlay_opacity",
      "type": "range",
      "label": "Overlay Opacity",
      "min": 0,
      "max": 1,
      "step": 0.1,
      "default": 0.5
    }
  ]
}
```

### Features List with Repeater

```json
{
  "name": "Features Section",
  "description": "Display multiple features with icons",
  "fields": [
    {
      "name": "title",
      "type": "text",
      "label": "Section Title",
      "default": "Our Features"
    },
    {
      "name": "features",
      "type": "repeater",
      "label": "Feature",
      "fields": [
        {
          "name": "icon",
          "type": "image",
          "label": "Icon",
          "default": ""
        },
        {
          "name": "title",
          "type": "text",
          "label": "Feature Title",
          "default": ""
        },
        {
          "name": "description",
          "type": "textarea",
          "label": "Description",
          "rows": 3,
          "default": ""
        },
        {
          "name": "link_url",
          "type": "url",
          "label": "Learn More Link",
          "default": ""
        }
      ]
    }
  ]
}
```

---

## Media Library Integration

The `image` field type now uses the **MediaPicker** component from the core package:

### Features:
- ✅ **Browse Media** - View all uploaded media files
- ✅ **Upload New** - Drag & drop or click to upload
- ✅ **Search** - Find media by filename
- ✅ **Filter** - Filter by type (images, videos, documents)
- ✅ **Preview** - Thumbnail previews for images
- ✅ **Pagination** - Load more as you scroll
- ✅ **Selection** - Click to select, visual confirmation

### API Endpoints:
- `GET /admin/media/api` - Fetch media list
- `POST /admin/media` - Upload new media

### Media Object Structure:
```javascript
{
  id: 123,
  url: "https://example.com/storage/media/image.jpg",
  thumbnail_url: "https://example.com/storage/media/thumbs/image.jpg",
  original_filename: "image.jpg",
  formatted_size: "2.5 MB",
  is_image: true,
  is_video: false,
  alt_text: "Description"
}
```

---

## Best Practices

### 1. **Use Appropriate Types**
- Use `text` for short strings, `textarea` for longer content
- Use `select` instead of `text` when there are predefined options
- Use `range` for values that benefit from visual feedback

### 2. **Provide Defaults**
- Always set sensible `default` values
- Prevents undefined/null errors in templates
- Improves user experience

### 3. **Add Help Text**
- Use `help` property for complex fields
- Explain expected formats, sizes, or constraints
- Guide users to make correct choices

### 4. **Validate Input**
- Use `min`/`max` for numbers and ranges
- Use `required` for essential fields
- Use `url` type for links (built-in validation)

### 5. **Organize Fields**
- Group related fields together
- Use clear, descriptive labels
- Order fields logically (top to bottom)

### 6. **Repeater Considerations**
- Keep nested fields simple
- Limit nesting depth (avoid repeaters in repeaters)
- Provide clear labels for each item

---

## Migration Notes

### Old Image Field (Deprecated)
```json
{
  "name": "image",
  "type": "image"
}
```
- Used basic button with `open-media-library` event
- Required parent component to handle media selection

### New Image Field (Current)
```json
{
  "name": "image",
  "type": "image"
}
```
- Uses MediaPicker component
- Self-contained media library modal
- No parent component changes needed
- Automatic upload, search, filter functionality

**Migration is automatic** - existing schemas work without changes!

---

## Support

For questions or issues:
- Check section `schema.json` files in `resources/views/themes/default/sections/`
- Review `ConfigurationSidebar.vue` component
- Test in Visual Editor at `/admin/visual-editor/pages`
