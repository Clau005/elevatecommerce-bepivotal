@extends('core::admin.layouts.app')

@section('title', 'Collections')

@section('content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <x-core::heading level="1" subtitle="Organize your products, pages, and content">
            Collections
        </x-core::heading>
        <a href="{{ route('admin.collections.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Create Collection
        </a>
    </div>

    <!-- Filters & Search -->
    <x-core::card :padding="false">
        <div class="p-4 border-b border-gray-200">
            <form action="{{ route('admin.collections.index') }}" method="GET" class="space-y-4">
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <x-core::input 
                            id="search"
                            name="search"
                            placeholder="Search by name, slug, or description..."
                            value="{{ request('search') }}"
                            icon="fas fa-search"
                        />
                    </div>

                    <!-- Parent Filter -->
                    <div class="w-48">
                        <select name="parent_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Collections</option>
                            <option value="root" {{ request('parent_id') === 'root' ? 'selected' : '' }}>Root Only</option>
                            @foreach($parentCollections ?? [] as $parent)
                                <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="w-48">
                        <select name="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <x-core::button type="submit" variant="secondary">
                        Filter
                    </x-core::button>
                </div>
            </form>
        </div>

        <!-- Collections Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($collections as $collection)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($collection->image)
                                    <img src="{{ $collection->image }}" alt="{{ $collection->name }}" class="h-10 w-10 rounded object-cover mr-3">
                                @else
                                    <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                        <i class="fas fa-layer-group text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $collection->name }}</div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm text-gray-500">{{ $collection->getFullSlug() }}</div>
                                        <button 
                                            onclick="copyToClipboard(event, '{{ url($collection->getFullSlug()) }}')" 
                                            class="text-gray-400 hover:text-gray-600 transition-colors"
                                            title="Copy link"
                                        >
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($collection->parent)
                                <div class="text-sm text-gray-900">{{ $collection->parent->name }}</div>
                            @else
                                <span class="text-sm text-gray-400">Root</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $collection->collectables_count ?? 0 }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($collection->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $collection->updated_at->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $collection->updated_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.collections.edit', $collection) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                Edit
                            </a>
                            <form action="{{ route('admin.collections.destroy', $collection) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this collection?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-layer-group text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No collections found</p>
                                <p class="text-sm">Get started by creating your first collection.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($collections->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $collections->links() }}
        </div>
        @endif
    </x-core::card>
</div>

@push('scripts')
<script>
function copyToClipboard(event, text) {
    // Try modern clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showCopySuccess(event);
        }).catch(err => {
            console.error('Clipboard API failed:', err);
            fallbackCopy(event, text);
        });
    } else {
        // Fallback for older browsers or non-HTTPS
        fallbackCopy(event, text);
    }
}

function fallbackCopy(event, text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        showCopySuccess(event);
    } catch (err) {
        console.error('Fallback copy failed:', err);
        alert('Failed to copy link. Please copy manually: ' + text);
    } finally {
        document.body.removeChild(textarea);
    }
}

function showCopySuccess(event) {
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    const originalClass = icon.className;
    
    icon.className = 'fas fa-check text-xs text-green-600';
    
    setTimeout(() => {
        icon.className = originalClass;
    }, 2000);
}
</script>
@endpush
@endsection
