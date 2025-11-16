<x-core::admin.layouts.app pageTitle="Create Filter" title="Create Filter - Admin">

<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Filter</h1>
            <p class="text-gray-600 mt-1">Define a new filter for your collections.</p>
        </div>
        <a href="{{ route('admin.filters.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Filters
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.filters.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            {{-- Info Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            <strong>Global Filter:</strong> This filter will be available to assign to any collection. After creating, you can enable it on specific collections from their edit pages.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Filter Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Filter Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="e.g., Color, Size, Brand"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Filter Slug --}}
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                    Slug (URL-friendly)
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                    placeholder="Auto-generated from name"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from name</p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Filter Type --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    Filter Type <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Select (Single Choice)</option>
                    <option value="checkbox" {{ old('type') == 'checkbox' ? 'selected' : '' }}>Checkbox (Multiple Choice)</option>
                    <option value="range" {{ old('type') == 'range' ? 'selected' : '' }}>Range (Min/Max)</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Source Model --}}
            <div>
                <label for="source_model" class="block text-sm font-medium text-gray-700 mb-1">
                    Source Model <span class="text-red-500">*</span>
                </label>
                <select name="source_model" id="source_model" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select model</option>
                    <option value="Elevate\Product\Models\Product" {{ old('source_model') == 'Elevate\Product\Models\Product' ? 'selected' : '' }}>
                        Product
                    </option>
                    <option value="Elevate\Product\Models\ProductVariant" {{ old('source_model') == 'Elevate\Product\Models\ProductVariant' ? 'selected' : '' }}>
                        Product Variant
                    </option>
                </select>
                <p class="mt-1 text-sm text-gray-500">The model to filter (usually Product)</p>
                @error('source_model')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Source Column --}}
            <div>
                <label for="source_column" class="block text-sm font-medium text-gray-700 mb-1">
                    Source Column <span class="text-red-500">*</span>
                </label>
                <input type="text" name="source_column" id="source_column" value="{{ old('source_column') }}" required
                    placeholder="e.g., color, size, brand_id"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-sm text-gray-500">The database column to filter by</p>
                @error('source_column')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Source Relation (Optional) --}}
            <div>
                <label for="source_relation" class="block text-sm font-medium text-gray-700 mb-1">
                    Source Relation (Optional)
                </label>
                <input type="text" name="source_relation" id="source_relation" value="{{ old('source_relation') }}"
                    placeholder="e.g., brand (if filtering by brand.name)"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-sm text-gray-500">If filtering by a related model, enter the relationship name</p>
                @error('source_relation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                    Sort Order
                </label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Active Status --}}
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Active (show on storefront)
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.filters.index') }}" 
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                Create Filter
            </button>
        </div>
    </form>
</div>

@endsection
