# Media Picker - Quick Usage Guide

## Auto-Mount Method (Easiest)

The simplest way to use the MediaPicker is with the auto-mount feature. Just add a `div` with `data-media-picker` attribute:

### Basic Example

```blade
<div>
    <label class="block text-xs font-medium text-gray-700 mb-2">
        Product Image
    </label>
    
    <!-- Hidden input to store the URL -->
    <input type="hidden" name="image_url" value="{{ old('image_url', $product->image_url ?? '') }}">
    
    <!-- Media Picker will mount here -->
    <div 
        data-media-picker
        data-input-name="image_url"
        data-type="images"
        data-label="Select Image"
        data-initial-value="{{ old('image_url', $product->image_url ?? '') }}"
    ></div>
</div>
```

### Data Attributes

| Attribute | Required | Default | Description |
|-----------|----------|---------|-------------|
| `data-media-picker` | ✅ Yes | - | Marks element for auto-mounting |
| `data-input-name` | ✅ Yes | `media_url` | Name of hidden input to update |
| `data-type` | No | `images` | Filter: `images`, `videos`, `documents`, `all` |
| `data-label` | No | `Select Media` | Button label text |
| `data-initial-value` | No | `''` | Pre-selected media URL |

## Real Examples

### 1. Logo Upload (General Settings)

```blade
<x-core::card title="Logo & Branding">
    <div class="space-y-6">
        <!-- Logo -->
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Store Logo
            </label>
            <input type="hidden" name="logo_url" value="{{ old('logo_url', config('store.logo_url')) }}">
            <div 
                data-media-picker
                data-input-name="logo_url"
                data-type="images"
                data-label="Upload Logo"
                data-initial-value="{{ old('logo_url', config('store.logo_url')) }}"
            ></div>
            <p class="mt-2 text-xs text-gray-500">PNG, JPG or SVG. Max 2MB.</p>
        </div>

        <!-- Favicon -->
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Favicon
            </label>
            <input type="hidden" name="favicon_url" value="{{ old('favicon_url', config('store.favicon_url')) }}">
            <div 
                data-media-picker
                data-input-name="favicon_url"
                data-type="images"
                data-label="Upload Favicon"
                data-initial-value="{{ old('favicon_url', config('store.favicon_url')) }}"
            ></div>
            <p class="mt-2 text-xs text-gray-500">ICO or PNG. 32x32 or 64x64 pixels.</p>
        </div>
    </div>
</x-core::card>
```

### 2. Product Image

```blade
<div>
    <label class="block text-xs font-medium text-gray-700 mb-2">
        Product Image
    </label>
    <input type="hidden" name="product_image" value="{{ old('product_image', $product->image_url) }}">
    <div 
        data-media-picker
        data-input-name="product_image"
        data-type="images"
        data-label="Select Product Image"
        data-initial-value="{{ old('product_image', $product->image_url) }}"
    ></div>
</div>
```

### 3. Video Upload

```blade
<div>
    <label class="block text-xs font-medium text-gray-700 mb-2">
        Product Video
    </label>
    <input type="hidden" name="video_url" value="{{ old('video_url', $product->video_url) }}">
    <div 
        data-media-picker
        data-input-name="video_url"
        data-type="videos"
        data-label="Select Video"
        data-initial-value="{{ old('video_url', $product->video_url) }}"
    ></div>
</div>
```

### 4. Document Upload

```blade
<div>
    <label class="block text-xs font-medium text-gray-700 mb-2">
        Product Manual (PDF)
    </label>
    <input type="hidden" name="manual_url" value="{{ old('manual_url', $product->manual_url) }}">
    <div 
        data-media-picker
        data-input-name="manual_url"
        data-type="documents"
        data-label="Select PDF"
        data-initial-value="{{ old('manual_url', $product->manual_url) }}"
    ></div>
</div>
```

### 5. Any File Type

```blade
<div>
    <label class="block text-xs font-medium text-gray-700 mb-2">
        Attachment
    </label>
    <input type="hidden" name="attachment_url" value="{{ old('attachment_url') }}">
    <div 
        data-media-picker
        data-input-name="attachment_url"
        data-type="all"
        data-label="Select File"
        data-initial-value="{{ old('attachment_url') }}"
    ></div>
</div>
```

## How It Works

1. **Auto-Detection**: On page load, JavaScript finds all `[data-media-picker]` elements
2. **Vue Mount**: Creates a Vue app instance for each element
3. **Preview**: Shows thumbnail if `data-initial-value` is set
4. **Selection**: Opens modal when button clicked
5. **Update**: Updates hidden input with selected media URL
6. **Form Submit**: Hidden input value is submitted with form

## Features

✅ **Automatic Preview** - Shows selected image thumbnail  
✅ **Search & Filter** - Built-in media library search  
✅ **Upload** - Upload new files from picker  
✅ **Type Filtering** - Only show relevant file types  
✅ **No Manual Vue Code** - Just add data attributes  

## Controller Validation

Update your controller to accept URL instead of file upload:

```php
public function update(Request $request)
{
    $validated = $request->validate([
        'logo_url' => 'nullable|url',
        'favicon_url' => 'nullable|url',
        'product_image' => 'nullable|url',
        // ... other fields
    ]);

    // Save the URLs to your model
    $product->update([
        'image_url' => $validated['product_image'],
    ]);

    return redirect()->back()->with('success', 'Updated!');
}
```

## Troubleshooting

### Picker doesn't appear
- Make sure `npm run dev` or `npm run build` has been run
- Check browser console for JavaScript errors
- Verify `@vite(['resources/js/app.js'])` is in your layout

### Image doesn't show
- Check the URL is valid and accessible
- Verify `php artisan storage:link` has been run
- Check file permissions on storage directory

### Form doesn't submit URL
- Verify hidden input has correct `name` attribute
- Check `data-input-name` matches hidden input name
- Inspect form data in browser dev tools

## Advanced: Manual Vue Component

If you need more control, you can manually mount the Vue component:

```vue
<script setup>
import { ref } from 'vue';
import MediaPicker from '@/components/MediaPicker.vue';

const imageUrl = ref('');

function handleSelected(media) {
    imageUrl.value = media.url;
}
</script>

<template>
    <div>
        <input type="hidden" name="image_url" :value="imageUrl">
        
        <img v-if="imageUrl" :src="imageUrl" class="w-32 h-32 mb-3">
        
        <MediaPicker 
            type="images" 
            @selected="handleSelected"
        >
            <template #trigger>
                <button type="button">Select Image</button>
            </template>
        </MediaPicker>
    </div>
</template>
```

## Need Help?

- Check `MEDIA_LIBRARY.md` for full media library documentation
- View `MediaPicker.vue` source for component API
- Test in `/admin/media` to verify media library is working
