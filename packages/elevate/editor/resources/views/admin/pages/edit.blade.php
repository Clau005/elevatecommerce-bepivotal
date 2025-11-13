<x-app pageTitle="Edit Page" title="Edit Page - Admin" description="Update page settings">
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Page: {{ $page->title }}</h1>
    </div>

    <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                Page Title <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                Slug <span class="text-red-500">*</span>
            </label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" required
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="mb-6">
            <label for="theme_id" class="block text-sm font-medium text-gray-700 mb-1">
                Theme <span class="text-red-500">*</span>
            </label>
            <select name="theme_id" id="theme_id" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($themes as $theme)
                    <option value="{{ $theme->id }}" {{ old('theme_id', $page->theme_id) == $theme->id ? 'selected' : '' }}>
                        {{ $theme->name }} {{ $theme->is_active ? '(Active)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
            <textarea name="excerpt" id="excerpt" rows="2"
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('excerpt', $page->excerpt) }}</textarea>
        </div>

        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                Status <span class="text-red-500">*</span>
            </label>
            <select name="status" id="status" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="draft" {{ old('status', $page->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $page->status) == 'published' ? 'selected' : '' }}>Published</option>
            </select>
            <p class="mt-1 text-sm text-gray-500">Only published pages are visible on the frontend</p>
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Active (visible on site)</span>
            </label>
        </div>

        <div class="border-t border-gray-200 pt-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
            
            <div class="mb-4">
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="2"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('admin.visual-editor.pages', ['theme' => $page->theme_id ?? 1, 'page' => $page]) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                → Edit Visual Design
            </a>
            <div class="flex gap-3">
                <a href="{{ route('admin.pages.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

</x-app>
