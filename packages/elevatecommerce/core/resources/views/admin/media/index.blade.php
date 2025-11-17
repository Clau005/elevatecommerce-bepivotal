@extends('core::admin.layouts.app')

@section('title', 'Media Library')

@section('content')
<div class="space-y-4" x-data="mediaLibrary()">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <x-core::heading level="1" subtitle="Manage your media files">
            Media Library
        </x-core::heading>
        <x-core::button 
            variant="primary"
            icon="fas fa-upload"
            @click="$refs.fileInput.click()"
        >
            Upload Files
        </x-core::button>
        <input 
            type="file" 
            x-ref="fileInput" 
            @change="uploadFiles($event)" 
            multiple 
            class="hidden"
            accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx"
        >
    </div>

    <!-- Filters & Search -->
    <x-core::card :padding="false">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <form action="{{ route('admin.media.index') }}" method="GET">
                        <x-core::input 
                            id="search"
                            name="search"
                            placeholder="Search files..."
                            value="{{ request('search') }}"
                            icon="fas fa-search"
                        />
                    </form>
                </div>

                <!-- Type Filter -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.media.index') }}" 
                       class="px-3 py-1.5 text-xs rounded-md {{ !request('type') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All
                    </a>
                    <a href="{{ route('admin.media.index', ['type' => 'images']) }}" 
                       class="px-3 py-1.5 text-xs rounded-md {{ request('type') === 'images' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Images
                    </a>
                    <a href="{{ route('admin.media.index', ['type' => 'videos']) }}" 
                       class="px-3 py-1.5 text-xs rounded-md {{ request('type') === 'videos' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Videos
                    </a>
                    <a href="{{ route('admin.media.index', ['type' => 'documents']) }}" 
                       class="px-3 py-1.5 text-xs rounded-md {{ request('type') === 'documents' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Documents
                    </a>
                </div>

                <!-- View Mode -->
                <div class="flex items-center space-x-1">
                    <button @click="viewMode = 'grid'" 
                            class="p-2 rounded-md" 
                            :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'">
                        <i class="fas fa-th text-xs"></i>
                    </button>
                    <button @click="viewMode = 'list'" 
                            class="p-2 rounded-md"
                            :class="viewMode === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'">
                        <i class="fas fa-list text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div x-show="selectedFiles.length > 0" class="p-3 bg-blue-50 border-b border-blue-200">
            <div class="flex items-center justify-between">
                <span class="text-sm text-blue-900">
                    <span x-text="selectedFiles.length"></span> file(s) selected
                </span>
                <x-core::button 
                    variant="danger"
                    size="sm"
                    icon="fas fa-trash"
                    @click="bulkDelete()"
                >
                    Delete Selected
                </x-core::button>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="viewMode === 'grid'" class="p-4">
            @if($media->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($media as $item)
                        <div class="relative group cursor-pointer border-2 rounded-lg overflow-hidden hover:border-blue-500 transition-colors"
                             :class="selectedFiles.includes({{ $item->id }}) ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200'"
                             @click="toggleSelection({{ $item->id }})">
                            <!-- Checkbox -->
                            <div class="absolute top-2 left-2 z-10">
                                <input 
                                    type="checkbox" 
                                    :checked="selectedFiles.includes({{ $item->id }})"
                                    class="w-4 h-4 rounded border-gray-300"
                                    @click.stop="toggleSelection({{ $item->id }})"
                                >
                            </div>

                            <!-- Preview -->
                            <div class="aspect-square bg-gray-100 flex items-center justify-center">
                                @if($item->is_image)
                                    <img src="{{ $item->thumbnail_url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover">
                                @elseif($item->is_video)
                                    <i class="fas fa-video text-4xl text-gray-400"></i>
                                @else
                                    <i class="fas fa-file text-4xl text-gray-400"></i>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="p-2 bg-white">
                                <p class="text-xs font-medium text-gray-900 truncate" title="{{ $item->original_filename }}">
                                    {{ $item->original_filename }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $item->formatted_size }}</p>
                            </div>

                            <!-- Actions on Hover -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <div class="flex items-center space-x-2">
                                    <button 
                                        onclick="copyUrl('{{ $item->url }}', this); event.stopPropagation();" 
                                        class="p-2 bg-white rounded-full hover:bg-gray-100"
                                        title="Copy URL"
                                    >
                                        <i class="fas fa-link text-gray-700"></i>
                                    </button>
                                    <a href="{{ route('admin.media.show', $item) }}" 
                                       class="p-2 bg-white rounded-full hover:bg-gray-100"
                                       title="View details"
                                       @click.stop>
                                        <i class="fas fa-eye text-gray-700"></i>
                                    </a>
                                    <button @click.stop="deleteFile({{ $item->id }})" 
                                            class="p-2 bg-white rounded-full hover:bg-red-100"
                                            title="Delete">
                                        <i class="fas fa-trash text-red-600"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 mb-4">No media files found</p>
                    <x-core::button 
                        variant="primary"
                        icon="fas fa-upload"
                        @click="$refs.fileInput.click()"
                    >
                        Upload Your First File
                    </x-core::button>
                </div>
            @endif
        </div>

        <!-- List View -->
        <div x-show="viewMode === 'list'" class="overflow-x-auto">
            @if($media->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">
                                <input type="checkbox" @change="toggleAll($event)" class="w-4 h-4 rounded border-gray-300">
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Preview</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Filename</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Size</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Uploaded</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($media as $item)
                            <tr class="hover:bg-gray-50" :class="selectedFiles.includes({{ $item->id }}) ? 'bg-blue-50' : ''">
                                <td class="px-4 py-2">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedFiles.includes({{ $item->id }})"
                                        @change="toggleSelection({{ $item->id }})"
                                        class="w-4 h-4 rounded border-gray-300"
                                    >
                                </td>
                                <td class="px-4 py-2">
                                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                                        @if($item->is_image)
                                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-file text-gray-400"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <p class="text-sm font-medium text-gray-900">{{ $item->original_filename }}</p>
                                    @if($item->alt_text)
                                        <p class="text-xs text-gray-500">{{ $item->alt_text }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-xs text-gray-600">{{ strtoupper($item->extension) }}</span>
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-600">{{ $item->formatted_size }}</td>
                                <td class="px-4 py-2 text-xs text-gray-600">{{ $item->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button 
                                            onclick="copyUrl('{{ $item->url }}', this)" 
                                            class="text-gray-600 hover:text-gray-800"
                                            title="Copy URL"
                                        >
                                            <i class="fas fa-link text-xs"></i>
                                        </button>
                                        <a href="{{ route('admin.media.show', $item) }}" class="text-blue-600 hover:text-blue-800" title="View details">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <button @click="deleteFile({{ $item->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No media files found</p>
                </div>
            @endif
        </div>
    </x-core::card>

    <!-- Pagination -->
    @if($media->hasPages())
        <div class="flex justify-center">
            {{ $media->links() }}
        </div>
    @endif
</div>

<script>
function copyUrl(url, button) {
    navigator.clipboard.writeText(url).then(() => {
        const icon = button.querySelector('i');
        icon.classList.remove('fa-link');
        icon.classList.add('fa-check');
        button.classList.remove('text-gray-600', 'hover:text-gray-800');
        button.classList.add('text-green-600');
        
        setTimeout(() => {
            icon.classList.remove('fa-check');
            icon.classList.add('fa-link');
            button.classList.remove('text-green-600');
            button.classList.add('text-gray-600', 'hover:text-gray-800');
        }, 2000);
    }).catch(err => {
        alert('Failed to copy URL');
    });
}

function mediaLibrary() {
    return {
        viewMode: 'grid',
        selectedFiles: [],
        
        toggleSelection(id) {
            const index = this.selectedFiles.indexOf(id);
            if (index > -1) {
                this.selectedFiles.splice(index, 1);
            } else {
                this.selectedFiles.push(id);
            }
        },
        
        toggleAll(event) {
            if (event.target.checked) {
                this.selectedFiles = @json($media->pluck('id'));
            } else {
                this.selectedFiles = [];
            }
        },
        
        async uploadFiles(event) {
            const files = event.target.files;
            if (!files.length) return;
            
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            
            try {
                const response = await fetch('{{ route('admin.media.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Upload failed:', error);
                alert('Upload failed. Please try again.');
            }
        },
        
        async deleteFile(id) {
            if (!confirm('Are you sure you want to delete this file?')) return;
            
            try {
                const response = await fetch(`/admin/media/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Delete failed:', error);
            }
        },
        
        async bulkDelete() {
            if (!confirm(`Are you sure you want to delete ${this.selectedFiles.length} file(s)?`)) return;
            
            try {
                const response = await fetch('{{ route('admin.media.bulk-destroy') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ids: this.selectedFiles })
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Bulk delete failed:', error);
            }
        }
    }
}
</script>
@endsection
