<x-app pageTitle="Payment Gateways" title="Payment Gateways - Admin" description="Manage payment gateways">
<div class="container mx-auto px-4 py-6 max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Payment Gateways</h1>
        <p class="text-gray-600 mt-1">Configure payment methods for your store</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @foreach($gateways as $gateway)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            @if($gateway->driver === 'stripe')
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#635BFF"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>
                            @elseif($gateway->driver === 'paypal')
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#00457C"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/></svg>
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $gateway->name }}</h3>
                            <p class="text-sm text-gray-500">{{ ucfirst($gateway->driver) }} payment gateway</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.payment-gateways.toggle', $gateway) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" 
                                   onchange="this.form.submit()" 
                                   {{ $gateway->is_enabled ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </form>
                </div>

                @if($gateway->is_enabled)
                <form action="{{ route('admin.payment-gateways.update', $gateway) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-medium text-gray-900">Credentials</h4>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700">Test Mode</span>
                                <input type="checkbox" name="test_mode" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ $gateway->test_mode ? 'checked' : '' }}>
                            </label>
                        </div>
                        
                        @if($gateway->test_mode)
                            <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-800">Test Mode Active - No real charges will be made</span>
                                </div>
                            </div>
                        @endif
                        
                        @if($gateway->driver === 'stripe')
                            <!-- Test Credentials -->
                            <div class="mb-4">
                                <h5 class="text-sm font-semibold text-gray-900 mb-2">Test Mode Credentials</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Test Publishable Key</label>
                                        <input type="text" name="test_credentials[publishable_key]" 
                                               value="{{ $gateway->test_credentials['publishable_key'] ?? '' }}"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="pk_test_...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Test Secret Key</label>
                                        <input type="password" name="test_credentials[secret_key]" 
                                               value="{{ $gateway->test_credentials['secret_key'] ?? '' }}"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="sk_test_...">
                                    </div>
                                </div>
                            </div>

                            <!-- Live Credentials -->
                            <div class="mb-4">
                                <h5 class="text-sm font-semibold text-gray-900 mb-2">Live Mode Credentials</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Live Publishable Key</label>
                                        <input type="text" name="credentials[publishable_key]" 
                                               value="{{ $gateway->credentials['publishable_key'] ?? '' }}"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="pk_live_...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Live Secret Key</label>
                                        <input type="password" name="credentials[secret_key]" 
                                               value="{{ $gateway->credentials['secret_key'] ?? '' }}"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="sk_live_...">
                                    </div>
                                </div>
                            </div>
                        @elseif($gateway->driver === 'paypal')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                                    <input type="text" name="credentials[client_id]" 
                                           value="{{ $gateway->credentials['client_id'] ?? '' }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Secret</label>
                                    <input type="password" name="credentials[secret]" 
                                           value="{{ $gateway->credentials['secret'] ?? '' }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supported Payment Methods</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($gateway->getAvailablePaymentMethods() as $method)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $method)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Save Changes
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
</x-app>
