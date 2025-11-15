@extends('core::admin.layouts.app')

@section('title', 'General Settings')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.settings.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
            </div>
            <p class="mt-1 text-sm text-gray-600">Manage your store's basic information and preferences</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Store Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Store Information</h2>
            
            <div class="space-y-6">
                <!-- Store Name -->
                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Store Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="store_name" 
                        name="store_name" 
                        value="{{ old('store_name', config('app.name')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    @error('store_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Email -->
                <div>
                    <label for="store_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Store Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="store_email" 
                        name="store_email" 
                        value="{{ old('store_email', config('mail.from.address')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                    <p class="mt-1 text-sm text-gray-500">This email will be used for customer communications</p>
                    @error('store_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Phone -->
                <div>
                    <label for="store_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Store Phone
                    </label>
                    <input 
                        type="tel" 
                        id="store_phone" 
                        name="store_phone" 
                        value="{{ old('store_phone', config('store.phone')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('store_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Description -->
                <div>
                    <label for="store_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Store Description
                    </label>
                    <textarea 
                        id="store_description" 
                        name="store_description" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >{{ old('store_description', config('store.description')) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">A brief description of your store</p>
                    @error('store_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Regional Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Regional Settings</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Timezone -->
                <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                        Timezone <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="timezone" 
                        name="timezone"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ config('app.timezone') === $tz ? 'selected' : '' }}>
                                {{ $tz }}
                            </option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Format -->
                <div>
                    <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">
                        Date Format
                    </label>
                    <select 
                        id="date_format" 
                        name="date_format"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="Y-m-d" {{ config('store.date_format') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                        <option value="m/d/Y" {{ config('store.date_format') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        <option value="d/m/Y" {{ config('store.date_format') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                        <option value="d-m-Y" {{ config('store.date_format') === 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                    </select>
                    @error('date_format')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Time Format -->
                <div>
                    <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">
                        Time Format
                    </label>
                    <select 
                        id="time_format" 
                        name="time_format"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="H:i:s" {{ config('store.time_format') === 'H:i:s' ? 'selected' : '' }}>24-hour (HH:MM:SS)</option>
                        <option value="h:i:s A" {{ config('store.time_format') === 'h:i:s A' ? 'selected' : '' }}>12-hour (hh:mm:ss AM/PM)</option>
                    </select>
                    @error('time_format')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Week Start Day -->
                <div>
                    <label for="week_start" class="block text-sm font-medium text-gray-700 mb-2">
                        Week Starts On
                    </label>
                    <select 
                        id="week_start" 
                        name="week_start"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="0" {{ config('store.week_start') == 0 ? 'selected' : '' }}>Sunday</option>
                        <option value="1" {{ config('store.week_start') == 1 ? 'selected' : '' }}>Monday</option>
                    </select>
                    @error('week_start')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Logo & Branding -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Logo & Branding</h2>
            
            <div class="space-y-6">
                <!-- Logo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Store Logo
                    </label>
                    <div class="flex items-center space-x-6">
                        <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            <i class="fas fa-image text-3xl text-gray-400"></i>
                        </div>
                        <div>
                            <input 
                                type="file" 
                                id="logo" 
                                name="logo" 
                                accept="image/*"
                                class="hidden"
                            >
                            <label 
                                for="logo"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer"
                            >
                                <i class="fas fa-upload mr-2"></i>
                                Upload Logo
                            </label>
                            <p class="mt-2 text-sm text-gray-500">PNG, JPG or SVG. Max 2MB.</p>
                        </div>
                    </div>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Favicon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Favicon
                    </label>
                    <div class="flex items-center space-x-6">
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            <i class="fas fa-image text-2xl text-gray-400"></i>
                        </div>
                        <div>
                            <input 
                                type="file" 
                                id="favicon" 
                                name="favicon" 
                                accept="image/*"
                                class="hidden"
                            >
                            <label 
                                for="favicon"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer"
                            >
                                <i class="fas fa-upload mr-2"></i>
                                Upload Favicon
                            </label>
                            <p class="mt-2 text-sm text-gray-500">ICO or PNG. 32x32 or 64x64 pixels.</p>
                        </div>
                    </div>
                    @error('favicon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end space-x-4">
            <a 
                href="{{ route('admin.settings.index') }}"
                class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </a>
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
