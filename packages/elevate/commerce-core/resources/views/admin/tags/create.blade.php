<x-app pageTitle="Create Tag" title="Create Tag - Admin" description="Create a new tag">

    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Create New Tag</h2>
                <p class="mt-1 text-sm text-gray-600">Add a new tag that can be used across products, blogs, and other content.</p>
            </div>

            <form action="{{ route('admin.tags.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                {{-- Tag Value --}}
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1">
                        Tag Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="value" 
                           id="value" 
                           value="{{ old('value') }}"
                           required
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('value') border-red-500 @enderror">
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">The display name for this tag (e.g., "Electronics", "Featured Product")</p>
                </div>

                {{-- Tag Handle (Optional) --}}
                <div>
                    <label for="handle" class="block text-sm font-medium text-gray-700 mb-1">
                        Handle (Optional)
                    </label>
                    <input type="text" 
                           name="handle" 
                           id="handle" 
                           value="{{ old('handle') }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-mono @error('handle') border-red-500 @enderror">
                    @error('handle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">URL-friendly identifier. Leave blank to auto-generate from tag name.</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.tags.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Create Tag
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app>
