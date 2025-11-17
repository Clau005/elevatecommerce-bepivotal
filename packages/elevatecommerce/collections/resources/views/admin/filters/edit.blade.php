@extends('core::admin.layouts.app')

@section('title', 'Edit Filter')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Filter: {{ $filter->name }}</h1>
            <p class="text-gray-600 mt-1">Manage filter settings and values.</p>
        </div>
        <a href="{{ route('admin.filters.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Filters
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter Settings Form --}}
    <form action="{{ route('admin.filters.update', $filter) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Filter Settings</h2>

            {{-- Filter Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Filter Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $filter->name) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Filter Slug --}}
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                    Slug <span class="text-red-500">*</span>
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $filter->slug) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                    <option value="select" {{ old('type', $filter->type) == 'select' ? 'selected' : '' }}>Select</option>
                    <option value="checkbox" {{ old('type', $filter->type) == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                    <option value="range" {{ old('type', $filter->type) == 'range' ? 'selected' : '' }}>Range</option>
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
                    @foreach($collectableTypes as $modelClass => $config)
                        <option value="{{ $modelClass }}" {{ old('source_model', $filter->source_model) == $modelClass ? 'selected' : '' }}>
                            {{ $config['label'] ?? class_basename($modelClass) }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Select the model type to filter from registered collectable types</p>
                @error('source_model')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Source Column --}}
            <div>
                <label for="source_column" class="block text-sm font-medium text-gray-700 mb-1">
                    Source Column <span class="text-red-500">*</span>
                </label>
                <input type="text" name="source_column" id="source_column" value="{{ old('source_column', $filter->source_column) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('source_column')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Source Relation --}}
            <div>
                <label for="source_relation" class="block text-sm font-medium text-gray-700 mb-1">
                    Source Relation (Optional)
                </label>
                <input type="text" name="source_relation" id="source_relation" value="{{ old('source_relation', $filter->source_relation) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('source_relation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                    Sort Order
                </label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $filter->sort_order) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Active Status --}}
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $filter->is_active) ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Active
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
                Update Filter
            </button>
        </div>
    </form>

    {{-- Collection Assignments --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Collection Assignments</h2>
        
        @if($filter->collections->count() > 0)
            <div class="space-y-2 mb-4">
                @foreach($filter->collections as $collection)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <span class="font-medium text-gray-900">{{ $collection->name }}</span>
                            <span class="ml-2 text-sm text-gray-500">
                                ({{ $collection->pivot->is_active ? 'Active' : 'Inactive' }})
                            </span>
                        </div>
                        <a href="{{ route('admin.collections.edit', $collection) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Manage →
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 text-gray-500">
                <p>This filter is not assigned to any collections yet.</p>
                <p class="text-sm mt-1">Go to a collection's edit page to assign this filter.</p>
            </div>
        @endif
        
        <div class="pt-4 border-t">
            <a href="{{ route('admin.collections.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Assign to Collections
            </a>
        </div>
    </div>

    {{-- Filter Values Section --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Filter Values</h2>
            <form action="{{ route('admin.filters.sync-values', $filter) }}" method="POST">
                @csrf
                <button type="submit" 
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Sync Values from Database
                </button>
            </form>
        </div>

        @if($filter->values->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Label</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Count</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($filter->values as $value)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $value->label }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $value->slug }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $value->value }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $value->product_count }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($value->is_active)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="mt-2">No filter values yet. Click "Sync Values from Database" to auto-discover values.</p>
            </div>
        @endif
    </div>
</div>

@endsection
