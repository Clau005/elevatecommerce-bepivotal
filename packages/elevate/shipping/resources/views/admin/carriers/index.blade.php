<x-app pageTitle="Shipping Carriers" title="Shipping Carriers - Admin" description="Manage your shipping carriers and configure ShipEngine integration">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Shipping Carriers</h1>
        <p class="mt-2 text-sm text-gray-600">Manage your shipping carriers and configure ShipEngine integration</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Available Carriers</h2>
            <p class="mt-1 text-sm text-gray-600">Enable carriers and configure your ShipEngine API credentials</p>
        </div>

        <div class="divide-y divide-gray-200">
            @foreach($carriers as $carrier)
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            @if($carrier->carrier_code === 'ups')
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <span class="text-xl font-bold text-yellow-800">UPS</span>
                                </div>
                            @elseif($carrier->carrier_code === 'fedex')
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="text-xl font-bold text-purple-800">FX</span>
                                </div>
                            @elseif($carrier->carrier_code === 'usps')
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span class="text-xl font-bold text-blue-800">USPS</span>
                                </div>
                            @elseif($carrier->carrier_code === 'dhl_express')
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <span class="text-xl font-bold text-red-800">DHL</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $carrier->name }}</h3>
                            <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $carrier->carrier_code)) }}</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.shipping-carriers.toggle', $carrier) }}" method="POST">
                        @csrf
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" 
                                   onchange="this.form.submit()" 
                                   {{ $carrier->is_enabled ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </form>
                </div>

                @if($carrier->is_enabled)
                <form action="{{ route('admin.shipping-carriers.update', $carrier) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-medium text-gray-900">ShipEngine Configuration</h4>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700">Test Mode</span>
                                <input type="checkbox" name="test_mode" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ $carrier->test_mode ? 'checked' : '' }}>
                            </label>
                        </div>
                        
                        @if($carrier->test_mode)
                            <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-800">Test Mode Active - Using test credentials</span>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Test Credentials -->
                        <div class="mb-4">
                            <h5 class="text-sm font-semibold text-gray-900 mb-2">Test Mode Credentials</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Test API Key</label>
                                    <input type="text" name="test_credentials[api_key]" 
                                           value="{{ $carrier->test_credentials['api_key'] ?? '' }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="TEST_...">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Test Carrier ID</label>
                                    <input type="text" name="test_credentials[carrier_id]" 
                                           value="{{ $carrier->test_credentials['carrier_id'] ?? '' }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="se_test_...">
                                </div>
                            </div>
                        </div>

                        <!-- Live Credentials -->
                        <div class="mb-4">
                            <h5 class="text-sm font-semibold text-gray-900 mb-2">Live Mode Credentials</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Live API Key</label>
                                    <input type="text" name="credentials[api_key]" 
                                           value="{{ $carrier->credentials['api_key'] ?? '' }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="API_...">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Live Carrier ID</label>
                                    <input type="text" name="credentials[carrier_id]" 
                                           value="{{ $carrier->credentials['carrier_id'] ?? '' }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="se_...">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supported Services</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($carrier->getAvailableServices() as $service)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucwords(str_replace('_', ' ', $service)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Save Configuration
                            </button>
                        </div>
                    </div>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Getting Started with ShipEngine</h3>
        <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
            <li>Sign up for a free account at <a href="https://www.shipengine.com/" target="_blank" class="underline">shipengine.com</a></li>
            <li>Get your API key from the ShipEngine dashboard</li>
            <li>Connect your carrier accounts (UPS, FedEx, etc.) in ShipEngine</li>
            <li>Copy your carrier IDs from ShipEngine and paste them above</li>
            <li>Enable test mode to use sandbox credentials for testing</li>
        </ol>
    </div>
</div>
</x-app>
