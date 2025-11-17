<x-app pageTitle="Create Channel" title="Create Channel - Admin" description="Create a new sales channel">
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Sales Channel</h1>
                    <p class="text-gray-600 mt-1">Add a new channel to organize your sales</p>
                </div>
                <a href="{{ route('admin.settings.channels.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.settings.channels.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Channel Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="e.g., Online Store, POS, Marketplace"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">A descriptive name for this sales channel</p>
                </div>

                {{-- Handle --}}
                <div>
                    <label for="handle" class="block text-sm font-medium text-gray-700 mb-2">
                        Handle
                    </label>
                    <input type="text" 
                           name="handle" 
                           id="handle" 
                           value="{{ old('handle') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono @error('handle') border-red-500 @enderror"
                           placeholder="e.g., online-store, pos">
                    @error('handle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">URL-friendly identifier (auto-generated if left blank)</p>
                </div>

                {{-- URL --}}
                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL
                    </label>
                    <input type="url" 
                           name="url" 
                           id="url" 
                           value="{{ old('url') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('url') border-red-500 @enderror"
                           placeholder="https://example.com">
                    @error('url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Optional: The website URL for this channel</p>
                </div>

                {{-- Default --}}
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="default" 
                               id="default" 
                               value="1"
                               {{ old('default') ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="ml-3">
                        <label for="default" class="text-sm font-medium text-gray-700">
                            Set as default channel
                        </label>
                        <p class="text-sm text-gray-500">New orders will be assigned to this channel by default</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.settings.channels.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    Create Channel
                </button>
            </div>
        </form>
    </div>
</x-app>
