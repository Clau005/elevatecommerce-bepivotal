@extends('core::admin.layouts.app')

@section('title', 'General Settings')

@section('content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <x-core::heading level="1" subtitle="Manage your store's basic information and preferences">
                General Settings
            </x-core::heading>
        </div>
        <div>
            <x-core::button 
                variant="secondary" 
                size="sm"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('admin.settings.index') }}'"
            >
                Back to Settings
            </x-core::button>
        </div>
    </div>

    <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Store Information -->
        <x-core::card title="Store Information">
            <div class="space-y-4">
                <x-core::input 
                    id="store_name"
                    name="store_name"
                    label="Store Name"
                    :required="true"
                    value="{{ old('store_name', config('app.name')) }}"
                    :error="$errors->first('store_name')"
                />

                <x-core::input 
                    type="email"
                    id="store_email"
                    name="store_email"
                    label="Store Email"
                    :required="true"
                    value="{{ old('store_email', config('mail.from.address')) }}"
                    hint="This email will be used for customer communications"
                    :error="$errors->first('store_email')"
                />

                <x-core::input 
                    type="tel"
                    id="store_phone"
                    name="store_phone"
                    label="Store Phone"
                    value="{{ old('store_phone', config('store.phone')) }}"
                    :error="$errors->first('store_phone')"
                />

                <x-core::textarea 
                    id="store_description"
                    name="store_description"
                    label="Store Description"
                    :rows="3"
                    hint="A brief description of your store"
                    :error="$errors->first('store_description')"
                >{{ old('store_description', config('store.description')) }}</x-core::textarea>
            </div>
        </x-core::card>

        <!-- Regional Settings -->
        <x-core::card title="Regional Settings">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-core::select 
                    id="timezone"
                    name="timezone"
                    label="Timezone"
                    :required="true"
                    :error="$errors->first('timezone')"
                >
                    @foreach(timezone_identifiers_list() as $tz)
                        <option value="{{ $tz }}" {{ config('app.timezone') === $tz ? 'selected' : '' }}>
                            {{ $tz }}
                        </option>
                    @endforeach
                </x-core::select>

                <x-core::select 
                    id="date_format"
                    name="date_format"
                    label="Date Format"
                    :error="$errors->first('date_format')"
                >
                    <option value="Y-m-d" {{ config('store.date_format') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                    <option value="m/d/Y" {{ config('store.date_format') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                    <option value="d/m/Y" {{ config('store.date_format') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                    <option value="d-m-Y" {{ config('store.date_format') === 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                </x-core::select>

                <x-core::select 
                    id="time_format"
                    name="time_format"
                    label="Time Format"
                    :error="$errors->first('time_format')"
                >
                    <option value="H:i:s" {{ config('store.time_format') === 'H:i:s' ? 'selected' : '' }}>24-hour (HH:MM:SS)</option>
                    <option value="h:i:s A" {{ config('store.time_format') === 'h:i:s A' ? 'selected' : '' }}>12-hour (hh:mm:ss AM/PM)</option>
                </x-core::select>

                <x-core::select 
                    id="week_start"
                    name="week_start"
                    label="Week Starts On"
                    :error="$errors->first('week_start')"
                >
                    <option value="0" {{ config('store.week_start') == 0 ? 'selected' : '' }}>Sunday</option>
                    <option value="1" {{ config('store.week_start') == 1 ? 'selected' : '' }}>Monday</option>
                </x-core::select>
            </div>
        </x-core::card>

        <!-- Logo & Branding -->
        <x-core::card title="Logo & Branding">
            <div class="space-y-6">
                <!-- Logo -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                        Store Logo
                    </label>
                    <input type="hidden" name="logo_url" value="{{ old('logo_url', config('store.logo_url')) }}">
                    <div 
                        data-media-picker
                        data-input-name="logo_url"
                        data-type="images"
                        data-label="Upload Logo"
                        data-initial-value="{{ old('logo_url', config('store.logo_url')) }}"
                    ></div>
                    <p class="mt-2 text-xs text-gray-500">PNG, JPG or SVG. Max 2MB.</p>
                    @error('logo_url')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Favicon -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                        Favicon
                    </label>
                    <input type="hidden" name="favicon_url" value="{{ old('favicon_url', config('store.favicon_url')) }}">
                    <div 
                        data-media-picker
                        data-input-name="favicon_url"
                        data-type="images"
                        data-label="Upload Favicon"
                        data-initial-value="{{ old('favicon_url', config('store.favicon_url')) }}"
                    ></div>
                    <p class="mt-2 text-xs text-gray-500">ICO or PNG. 32x32 or 64x64 pixels.</p>
                    @error('favicon_url')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-core::card>

        <!-- Save Button -->
        <div class="flex items-center justify-end space-x-2">
            <x-core::button 
                variant="secondary"
                type="button"
                onclick="window.location.href='{{ route('admin.settings.index') }}'"
            >
                Cancel
            </x-core::button>
            <x-core::button 
                variant="primary"
                type="submit"
                icon="fas fa-save"
            >
                Save Changes
            </x-core::button>
        </div>
    </form>
</div>
@endsection
