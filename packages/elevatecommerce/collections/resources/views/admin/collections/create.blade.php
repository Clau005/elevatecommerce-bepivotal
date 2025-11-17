@extends('core::admin.layouts.app')

@section('title', 'Create Collection')

@section('content')

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Collection</h1>
            <p class="text-gray-600 mt-1">Create a new collection to organize your content.</p>
        </div>
        <a href="{{ route('admin.collections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-md transition-all duration-200 text-sm hover:bg-gray-300">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Collections
        </a>
    </div>

    <form action="{{ route('admin.collections.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h3>
                
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Collection Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('slug') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">URL-friendly version (e.g., electronics, mens-clothing)</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Collection Image --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Collection Image
                        </label>
                        <input type="hidden" name="image" value="{{ old('image') }}">
                        <div 
                            data-media-picker
                            data-input-name="image"
                            data-type="images"
                            data-label="Upload Image"
                            data-initial-value="{{ old('image') }}"
                        ></div>
                        <p class="mt-2 text-sm text-gray-500">Used for collection banner and listings. PNG, JPG or WebP.</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <x-core::richtext 
                        id="description"
                        name="description"
                        label="Description (Optional)"
                        hint="Rich text description displayed on the collection page"
                        :error="$errors->first('description')"
                    >{{ old('description') }}</x-core::richtext>

                    {{-- Template --}}
                    <div>
                        <label for="template_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Template <span class="text-red-500">*</span>
                        </label>
                        <select name="template_id" id="template_id" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('template_id') border-red-300 @enderror">
                            <option value="">Select a template...</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
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
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
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
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('sort_order') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Items Per Page --}}
                    <div>
                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Items Per Page</label>
                        <input type="number" name="per_page" id="per_page" value="{{ old('per_page', 12) }}" min="1" max="100"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('per_page') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Number of products to show per page</p>
                        @error('per_page')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
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
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
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
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('meta_description') border-red-300 @enderror">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-black text-white font-medium rounded-md hover:bg-gray-800 transition-all text-sm">
                Create Collection
            </button>
        </div>
    </form>
</div>

<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });
</script>

@endsection
