@php
    $isEdit = isset($role);
    $pageTitle = $isEdit ? 'Edit Role' : 'Create Role';
@endphp

<x-app pageTitle="{{ $pageTitle }}" title="{{ $pageTitle }} - Settings" description="Manage role details and permissions">

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">{{ $pageTitle }}</h2>
                    <div class="flex items-center space-x-3">
                        {{-- @if($isEdit)
                            <a href="{{ route('admin.settings.roles.permissions', $role) }}" 
                               class="inline-flex items-center px-3 py-2 border border-purple-300 shadow-sm text-sm leading-4 font-medium rounded-md text-purple-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Manage Permissions
                            </a>
                        @endif --}}
                        <a href="{{ route('admin.settings.roles.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Roles
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.settings.roles.update', $role) : route('admin.settings.roles.store') }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        {{-- Role Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Role Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', $isEdit ? $role->name : '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Internal role name (e.g., admin, manager, editor)
                            </p>
                        </div>

                        {{-- Display Name --}}
                        <div>
                            <label for="display_name" class="block text-sm font-medium text-gray-700">
                                Display Name
                            </label>
                            <input type="text" 
                                   name="display_name" 
                                   id="display_name"
                                   value="{{ old('display_name', $isEdit ? $role->display_name : '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('display_name') border-red-300 @enderror"
                                   placeholder="Human-readable name">
                            @error('display_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                User-friendly name shown in the interface
                            </p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description"
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-300 @enderror"
                                  placeholder="Brief description of this role's purpose and responsibilities">{{ old('description', $isEdit ? $role->description : '') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Guard Name --}}
                    <div>
                        <label for="guard_name" class="block text-sm font-medium text-gray-700">
                            Guard <span class="text-red-500">*</span>
                        </label>
                        <select name="guard_name" 
                                id="guard_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('guard_name') border-red-300 @enderror"
                                required>
                            <option value="">Select Guard</option>
                            <option value="web" {{ old('guard_name', $isEdit ? $role->guard_name : '') === 'web' ? 'selected' : '' }}>Web</option>
                            <option value="api" {{ old('guard_name', $isEdit ? $role->guard_name : '') === 'api' ? 'selected' : '' }}>API</option>
                            <option value="staff" {{ old('guard_name', $isEdit ? $role->guard_name : '') === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                        @error('guard_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Authentication guard this role applies to
                        </p>
                    </div>

                    @if($isEdit && $role->users_count > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Role Assignment Information
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>This role is currently assigned to {{ $role->users_count }} user(s).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <a href="{{ route('admin.settings.show', 'roles') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ $isEdit ? 'Update Role' : 'Create Role' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-generate display name from name if display name is empty
        document.getElementById('name').addEventListener('blur', function() {
            const displayNameInput = document.getElementById('display_name');
            if (!displayNameInput.value.trim()) {
                const name = this.value.trim();
                if (name) {
                    // Convert snake_case or kebab-case to Title Case
                    const displayName = name.replace(/[-_]/g, ' ')
                        .replace(/\b\w/g, l => l.toUpperCase());
                    displayNameInput.value = displayName;
                }
            }
        });
    </script>

</x-app>