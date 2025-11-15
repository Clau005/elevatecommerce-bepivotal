<?php

namespace ElevateCommerce\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use ElevateCommerce\Core\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaController extends Controller
{
    /**
     * Display media library
     */
    public function index(Request $request)
    {
        $query = Media::query()->latest();

        // Filter by type
        if ($request->filled('type')) {
            switch ($request->type) {
                case 'images':
                    $query->images();
                    break;
                case 'videos':
                    $query->videos();
                    break;
                case 'documents':
                    $query->documents();
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate(24);

        return view('core::admin.media.index', compact('media'));
    }

    /**
     * Upload new media files
     */
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:10240', // 10MB max
        ]);

        $uploadedMedia = [];

        foreach ($request->file('files') as $file) {
            $media = $this->uploadFile($file);
            $uploadedMedia[] = $media;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'media' => $uploadedMedia,
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', count($uploadedMedia) . ' file(s) uploaded successfully');
    }

    /**
     * Show single media details
     */
    public function show(Media $media)
    {
        return view('core::admin.media.show', compact('media'));
    }

    /**
     * Update media metadata
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $media->update($request->only(['alt_text', 'description']));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'media' => $media->fresh(),
            ]);
        }

        return redirect()->route('admin.media.show', $media)
            ->with('success', 'Media updated successfully');
    }

    /**
     * Delete media
     */
    public function destroy(Media $media)
    {
        $media->forceDelete(); // This will trigger the boot method to delete files

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully',
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'Media deleted successfully');
    }

    /**
     * Bulk delete media
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:media,id',
        ]);

        Media::whereIn('id', $request->ids)->each(function ($media) {
            $media->forceDelete();
        });

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' media file(s) deleted successfully',
        ]);
    }

    /**
     * Get media for API/picker
     */
    public function api(Request $request)
    {
        $query = Media::query()->latest();

        // Filter by type
        if ($request->filled('type')) {
            switch ($request->type) {
                case 'images':
                    $query->images();
                    break;
                case 'videos':
                    $query->videos();
                    break;
                case 'documents':
                    $query->documents();
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate($request->get('per_page', 24));

        return response()->json($media);
    }

    /**
     * Handle file upload
     */
    protected function uploadFile($file)
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;
        
        // Store file
        $path = $file->storeAs('media', $filename, 'public');

        // Get image dimensions if it's an image
        $width = null;
        $height = null;
        if (str_starts_with($mimeType, 'image/')) {
            try {
                // Create ImageManager with GD driver
                $manager = new ImageManager(new Driver());
                
                // Read image from uploaded file
                $image = $manager->read($file->getRealPath());
                $width = $image->width();
                $height = $image->height();
                
                // Create thumbnail (cover fit 300x300)
                $thumbnail = $image->cover(300, 300);
                $thumbnailPath = 'thumbnails/' . $filename;
                
                // Encode and save thumbnail
                $encoded = $thumbnail->toJpeg(80);
                Storage::disk('public')->put($thumbnailPath, (string) $encoded);
            } catch (\Exception $e) {
                // If image processing fails, continue without dimensions
                \Log::warning('Failed to process image: ' . $e->getMessage());
            }
        }

        // Create media record
        $media = Media::create([
            'filename' => $filename,
            'original_filename' => $originalName,
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'uploaded_by' => auth('admin')->id(),
        ]);

        return $media;
    }
}
