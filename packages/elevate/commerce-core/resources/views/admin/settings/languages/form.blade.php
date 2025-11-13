<x-app pageTitle="{{ $isEdit ? 'Edit Language' : 'Create Language' }}" title="{{ $isEdit ? 'Edit Language' : 'Create Language' }} - Admin" description="{{ $isEdit ? 'Edit language details' : 'Add a new language to the system' }}">

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $isEdit ? 'Edit Language' : 'Create Language' }}
                        </h1>
                        <p class="text-gray-600 mt-1">
                            {{ $isEdit ? 'Update language details' : 'Add a new language to your store' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.settings.languages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Languages
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.settings.languages.update', $language) : route('admin.settings.languages.store') }}" class="p-6">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Code --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Language Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $language->code) }}"
                               maxlength="10"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">ISO language code (e.g., en, es, fr, de)</p>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Language Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $language->name) }}"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Display name (e.g., English, Spanish, French)</p>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Enabled --}}
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_enabled" 
                               name="is_enabled" 
                               value="1"
                               {{ old('is_enabled', $language->is_enabled ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="is_enabled" class="ml-2 text-sm font-medium text-gray-700">
                            Enabled
                        </label>
                    </div>

                    {{-- Default --}}
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_default" 
                               name="is_default" 
                               value="1"
                               {{ old('is_default', $language->is_default ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="is_default" class="ml-2 text-sm font-medium text-gray-700">
                            Set as Default Language
                        </label>
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="mt-8 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.settings.languages.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ $isEdit ? 'Update Language' : 'Create Language' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app>
