@extends('core::admin.layouts.app')

@section('title', 'Add Currency')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.settings.currencies.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Add Currency</h1>
            </div>
            <p class="mt-1 text-sm text-gray-600">Create a new currency</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.currencies.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Currency Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Currency Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Currency Code <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code" 
                        value="{{ old('code') }}"
                        maxlength="3"
                        placeholder="GBP"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500">ISO 4217 code (3 letters)</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Currency Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        placeholder="British Pound"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Symbol -->
                <div>
                    <label for="symbol" class="block text-sm font-medium text-gray-700 mb-2">
                        Symbol <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="symbol" 
                        name="symbol" 
                        value="{{ old('symbol') }}"
                        placeholder="£"
                        maxlength="10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    @error('symbol')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Decimal Places -->
                <div>
                    <label for="decimal_places" class="block text-sm font-medium text-gray-700 mb-2">
                        Decimal Places <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="decimal_places" 
                        name="decimal_places" 
                        value="{{ old('decimal_places', 2) }}"
                        min="0"
                        max="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500">Usually 2 for most currencies</p>
                    @error('decimal_places')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exchange Rate -->
                <div>
                    <label for="exchange_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        Exchange Rate <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="exchange_rate" 
                        name="exchange_rate" 
                        value="{{ old('exchange_rate', 1.000000) }}"
                        step="0.000001"
                        min="0.000001"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500">Relative to your base currency</p>
                    @error('exchange_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Settings</h2>
            
            <div class="space-y-4">
                <!-- Is Default -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="is_default" 
                        name="is_default" 
                        value="1"
                        {{ old('is_default') ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="is_default" class="ml-3 block text-sm text-gray-700">
                        Set as default currency
                    </label>
                </div>

                <!-- Is Enabled -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="is_enabled" 
                        name="is_enabled" 
                        value="1"
                        {{ old('is_enabled', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="is_enabled" class="ml-3 block text-sm text-gray-700">
                        Enable this currency
                    </label>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4">
            <a 
                href="{{ route('admin.settings.currencies.index') }}"
                class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </a>
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
            >
                <i class="fas fa-save mr-2"></i>
                Create Currency
            </button>
        </div>
    </form>
</div>
@endsection
