# Media Library

A Shopify-inspired media library system for managing files in your ElevateCommerce admin.

## Features

- 📁 **Full Media Management** - Upload, browse, search, filter, and delete files
- 🖼️ **Image Support** - Automatic thumbnail generation for images
- 🎬 **Multiple File Types** - Images, videos, and documents
- 🔍 **Search & Filter** - Find files quickly by name, type, or metadata
- 📊 **Grid & List Views** - Switch between visual grid and detailed list views
- ✅ **Bulk Actions** - Select and delete multiple files at once
- 🎨 **Vue.js Picker Component** - Reusable media picker for any form

## Admin Page

Access the full media library at `/admin/media` or via the sidebar navigation.

### Features:
- Upload multiple files via drag & drop or file picker
- Search files by filename or alt text
- Filter by type (All, Images, Videos, Documents)
- Switch between grid and list views
- Bulk select and delete files
- View file details (size, dimensions, upload date)

## Vue.js Media Picker Component

Use the `MediaPicker` component anywhere in your admin to let users select media files.

### Basic Usage

```vue
<template>
    <div>
        <media-picker @selected="handleMediaSelected">
            <template #trigger>
                <button class="btn btn-primary">
                    Select Image
                </button>
            </template>
        </media-picker>

        <div v-if="selectedMedia">
            <img :src="selectedMedia.url" :alt="selectedMedia.alt_text">
            <p>{{ selectedMedia.original_filename }}</p>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedMedia: null
        }
    },
    methods: {
        handleMediaSelected(media) {
            this.selectedMedia = media;
            console.log('Selected media URL:', media.url);
        }
    }
}
</script>
```

### Multiple Selection

```vue
<media-picker 
    :multiple="true" 
    @selected="handleMultipleMedia"
>
    <template #trigger>
        <button>Select Multiple Images</button>
    </template>
</media-picker>
```

### Filter by Type

```vue
<!-- Only show images -->
<media-picker 
    type="images" 
    @selected="handleImageSelected"
/>

<!-- Only show videos -->
<media-picker 
    type="videos" 
    @selected="handleVideoSelected"
/>

<!-- Only show documents -->
<media-picker 
    type="documents" 
    @selected="handleDocumentSelected"
/>
```

### Custom Accept Types

```vue
<media-picker 
    accept="image/png,image/jpeg" 
    @selected="handleImageSelected"
/>
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `multiple` | Boolean | `false` | Allow selecting multiple files |
| `type` | String | `'all'` | Filter by type: `'all'`, `'images'`, `'videos'`, `'documents'` |
| `accept` | String | `'image/*,video/*,.pdf,.doc,.docx'` | File types to accept for upload |

## Component Events

| Event | Payload | Description |
|-------|---------|-------------|
| `selected` | `Media` or `Media[]` | Emitted when user confirms selection. Single object if `multiple=false`, array if `multiple=true` |

## Media Object Structure

```javascript
{
    id: 1,
    filename: "uuid-filename.jpg",
    original_filename: "my-image.jpg",
    path: "media/uuid-filename.jpg",
    url: "https://example.com/storage/media/uuid-filename.jpg",
    thumbnail_url: "https://example.com/storage/thumbnails/uuid-filename.jpg",
    mime_type: "image/jpeg",
    extension: "jpg",
    size: 1024000, // bytes
    formatted_size: "1.00 MB",
    width: 1920,
    height: 1080,
    alt_text: "Product image",
    description: "Main product photo",
    is_image: true,
    is_video: false,
    is_document: false,
    created_at: "2024-11-15T10:00:00.000000Z",
    updated_at: "2024-11-15T10:00:00.000000Z"
}
```

## Using in Blade Forms

### Example: Product Image Upload

```blade
<div x-data="{ mediaUrl: '{{ old('image_url', $product->image_url ?? '') }}' }">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Product Image
    </label>
    
    <!-- Hidden input to store the URL -->
    <input type="hidden" name="image_url" x-model="mediaUrl">
    
    <!-- Preview -->
    <div x-show="mediaUrl" class="mb-3">
        <img :src="mediaUrl" class="w-32 h-32 object-cover rounded-lg border">
    </div>
    
    <!-- Media Picker Button -->
    <div id="media-picker-mount"></div>
    
    <script>
        // Mount Vue component
        import { createApp } from 'vue';
        import MediaPicker from '@/components/MediaPicker.vue';
        
        createApp({
            components: { MediaPicker },
            template: `
                <media-picker 
                    type="images" 
                    @selected="handleSelected"
                >
                    <template #trigger>
                        <button type="button" class="btn btn-secondary">
                            <i class="fas fa-images mr-2"></i>
                            Select Image
                        </button>
                    </template>
                </media-picker>
            `,
            methods: {
                handleSelected(media) {
                    // Update Alpine.js data
                    document.querySelector('[x-data]').__x.$data.mediaUrl = media.url;
                }
            }
        }).mount('#media-picker-mount');
    </script>
</div>
```

## API Endpoints

### Get Media (Paginated)
```
GET /admin/media/api?page=1&per_page=24&type=images&search=product
```

### Upload Files
```
POST /admin/media
Content-Type: multipart/form-data

files[]: File
files[]: File
```

### Update Media Metadata
```
PUT /admin/media/{id}
Content-Type: application/json

{
    "alt_text": "Product image",
    "description": "Main product photo"
}
```

### Delete Media
```
DELETE /admin/media/{id}
```

### Bulk Delete
```
POST /admin/media/bulk-destroy
Content-Type: application/json

{
    "ids": [1, 2, 3]
}
```

## Database Schema

The `media` table stores all file metadata:

```sql
- id (bigint, primary key)
- filename (string) - UUID-based filename
- original_filename (string) - Original uploaded filename
- path (string) - Storage path
- disk (string) - Storage disk (default: 'public')
- mime_type (string) - File MIME type
- extension (string) - File extension
- size (bigint) - File size in bytes
- width (int, nullable) - Image width
- height (int, nullable) - Image height
- alt_text (string, nullable) - Alt text for images
- description (text, nullable) - File description
- metadata (json, nullable) - Additional metadata
- uploaded_by (bigint, nullable) - Admin user ID
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable) - Soft deletes
```

## Storage Configuration

Files are stored in `storage/app/public/media/` by default.

Thumbnails are generated at `storage/app/public/thumbnails/`.

Make sure your storage is linked:
```bash
php artisan storage:link
```

## Image Processing

The system uses Intervention Image for thumbnail generation. Install it:

```bash
composer require intervention/image
```

Thumbnails are automatically created at 300x300px for all uploaded images.

## File Size Limits

Default upload limit: **10MB per file**

Adjust in `MediaController::store()` validation rules:

```php
'files.*' => 'required|file|max:10240', // 10MB
```

## Supported File Types

### Images
- JPG, JPEG, PNG, GIF, SVG, WebP, BMP, TIFF, ICO

### Videos
- MP4, MOV, AVI, WMV, FLV, WebM

### Documents
- PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX

## Security

- All uploads are validated for file type and size
- Files are stored with UUID filenames to prevent conflicts
- Soft deletes allow recovery of accidentally deleted files
- Admin authentication required for all operations

## Future Enhancements

- [ ] Image editing (crop, resize, filters)
- [ ] Folder/collection organization
- [ ] CDN integration
- [ ] Advanced search with tags
- [ ] Usage tracking (where media is used)
- [ ] Bulk upload via ZIP
- [ ] AI-powered alt text generation
