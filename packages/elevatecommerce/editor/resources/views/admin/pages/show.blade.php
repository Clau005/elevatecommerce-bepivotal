@extends('core::admin.layouts.app')

@section('title', 'Page: ' . $page->title)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $page->title }}</h1>
            @if($page->is_active)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                    Published
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-2">
                    Draft
                </span>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.visual-editor.pages', ['theme' => $page->theme_id ?? 1, 'page' => $page]) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Visual Editor
            </a>
            <a href="{{ route('admin.pages.edit', $page) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Edit Settings
            </a>
            <a href="{{ route('admin.pages.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Back to Pages
            </a>
        </div>
    </div>

    {{-- Page Details --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Title</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $page->title }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Slug</dt>
                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $page->slug }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Theme</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $page->theme->name ?? 'N/A' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    @if($page->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Published
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Draft
                        </span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Created</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $page->created_at->format('M d, Y') }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $page->updated_at->diffForHumans() }}</dd>
            </div>

            @if($page->excerpt)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Excerpt</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $page->excerpt }}</dd>
                </div>
            @endif

            @if($page->meta_title || $page->meta_description)
                <div class="sm:col-span-2 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">SEO Settings</h4>
                    <dl class="space-y-3">
                        @if($page->meta_title)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Meta Title</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $page->meta_title }}</dd>
                            </div>
                        @endif
                        @if($page->meta_description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $page->meta_description }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif
        </dl>
    </div>

    {{-- Actions --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
        <div class="flex gap-3">
            @if(!$page->is_active)
                <form action="{{ route('admin.pages.publish', $page) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        Publish Page
                    </button>
                </form>
            @endif
            
            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                    Delete Page
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
