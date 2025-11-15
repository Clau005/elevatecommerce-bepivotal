@extends('core::admin.layouts.app')

@section('title', 'Media Details')

@section('content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <x-core::button 
                variant="ghost" 
                size="sm"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('admin.media.index') }}'"
            />
            <x-core::heading level="1" subtitle="View and edit media details">
                Media Details
            </x-core::heading>
        </div>
        <x-core::button 
            variant="danger"
            icon="fas fa-trash"
            onclick="if(confirm('Are you sure you want to delete this file?')) { document.getElementById('delete-form').submit(); }"
        >
            Delete
        </x-core::button>
    </div>

    <form id="delete-form" action="{{ route('admin.media.destroy', $media) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Preview -->
        <x-core::card title="Preview">
            <div class="bg-gray-50 rounded-lg p-8 flex items-center justify-center min-h-[400px]">
                @if($media->is_image)
                    <img src="{{ $media->url }}" alt="{{ $media->alt_text }}" class="max-w-full max-h-[500px] rounded-lg shadow-lg">
                @elseif($media->is_video)
                    <video controls class="max-w-full max-h-[500px] rounded-lg shadow-lg">
                        <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <div class="text-center">
                        <i class="fas fa-file text-8xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">{{ $media->original_filename }}</p>
                        <a href="{{ $media->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-download mr-1"></i> Download
                        </a>
                    </div>
                @endif
            </div>
        </x-core::card>

        <!-- Details & Edit -->
        <div class="space-y-4">
            <!-- File Information -->
            <x-core::card title="File Information">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Filename:</span>
                        <span class="font-medium text-gray-900">{{ $media->original_filename }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Type:</span>
                        <span class="font-medium text-gray-900">{{ strtoupper($media->extension) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Size:</span>
                        <span class="font-medium text-gray-900">{{ $media->formatted_size }}</span>
                    </div>
                    @if($media->width && $media->height)
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Dimensions:</span>
                            <span class="font-medium text-gray-900">{{ $media->width }} × {{ $media->height }}px</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Uploaded:</span>
                        <span class="font-medium text-gray-900">{{ $media->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">MIME Type:</span>
                        <span class="font-medium text-gray-900 text-xs">{{ $media->mime_type }}</span>
                    </div>
                </div>
            </x-core::card>

            <!-- Public URL -->
            <x-core::card title="Public URL">
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <input 
                            type="text" 
                            id="media-url" 
                            value="{{ $media->url }}" 
                            readonly
                            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50"
                        >
                        <x-core::button 
                            variant="secondary"
                            size="sm"
                            icon="fas fa-copy"
                            onclick="copyToClipboard('{{ $media->url }}')"
                        >
                            Copy
                        </x-core::button>
                    </div>
                    <p class="text-xs text-gray-500">Use this URL to reference this file in your content</p>
                </div>
            </x-core::card>

            <!-- Edit Metadata -->
            <x-core::card title="Metadata">
                <form action="{{ route('admin.media.update', $media) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <x-core::input 
                        id="alt_text"
                        name="alt_text"
                        label="Alt Text"
                        value="{{ old('alt_text', $media->alt_text) }}"
                        hint="Describe this image for accessibility"
                        :error="$errors->first('alt_text')"
                    />

                    <x-core::textarea 
                        id="description"
                        name="description"
                        label="Description"
                        :rows="3"
                        hint="Add notes or description for this file"
                        :error="$errors->first('description')"
                    >{{ old('description', $media->description) }}</x-core::textarea>

                    <div class="flex justify-end">
                        <x-core::button 
                            variant="primary"
                            type="submit"
                            icon="fas fa-save"
                        >
                            Save Changes
                        </x-core::button>
                    </div>
                </form>
            </x-core::card>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show success feedback
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
        btn.classList.remove('bg-gray-100', 'hover:bg-gray-200');
        btn.classList.add('bg-green-100', 'text-green-700');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('bg-green-100', 'text-green-700');
            btn.classList.add('bg-gray-100', 'hover:bg-gray-200');
        }, 2000);
    }).catch(err => {
        alert('Failed to copy URL');
    });
}
</script>
@endsection
