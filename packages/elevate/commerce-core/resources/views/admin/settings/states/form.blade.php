<x-app pageTitle="{{ $isEdit ? 'Edit State' : 'Create State' }}" title="{{ $isEdit ? 'Edit State' : 'Create State' }} - Admin" description="{{ $isEdit ? 'Edit state/region details' : 'Add a new state/region' }}">

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $isEdit ? 'Edit State/Region' : 'Create State/Region' }}
                        </h1>
                        <p class="text-gray-600 mt-1">
                            {{ $isEdit ? 'Update state/region details for' : 'Add a new state/region to' }} {{ $country->name }}
                        </p>
                    </div>
                    <a href="{{ route('admin.settings.states.country', $country) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to {{ $country->name }}
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.settings.states.update', [$country, $state]) : route('admin.settings.states.store', $country) }}" class="p-6">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            State/Region Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $state->name) }}"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Code --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            State/Region Code
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $state->code) }}"
                               maxlength="10"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Optional state/region code (e.g., CA, NY, TX)</p>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="mt-8 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.settings.states.country', $country) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ $isEdit ? 'Update State/Region' : 'Create State/Region' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app>
