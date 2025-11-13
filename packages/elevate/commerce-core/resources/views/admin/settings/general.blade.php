<x-app pageTitle="General Settings" title="General Settings - Admin" description="Configure your store's basic information and settings">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
                <p class="text-gray-600 mt-1">Store name, contact information, and basic settings</p>
            </div>
            <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Settings
            </a>
        </div>

        {{-- Settings Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" class="divide-y divide-gray-200">
                @csrf

                {{-- Store Information --}}
                <div class="p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Store Information</h3>
                    </div>

                    {{-- Store Name --}}
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Name
                        </label>
                        <input 
                            type="text" 
                            id="store_name" 
                            name="store_name" 
                            value="{{ old('store_name', config('app.name')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="My Store"
                        >
                        @error('store_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Store Email --}}
                    <div>
                        <label for="store_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Email
                        </label>
                        <input 
                            type="email" 
                            id="store_email" 
                            name="store_email" 
                            value="{{ old('store_email', config('mail.from.address')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="store@example.com"
                        >
                        @error('store_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Used for order confirmations and customer communications</p>
                    </div>

                    {{-- Store Phone --}}
                    <div>
                        <label for="store_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Phone
                        </label>
                        <input 
                            type="tel" 
                            id="store_phone" 
                            name="store_phone" 
                            value="{{ old('store_phone', config('commerce.store.phone', '')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="+1 (555) 123-4567"
                        >
                        @error('store_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Store Address --}}
                    <div>
                        <label for="store_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Address
                        </label>
                        <textarea 
                            id="store_address" 
                            name="store_address" 
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="123 Main Street, City, State, ZIP"
                        >{{ old('store_address', config('commerce.store.address', '')) }}</textarea>
                        @error('store_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror>
                    </div>
                </div>

                {{-- Regional Settings --}}
                <div class="p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Regional Settings</h3>
                    </div>

                    {{-- Timezone --}}
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Timezone
                        </label>
                        <select 
                            id="timezone" 
                            name="timezone"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="UTC" {{ config('app.timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ config('app.timezone') === 'America/New_York' ? 'selected' : '' }}>Eastern Time (US & Canada)</option>
                            <option value="America/Chicago" {{ config('app.timezone') === 'America/Chicago' ? 'selected' : '' }}>Central Time (US & Canada)</option>
                            <option value="America/Denver" {{ config('app.timezone') === 'America/Denver' ? 'selected' : '' }}>Mountain Time (US & Canada)</option>
                            <option value="America/Los_Angeles" {{ config('app.timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (US & Canada)</option>
                            <option value="Europe/London" {{ config('app.timezone') === 'Europe/London' ? 'selected' : '' }}>London</option>
                            <option value="Europe/Paris" {{ config('app.timezone') === 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                        </select>
                        @error('timezone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date Format --}}
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">
                            Date Format
                        </label>
                        <select 
                            id="date_format" 
                            name="date_format"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="Y-m-d">YYYY-MM-DD (2024-01-15)</option>
                            <option value="m/d/Y">MM/DD/YYYY (01/15/2024)</option>
                            <option value="d/m/Y">DD/MM/YYYY (15/01/2024)</option>
                            <option value="d M Y">DD Mon YYYY (15 Jan 2024)</option>
                        </select>
                        @error('date_format')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Order Settings --}}
                <div class="p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Settings</h3>
                    </div>

                    {{-- Order Prefix --}}
                    <div>
                        <label for="order_prefix" class="block text-sm font-medium text-gray-700 mb-2">
                            Order Number Prefix
                        </label>
                        <input 
                            type="text" 
                            id="order_prefix" 
                            name="order_prefix" 
                            value="{{ old('order_prefix', config('commerce.orders.prefix', 'ORD')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="ORD"
                            maxlength="10"
                        >
                        @error('order_prefix')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Example: ORD-12345</p>
                    </div>

                    {{-- Low Stock Threshold --}}
                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-2">
                            Low Stock Threshold
                        </label>
                        <input 
                            type="number" 
                            id="low_stock_threshold" 
                            name="low_stock_threshold" 
                            value="{{ old('low_stock_threshold', config('commerce.inventory.low_stock_threshold', 10)) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            min="0"
                        >
                        @error('low_stock_threshold')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Notify when stock falls below this number</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="p-6 flex items-center justify-end gap-3 bg-gray-50">
                    <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- Info Box --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Configuration Note</p>
                    <p>Some settings may require cache clearing to take effect. Changes to email and timezone settings will apply immediately to new orders and communications.</p>
                </div>
            </div>
        </div>
    </div>

</x-app>
