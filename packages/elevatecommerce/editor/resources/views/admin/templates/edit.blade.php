@extends('core::admin.layouts.app')

@section('title', 'Edit Template: ' . $template->name)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Template: {{ $template->name }}</h1>
        <p class="mt-1 text-sm text-gray-600">Update template settings</p>
    </div>

    <form action="{{ route('admin.templates.update', $template) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Template Name <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name', $template->name) }}"
                   required
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Slug --}}
        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                Slug <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="slug" 
                   id="slug" 
                   value="{{ old('slug', $template->slug) }}"
                   required
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('slug') border-red-500 @enderror">
            <p class="mt-1 text-sm text-gray-500">URL-friendly identifier</p>
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
                @foreach($availableModels as $model)
                    <option value="{{ $model['value'] }}" {{ old('model_type', $template->model_type) == $model['value'] ? 'selected' : '' }}>
                        {{ $model['label'] }} - {{ $model['description'] ?? '' }}
                    </option>
                @endforeach
            </select>
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
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $template->description) }}</textarea>
        </div>

        {{-- Status --}}
        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                Status
            </label>
            <select name="status" 
                    id="status" 
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                <option value="draft" {{ old('status', $template->status) == 'draft' ? 'selected' : '' }}>
                    Draft
                </option>
                <option value="published" {{ old('status', $template->status) == 'published' ? 'selected' : '' }}>
                    Published
                </option>
            </select>
            <p class="mt-1 text-sm text-gray-500">Draft templates are not visible on the live site</p>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Active Status --}}
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="is_active" 
                       value="1"
                       {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Active (available for use)</span>
            </label>
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
                       value="{{ old('meta_title', $template->meta_title) }}"
                       maxlength="255"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            {{-- Meta Description --}}
            <div class="mb-4">
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                    Meta Description
                </label>
                <textarea name="meta_description" 
                          id="meta_description" 
                          rows="2"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('meta_description', $template->meta_description) }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            @php
                $activeTheme = \ElevateCommerce\Editor\Models\Theme::where('is_active', true)->first();
            @endphp
            @if($activeTheme)
                <a href="{{ route('admin.visual-editor.templates', ['theme' => $activeTheme->id, 'template' => $template->id]) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    → Edit Visual Design
                </a>
            @endif
            <div class="flex gap-3">
                <a href="{{ route('admin.templates.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
