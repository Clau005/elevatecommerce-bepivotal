@extends('core::admin.layouts.app')

@section('title', 'Edit Currency')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <x-core::heading level="1" subtitle="Update a new currency">
                 Edit Currency
            </x-core::heading>
        </div>
        <div>
            <x-core::button 
                variant="secondary" 
                size="sm"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('admin.settings.index') }}'"
            >
                Back to Currencies
            </x-core::button>
        </div>
    </div>

    <form action="{{ route('admin.settings.currencies.update', $currency) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Currency Information -->
        <x-core::card title="Currency Information">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-core::input 
                    id="code"
                    name="code"
                    label="Currency Code"
                    :required="true"
                    value="{{ old('code', $currency->code) }}"
                    maxlength="3"
                    class="uppercase"
                    :error="$errors->first('code')"
                />

                <x-core::input 
                    id="name"
                    name="name"
                    label="Currency Name"
                    :required="true"
                    value="{{ old('name', $currency->name) }}"
                    :error="$errors->first('name')"
                />

                <x-core::input 
                    id="symbol"
                    name="symbol"
                    label="Symbol"
                    :required="true"
                    value="{{ old('symbol', $currency->symbol) }}"
                    maxlength="10"
                    :error="$errors->first('symbol')"
                />

                <x-core::input 
                    type="number"
                    id="decimal_places"
                    name="decimal_places"
                    label="Decimal Places"
                    :required="true"
                    value="{{ old('decimal_places', $currency->decimal_places) }}"
                    min="0"
                    max="4"
                    :error="$errors->first('decimal_places')"
                />

                <x-core::input 
                    type="number"
                    id="exchange_rate"
                    name="exchange_rate"
                    label="Exchange Rate"
                    :required="true"
                    value="{{ old('exchange_rate', $currency->exchange_rate) }}"
                    step="0.000001"
                    min="0.000001"
                    :error="$errors->first('exchange_rate')"
                />
            </div>
        </x-core::card>

        <!-- Settings -->
        <x-core::card title="Settings">
            <div class="space-y-4">
                <x-core::checkbox 
                    id="is_default"
                    name="is_default"
                    value="1"
                    label="Set as default currency"
                    :checked="old('is_default', $currency->is_default)"
                />

                <x-core::checkbox 
                    id="is_enabled"
                    name="is_enabled"
                    value="1"
                    label="Enable this currency"
                    :checked="old('is_enabled', $currency->is_enabled)"
                />
            </div>
        </x-core::card>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-2">
            <x-core::button 
                variant="secondary"
                type="button"
                onclick="window.location.href='{{ route('admin.settings.currencies.index') }}'"
            >
                Cancel
            </x-core::button>
            <x-core::button 
                variant="primary"
                type="submit"
                icon="fas fa-save"
            >
                Update Currency
            </x-core::button>
        </div>
    </form>
</div>
@endsection
