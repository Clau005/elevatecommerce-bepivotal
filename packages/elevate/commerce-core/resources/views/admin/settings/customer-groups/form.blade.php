@php
    $isEdit = isset($customerGroup);
    $pageTitle = $isEdit ? 'Edit Customer Group' : 'Create Customer Group';
@endphp

<x-app pageTitle="{{ $pageTitle }}" title="{{ $pageTitle }} - Settings" description="Manage customer group details">

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">{{ $pageTitle }}</h2>
                    <div class="flex items-center space-x-3">
                        @if($isEdit)
                            <a href="{{ route('admin.settings.customer-groups.show', $customerGroup) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                View Customer Group
                            </a>
                        @endif
                        <a href="{{ route('admin.settings.customer-groups.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Customer Groups
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.settings.customer-groups.update', $customerGroup) : route('admin.settings.customer-groups.store') }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        {{-- Group Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Group Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', $isEdit ? $customerGroup->name : '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Handle --}}
                        <div>
                            <label for="handle" class="block text-sm font-medium text-gray-700">
                                Handle
                            </label>
                            <input type="text" 
                                   name="handle" 
                                   id="handle"
                                   value="{{ old('handle', $isEdit ? $customerGroup->handle : '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('handle') border-red-300 @enderror"
                                   placeholder="Auto-generated from name if left empty">
                            @error('handle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Used for API and internal references. Leave empty to auto-generate.
                            </p>
                        </div>
                    </div>

                    {{-- Default Group --}}
                    <div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="is_default" 
                                       id="is_default"
                                       value="1"
                                       {{ old('is_default', $isEdit ? $customerGroup->is_default : false) ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_default" class="font-medium text-gray-700">
                                    Default Customer Group
                                </label>
                                <p class="text-gray-500">
                                    New customers will be automatically assigned to this group if checked.
                                </p>
                            </div>
                        </div>
                        @error('is_default')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($isEdit && $customerGroup->customers_count > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Customer Group Information
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>This customer group currently has {{ $customerGroup->customers_count }} customer(s) assigned to it.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <a href="{{ route('admin.settings.show', 'customer-groups') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $isEdit ? 'Update Customer Group' : 'Create Customer Group' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-generate handle from name if handle is empty
        document.getElementById('name').addEventListener('blur', function() {
            const handleInput = document.getElementById('handle');
            if (!handleInput.value.trim()) {
                const name = this.value.trim();
                if (name) {
                    const handle = name.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                    handleInput.value = handle;
                }
            }
        });
    </script>

</x-app>