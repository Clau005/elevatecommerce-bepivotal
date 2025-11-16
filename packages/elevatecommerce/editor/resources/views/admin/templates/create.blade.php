@extends('core::admin.layouts.app')

@section('title', 'Create Template')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create Template</h1>
        <p class="mt-1 text-sm text-gray-600">Create a new template for your models</p>
    </div>

    <form action="{{ route('admin.templates.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
        @csrf

        {{-- Name --}}
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Template Name <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name') }}"
                   required
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Slug --}}
        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                Slug
            </label>
            <input type="text" 
                   name="slug" 
                   id="slug" 
                   value="{{ old('slug') }}"
                   placeholder="Leave blank to auto-generate"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('slug') border-red-500 @enderror">
            <p class="mt-1 text-sm text-gray-500">URL-friendly identifier (auto-generated from name if left blank)</p>
            @error('slug')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Model Type --}}
        <div class="mb-6">
            <label for="model_type" class="block text-sm font-medium text-gray-700 mb-1">
                Model Type <span class="text-red-500">*</span>
            </label>
            <select name="model_type" 
                    id="model_type" 
                    required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('model_type') border-red-500 @enderror">
                <option value="">Select a model type...</option>
                @foreach($availableModels as $model)
                    <option value="{{ $model['value'] }}" {{ old('model_type') == $model['value'] ? 'selected' : '' }}>
                        {{ $model['label'] }} - {{ $model['description'] ?? '' }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-sm text-gray-500">Which type of content will this template be used for?</p>
            @error('model_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Description
            </label>
            <textarea name="description" 
                      id="description" 
                      rows="3"
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            <p class="mt-1 text-sm text-gray-500">Brief description of this template's purpose</p>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- SEO Section --}}
        <div class="border-t border-gray-200 pt-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
            
            {{-- Meta Title --}}
            <div class="mb-4">
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Title
                </label>
                <input type="text" 
                       name="meta_title" 
                       id="meta_title" 
                       value="{{ old('meta_title') }}"
                       maxlength="255"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-sm text-gray-500">Default meta title for pages using this template</p>
            </div>

            {{-- Meta Description --}}
            <div class="mb-4">
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Description
                </label>
                <textarea name="meta_description" 
                          id="meta_description" 
                          rows="2"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('meta_description') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Default meta description for SEO</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.templates.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Create & Edit Visual
            </button>
        </div>
    </form>
</div>

@endsection
