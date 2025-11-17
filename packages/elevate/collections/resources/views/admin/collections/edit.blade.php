<x-app pageTitle="Edit Collection" title="Edit Collection - Admin" description="Edit collection">

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Collection</h1>
            <p class="text-gray-600 mt-1">Update collection settings and manage items.</p>
        </div>
        <a href="{{ route('admin.collections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-md transition-all duration-200 text-sm hover:bg-gray-300">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Collections
        </a>
    </div>

    <form action="{{ route('admin.collections.update', $collection) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h3>
                
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Collection Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $collection->name) }}" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $collection->slug) }}" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('slug') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">URL-friendly version (e.g., electronics, mens-clothing)</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Collection Image --}}
                    <x-image-picker 
                        name="image" 
                        :value="old('image', $collection->image)" 
                        label="Collection Image" 
                    />
                    <p class="mt-1 text-sm text-gray-500">Used for collection banner and listings</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Description (Rich Text) --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <x-trix-input 
                            id="description" 
                            name="description" 
                            :value="old('description', $collection->description)" 
                        />
                        <p class="mt-1 text-sm text-gray-500">Rich text description displayed on the collection page</p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Template --}}
                    <div>
                        <label for="template_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Template <span class="text-red-500">*</span>
                        </label>
                        <select name="template_id" id="template_id" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('template_id') border-red-300 @enderror">
                            <option value="">Select a template...</option>
                            @foreach(\Elevate\Editor\Models\Template::where('model_type', 'Elevate\Collections\Models\Collection')->get() as $template)
                                <option value="{{ $template->id }}" {{ old('template_id', $collection->template_id) == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose how this collection will be displayed on the storefront</p>
                        @error('template_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Parent Collection --}}
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Parent Collection (Optional)</label>
                        <select name="parent_id" id="parent_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('parent_id') border-red-300 @enderror">
                            <option value="">None (Root Collection)</option>
                            @foreach($parentCollections as $parent)
                                @if($parent->id !== $collection->id)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $collection->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Create a subcollection by selecting a parent</p>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sort Order --}}
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $collection->sort_order) }}" min="0"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('sort_order') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $collection->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Active (visible on storefront)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">SEO</h3>
                
                <div class="space-y-4">
                    {{-- Meta Title --}}
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $collection->meta_title) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('meta_title') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to use collection name</p>
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Meta Description --}}
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('meta_description') border-red-300 @enderror">{{ old('meta_description', $collection->meta_description) }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Collection Filters</h3>
                        <p class="text-sm text-gray-600 mt-1">Enable filters for this collection to help customers find products</p>
                    </div>
                    <a href="{{ route('admin.filters.create') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        + Create New Filter
                    </a>
                </div>

                @php
                    $availableFilters = \Elevate\Collections\Models\CollectionFilter::with('values')
                        ->orderBy('sort_order')
                        ->get();
                    $enabledFilterIds = old('enabled_filters', $collection->filters->pluck('id')->toArray());
                @endphp

                @if($availableFilters->count() > 0)
                    <div class="space-y-3">
                        @foreach($availableFilters as $filter)
                            <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                        name="enabled_filters[]" 
                                        value="{{ $filter->id }}" 
                                        id="filter_{{ $filter->id }}"
                                        {{ in_array($filter->id, $enabledFilterIds) || $filter->is_active ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </div>
                                <div class="ml-3 flex-1">
                                    <label for="filter_{{ $filter->id }}" class="font-medium text-gray-900 cursor-pointer">
                                        {{ $filter->name }}
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $filter->values->count() }} values • {{ ucfirst($filter->type) }} • {{ class_basename($filter->source_model) }}.{{ $filter->source_column }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.filters.edit', $filter) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <p class="mt-2">No filters created for this collection yet.</p>
                        <a href="{{ route('admin.filters.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            Create First Filter
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-black text-white font-medium rounded-md hover:bg-gray-800 transition-all text-sm">
                Update Collection
            </button>
        </div>
    </form>

    {{-- Collection Items --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Collection Items</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $collection->type === 'smart' ? 'Items automatically added based on rules' : 'Manually manage items in this collection' }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" id="bulkRemoveBtn" onclick="bulkRemoveItems()" class="hidden inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Remove Selected
                    </button>
                    @if($collection->type === 'manual')
                        <button type="button" onclick="openAddItemModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Items
                        </button>
                    @endif
                </div>
            </div>

            @if($collection->collectables->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="w-12 px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Item
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="collectables-list">
                            @foreach($collection->collectables->sortBy('sort_order') as $collectable)
                                <tr data-id="{{ $collectable->id }}" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" class="item-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $collectable->id }}" onchange="updateBulkRemoveButton()">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 cursor-move mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                            </svg>
                                            <span class="text-sm text-gray-900">{{ $collectable->sort_order }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $collectable->collectable->name ?? $collectable->collectable->title ?? 'Item #'.$collectable->collectable_id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ class_basename($collectable->collectable_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #{{ $collectable->collectable_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('admin.collections.items.remove', [$collection, $collectable]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Remove this item from the collection?')">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No items yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding items to this collection.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Add Items Modal --}}
<div id="addItemModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Add Items to Collection</h3>
            <button type="button" onclick="closeAddItemModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Step 1: Select Type --}}
        <div id="step1" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Select Item Type</label>
                <div class="grid grid-cols-3 gap-4">
                    @foreach($collectableTypes as $typeClass => $typeConfig)
                        @php
                            $typeKey = strtolower(class_basename($typeClass));
                        @endphp
                        <button type="button" onclick="selectType('{{ $typeKey }}')" class="type-selector p-6 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-center">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $typeConfig['icon'] }}"/>
                            </svg>
                            <span class="font-medium text-gray-900">{{ $typeConfig['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Step 2: Select Mode (Manual or Smart) --}}
        <div id="step2" class="hidden space-y-4">
            <button type="button" onclick="backToStep1()" class="mb-4 text-sm text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to type selection
            </button>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">How would you like to add items?</label>
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" onclick="selectMode('manual')" class="mode-selector p-6 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <div class="text-left">
                                <div class="font-medium text-gray-900 mb-1">Manual Selection</div>
                                <div class="text-sm text-gray-500">Browse and select individual items</div>
                            </div>
                        </div>
                    </button>
                    <button type="button" onclick="selectMode('smart')" class="mode-selector p-6 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <div class="text-left">
                                <div class="font-medium text-gray-900 mb-1">Smart Selection</div>
                                <div class="text-sm text-gray-500">Filter by tags and add all matching items</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        {{-- Step 3: Manual - Select Items --}}
        <div id="step3Manual" class="hidden">
            <button type="button" onclick="backToStep2()" class="mb-4 text-sm text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to mode selection
            </button>

            <div class="mb-4">
                <input type="text" id="itemSearch" placeholder="Search items..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>

            <div id="itemsTableContainer" class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <form id="addItemsForm" action="{{ route('admin.collections.items.add', $collection) }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="collectable_type" id="selectedType">
                <input type="hidden" name="collectable_ids" id="selectedIds">
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddItemModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                        Add Selected Items
                    </button>
                </div>
            </form>
        </div>

        {{-- Step 3: Smart - Filter by Tags --}}
        <div id="step3Smart" class="hidden">
            <button type="button" onclick="backToStep2()" class="mb-4 text-sm text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to mode selection
            </button>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Tags</label>
                    <p class="text-sm text-gray-500 mb-3">Select tags to filter items. All items with the selected tags will be added to the collection.</p>
                    
                    <div id="tagFilterContainer" class="space-y-3">
                        {{-- Tag filter will be loaded here --}}
                        <div class="flex items-center gap-2">
                            <select id="tagSelect" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select a tag...</option>
                            </select>
                            <button type="button" onclick="addTagFilter()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                Add Tag
                            </button>
                        </div>

                        <div id="selectedTagsContainer" class="flex flex-wrap gap-2 min-h-[40px] p-3 border border-gray-200 rounded-md bg-gray-50">
                            <span class="text-sm text-gray-500">No tags selected</span>
                        </div>
                    </div>
                </div>

                <div id="smartPreviewContainer" class="hidden">
                    <div class="border border-gray-200 rounded-lg p-4 bg-blue-50">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-blue-900">Preview</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    <span id="matchingItemsCount">0</span> items match the selected tags
                                </p>
                            </div>
                        </div>
                    </div>

                    <div id="smartItemsPreview" class="mt-4 max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                        {{-- Preview items will be loaded here --}}
                    </div>
                </div>
            </div>

            <form id="addSmartItemsForm" action="{{ route('admin.collections.items.add', $collection) }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="collectable_type" id="smartSelectedType">
                <input type="hidden" name="tag_ids" id="selectedTagIds">
                <input type="hidden" name="mode" value="smart">
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddItemModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit" id="addSmartItemsBtn" disabled class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        Add All Matching Items
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-generate slug from name (only if not manually edited)
    document.getElementById('name').addEventListener('input', function(e) {
        const slugInput = document.getElementById('slug');
        if (!slugInput.dataset.manuallyEdited) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });
    
    // Mark slug as manually edited if user changes it
    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.manuallyEdited = 'true';
    });

    // Modal functions
    let selectedItems = [];
    let selectedTags = [];
    let currentType = '';
    let currentMode = '';

    function openAddItemModal() {
        document.getElementById('addItemModal').classList.remove('hidden');
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3Manual').classList.add('hidden');
        document.getElementById('step3Smart').classList.add('hidden');
        selectedItems = [];
        selectedTags = [];
    }

    function closeAddItemModal() {
        document.getElementById('addItemModal').classList.add('hidden');
    }

    function backToStep1() {
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3Manual').classList.add('hidden');
        document.getElementById('step3Smart').classList.add('hidden');
        selectedItems = [];
        selectedTags = [];
    }

    function backToStep2() {
        document.getElementById('step2').classList.remove('hidden');
        document.getElementById('step3Manual').classList.add('hidden');
        document.getElementById('step3Smart').classList.add('hidden');
        selectedItems = [];
        selectedTags = [];
    }

    async function selectType(type) {
        currentType = type;
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
        
        // Set the type mapping from PHP
        const typeMap = {
            @foreach($collectableTypes as $typeClass => $typeConfig)
                '{{ strtolower(class_basename($typeClass)) }}': '{{ addslashes($typeClass) }}',
            @endforeach
        };
        
        document.getElementById('selectedType').value = typeMap[type] || type;
        document.getElementById('smartSelectedType').value = typeMap[type] || type;
    }

    async function selectMode(mode) {
        currentMode = mode;
        document.getElementById('step2').classList.add('hidden');
        
        if (mode === 'manual') {
            document.getElementById('step3Manual').classList.remove('hidden');
            await loadItems(currentType);
        } else if (mode === 'smart') {
            document.getElementById('step3Smart').classList.remove('hidden');
            await loadTags();
        }
    }

    async function loadItems(type) {
        const tbody = document.getElementById('itemsTableBody');
        tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Loading...</td></tr>';
        
        try {
            const response = await fetch(`/admin/collections/items/available?type=${type}`);
            const items = await response.json();
            
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">No items found</td></tr>';
                return;
            }
            
            tbody.innerHTML = items.map(item => `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                            value="${item.id}" 
                            data-name="${item.name}">
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">${item.name}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">#${item.id}</td>
                </tr>
            `).join('');
            
            // Add event listeners
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedItems);
            });
            
            // Select all functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                document.querySelectorAll('.item-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateSelectedItems();
            });
            
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-red-500">Error loading items</td></tr>';
        }
    }

    function updateSelectedItems() {
        selectedItems = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
        document.getElementById('selectedIds').value = selectedItems.join(',');
    }

    // Search functionality
    document.getElementById('itemSearch')?.addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('#itemsTableBody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    });

    // Close modal on outside click
    document.getElementById('addItemModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddItemModal();
        }
    });

    // Smart mode functions
    async function loadTags() {
        try {
            const response = await fetch('/admin/tags/all');
            const tags = await response.json();
            
            const tagSelect = document.getElementById('tagSelect');
            tagSelect.innerHTML = '<option value="">Select a tag...</option>';
            
            tags.forEach(tag => {
                const option = document.createElement('option');
                option.value = tag.id;
                option.textContent = tag.value;
                option.dataset.tagValue = tag.value;
                tagSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading tags:', error);
        }
    }

    function addTagFilter() {
        const tagSelect = document.getElementById('tagSelect');
        const selectedOption = tagSelect.options[tagSelect.selectedIndex];
        
        if (!selectedOption.value) return;
        
        const tagId = selectedOption.value;
        const tagValue = selectedOption.dataset.tagValue;
        
        // Check if tag already selected
        if (selectedTags.find(t => t.id === tagId)) {
            return;
        }
        
        selectedTags.push({ id: tagId, value: tagValue });
        updateSelectedTagsDisplay();
        updateSmartPreview();
        
        // Reset select
        tagSelect.selectedIndex = 0;
    }

    function removeTagFilter(tagId) {
        selectedTags = selectedTags.filter(t => t.id !== tagId);
        updateSelectedTagsDisplay();
        updateSmartPreview();
    }

    function updateSelectedTagsDisplay() {
        const container = document.getElementById('selectedTagsContainer');
        
        if (selectedTags.length === 0) {
            container.innerHTML = '<span class="text-sm text-gray-500">No tags selected</span>';
            return;
        }
        
        container.innerHTML = selectedTags.map(tag => `
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                ${tag.value}
                <button type="button" onclick="removeTagFilter('${tag.id}')" class="ml-2 inline-flex items-center p-0.5 rounded-full hover:bg-blue-200 focus:outline-none">
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </span>
        `).join('');
    }

    async function updateSmartPreview() {
        if (selectedTags.length === 0) {
            document.getElementById('smartPreviewContainer').classList.add('hidden');
            document.getElementById('addSmartItemsBtn').disabled = true;
            return;
        }
        
        document.getElementById('smartPreviewContainer').classList.remove('hidden');
        document.getElementById('selectedTagIds').value = selectedTags.map(t => t.id).join(',');
        
        try {
            const tagIds = selectedTags.map(t => t.id).join(',');
            const response = await fetch(`/admin/collections/items/by-tags?type=${currentType}&tag_ids=${tagIds}`);
            const items = await response.json();
            
            document.getElementById('matchingItemsCount').textContent = items.length;
            
            const previewContainer = document.getElementById('smartItemsPreview');
            if (items.length === 0) {
                previewContainer.innerHTML = '<div class="p-4 text-center text-gray-500">No items match the selected tags</div>';
                document.getElementById('addSmartItemsBtn').disabled = true;
            } else {
                previewContainer.innerHTML = items.map(item => `
                    <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-b-0">
                        <div>
                            <div class="text-sm font-medium text-gray-900">${item.name || item.title}</div>
                            <div class="text-xs text-gray-500">ID: ${item.id}</div>
                        </div>
                    </div>
                `).join('');
                document.getElementById('addSmartItemsBtn').disabled = false;
            }
        } catch (error) {
            console.error('Error loading preview:', error);
            document.getElementById('addSmartItemsBtn').disabled = true;
        }
    }

    // Bulk operations for collection items
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateBulkRemoveButton();
    }

    function updateBulkRemoveButton() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const bulkBtn = document.getElementById('bulkRemoveBtn');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        if (checkedBoxes.length > 0) {
            bulkBtn.classList.remove('hidden');
            bulkBtn.classList.add('inline-flex');
            bulkBtn.textContent = `Remove Selected (${checkedBoxes.length})`;
            bulkBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Remove Selected (${checkedBoxes.length})
            `;
        } else {
            bulkBtn.classList.add('hidden');
            bulkBtn.classList.remove('inline-flex');
        }

        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.item-checkbox');
        if (allCheckboxes.length > 0) {
            selectAllCheckbox.checked = checkedBoxes.length === allCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < allCheckboxes.length;
        }
    }

    async function bulkRemoveItems() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (ids.length === 0) return;
        
        if (!confirm(`Are you sure you want to remove ${ids.length} item(s) from this collection?`)) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            formData.append('collectable_ids', ids.join(','));

            const response = await fetch('{{ route("admin.collections.items.bulk-remove", $collection) }}', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                // Remove rows from table
                ids.forEach(id => {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) row.remove();
                });

                // Reset checkboxes
                document.getElementById('selectAll').checked = false;
                updateBulkRemoveButton();

                // Show success message
                alert('Items removed successfully!');
                
                // Reload page if no items left
                const remainingRows = document.querySelectorAll('#collectables-list tr');
                if (remainingRows.length === 0) {
                    location.reload();
                }
            } else {
                alert('Failed to remove items. Please try again.');
            }
        } catch (error) {
            console.error('Error removing items:', error);
            alert('An error occurred. Please try again.');
        }
    }
</script>

</x-app>
