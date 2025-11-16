<x-app pageTitle="Register Theme" title="Register Theme - Admin" description="Register a new theme">
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Register Theme</h1>
        <p class="mt-1 text-sm text-gray-600">Register a new theme (files should already exist in resources/views/themes/)</p>
    </div>

    <form action="{{ route('admin.themes.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
        @csrf

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Theme Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                   placeholder="Leave blank to auto-generate"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <p class="mt-1 text-sm text-gray-500">Must match the folder name in resources/views/themes/</p>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" id="description" rows="3"
                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label for="version" class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                <input type="text" name="version" id="version" value="{{ old('version', '1.0.0') }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                <input type="text" name="author" id="author" value="{{ old('author') }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.themes.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Register Theme
            </button>
        </div>
    </form>
</div>

</x-app>
