<x-app pageTitle="Edit Tag" title="Edit Tag - Admin" description="Edit tag details">

    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit Tag</h2>
                <p class="mt-1 text-sm text-gray-600">Update tag details. This tag is currently used by {{ $usageCount }} item(s).</p>
            </div>

            <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Tag Value --}}
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1">
                        Tag Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="value" 
                           id="value" 
                           value="{{ old('value', $tag->value) }}"
                           required
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('value') border-red-500 @enderror">
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">The display name for this tag</p>
                </div>

                {{-- Tag Handle --}}
                <div>
                    <label for="handle" class="block text-sm font-medium text-gray-700 mb-1">
                        Handle
                    </label>
                    <input type="text" 
                           name="handle" 
                           id="handle" 
                           value="{{ old('handle', $tag->handle) }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-mono @error('handle') border-red-500 @enderror">
                    @error('handle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">URL-friendly identifier</p>
                </div>

                {{-- Usage Info --}}
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Tag Usage</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>This tag is currently attached to <strong>{{ $usageCount }}</strong> item(s). Updating this tag will affect all associated items.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.tags.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Update Tag
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app>
