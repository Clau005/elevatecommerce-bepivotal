<x-app pageTitle="{{ $isEdit ? 'Edit Currency' : 'Create Currency' }}" title="{{ $isEdit ? 'Edit Currency' : 'Create Currency' }} - Admin" description="{{ $isEdit ? 'Edit currency details and exchange rates' : 'Add a new currency to the system' }}">

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $isEdit ? 'Edit Currency' : 'Create Currency' }}
                        </h1>
                        <p class="text-gray-600 mt-1">
                            {{ $isEdit ? 'Update currency details and exchange rates' : 'Add a new currency to your store' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.settings.currencies.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Currencies
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.settings.currencies.update', $currency) : route('admin.settings.currencies.store') }}" class="p-6">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Code --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Currency Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $currency->code) }}"
                               maxlength="3"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">3-letter ISO currency code (e.g., USD, EUR, GBP)</p>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Currency Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $currency->name) }}"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Symbol --}}
                    <div>
                        <label for="symbol" class="block text-sm font-medium text-gray-700 mb-2">
                            Currency Symbol <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="symbol" 
                               name="symbol" 
                               value="{{ old('symbol', $currency->symbol) }}"
                               maxlength="10"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('symbol') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Currency symbol (e.g., $, €, £)</p>
                        @error('symbol')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Exchange Rate --}}
                    <div>
                        <label for="exchange_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Exchange Rate <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="exchange_rate" 
                               name="exchange_rate" 
                               value="{{ old('exchange_rate', $currency->exchange_rate ?? 1) }}"
                               step="0.0001"
                               min="0"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('exchange_rate') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Exchange rate relative to base currency</p>
                        @error('exchange_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Enabled --}}
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_enabled" 
                               name="is_enabled" 
                               value="1"
                               {{ old('is_enabled', $currency->is_enabled ?? true) ? 'checked' : '' }}
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
                               {{ old('is_default', $currency->is_default ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="is_default" class="ml-2 text-sm font-medium text-gray-700">
                            Set as Default Currency
                        </label>
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="mt-8 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.settings.currencies.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ $isEdit ? 'Update Currency' : 'Create Currency' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app>
