@extends('core::admin.layouts.app')

@section('title', 'Payment Settings')

@section('content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <x-core::heading level="1" subtitle="Manage payment gateway configurations and credentials">
                Payment Settings
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

    @if(session('success'))
    <x-core::alert type="success" dismissible>
        {{ session('success') }}
    </x-core::alert>
    @endif

    @if(session('error'))
    <x-core::alert type="error" dismissible>
        {{ session('error') }}
    </x-core::alert>
    @endif

    @foreach($gateways as $gateway)
    <x-core::card>
        <!-- Gateway Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-4">
            <div class="flex items-center space-x-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gray-50 border border-gray-200">
                    <i class="{{ $gateway->icon }} text-2xl text-gray-700"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $gateway->name }}</h2>
                    <p class="text-xs text-gray-600">{{ $gateway->description }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Credentials Status -->
                @if($gateway->isConfigured())
                    <x-core::badge color="green">
                        <i class="fas fa-check-circle mr-1"></i>
                        Configured
                    </x-core::badge>
                @else
                    <x-core::badge color="yellow">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Missing Credentials
                    </x-core::badge>
                @endif
                
                <!-- Enabled Toggle -->
                <form action="{{ route('admin.settings.payments.update', $gateway) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $gateway->name }}">
                    <input type="hidden" name="description" value="{{ $gateway->description }}">
                    <input type="hidden" name="test_mode" value="{{ $gateway->test_mode ? '1' : '0' }}">
                    <input type="hidden" name="enabled" value="{{ $gateway->enabled ? '0' : '1' }}">
                    <button type="submit" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $gateway->enabled ? 'bg-blue-600' : 'bg-gray-200' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $gateway->enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Gateway Settings Form -->
        <form action="{{ route('admin.settings.payments.update', $gateway) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Display Name -->
                <x-core::input 
                    id="name_{{ $gateway->id }}" 
                    name="name" 
                    label="Display Name"
                    :required="true"
                    value="{{ old('name', $gateway->name) }}"
                    :error="$errors->first('name')"
                />

                <!-- Test Mode Toggle -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                        Mode <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <x-core::radio 
                            id="test_mode_{{ $gateway->id }}_test"
                            name="test_mode" 
                            value="1" 
                            label="Test Mode (Sandbox)"
                            :checked="$gateway->test_mode"
                        />
                        <x-core::radio 
                            id="test_mode_{{ $gateway->id }}_live"
                            name="test_mode" 
                            value="0" 
                            label="Live Mode"
                            :checked="!$gateway->test_mode"
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        @if($gateway->test_mode)
                            Using {{ $gateway->gateway === 'stripe' ? 'test' : 'sandbox' }} credentials
                        @else
                            Using live/production credentials
                        @endif
                    </p>
                </div>
            </div>

            <!-- Description -->
            <x-core::textarea 
                id="description_{{ $gateway->id }}" 
                name="description" 
                label="Description"
                :rows="2"
                hint="This description will be shown to customers during checkout"
                :error="$errors->first('description')"
            >{{ old('description', $gateway->description) }}</x-core::textarea>

            <!-- Credentials Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2 text-sm"></i>
                    <div class="flex-1">
                        <h3 class="text-xs font-semibold text-blue-900 mb-1">API Credentials</h3>
                        <p class="text-xs text-blue-800 mb-2">
                            Credentials are stored securely in your <code class="px-1 py-0.5 bg-blue-100 rounded text-xs">.env</code> file and are not visible here.
                        </p>
                        
                        @if($gateway->gateway === 'stripe')
                            <div class="space-y-0.5 text-xs text-blue-800">
                                <p><strong>Test Mode:</strong> STRIPE_TEST_PK, STRIPE_TEST_SK</p>
                                <p><strong>Live Mode:</strong> STRIPE_LIVE_PK, STRIPE_LIVE_SK</p>
                            </div>
                        @elseif($gateway->gateway === 'paypal')
                            <div class="space-y-0.5 text-xs text-blue-800">
                                <p><strong>Sandbox:</strong> PAYPAL_SANDBOX_CLIENT_ID, PAYPAL_SANDBOX_CLIENT_SECRET</p>
                                <p><strong>Live:</strong> PAYPAL_LIVE_CLIENT_ID, PAYPAL_LIVE_CLIENT_SECRET</p>
                            </div>
                        @endif

                        @if(!$gateway->isConfigured())
                            <x-core::alert type="warning" class="mt-2">
                                <strong>Missing credentials!</strong> Add the required environment variables to your <code>.env</code> file.
                            </x-core::alert>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Enabled Checkbox -->
            <input type="hidden" name="enabled" value="{{ $gateway->enabled ? '1' : '0' }}">

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="text-xs text-gray-500">
                    Last updated: {{ $gateway->updated_at->diffForHumans() }}
                </div>
                <x-core::button type="submit" variant="primary" size="sm">
                    Save Changes
                </x-core::button>
            </div>
        </form>
    </x-core::card>
    @endforeach

    <!-- Help Section -->
    <x-core::card title="Need Help?">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-700">
            <div>
                <h4 class="font-semibold mb-2 text-gray-900">Stripe Setup</h4>
                <ol class="list-decimal list-inside space-y-1 text-gray-600">
                    <li>Create a Stripe account at <a href="https://stripe.com" target="_blank" class="text-blue-600 hover:underline">stripe.com</a></li>
                    <li>Get your API keys from the Stripe Dashboard</li>
                    <li>Add keys to your .env file</li>
                    <li>Enable Stripe and select test/live mode</li>
                </ol>
            </div>
            <div>
                <h4 class="font-semibold mb-2 text-gray-900">PayPal Setup</h4>
                <ol class="list-decimal list-inside space-y-1 text-gray-600">
                    <li>Create a PayPal Business account</li>
                    <li>Go to <a href="https://developer.paypal.com" target="_blank" class="text-blue-600 hover:underline">developer.paypal.com</a></li>
                    <li>Create an app and get your credentials</li>
                    <li>Add credentials to your .env file</li>
                </ol>
            </div>
        </div>
    </x-core::card>
</div>
@endsection
