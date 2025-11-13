<x-app pageTitle="Checkout" title="Checkout - Admin" description="Configure checkout form fields and options">

    <div class="max-w-6xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
                <p class="text-gray-600 mt-1">Customize checkout form fields, marketing options, and checkout rules</p>
            </div>
            <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Settings
            </a>
        </div>

        <form method="POST" action="{{ route('admin.settings.checkout.update') }}" class="space-y-6">
            @csrf

            {{-- Guest Checkout --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Guest Checkout</h2>
                    <p class="text-sm text-gray-600 mt-1">Control whether customers need to create an account to purchase</p>
                </div>

                <div class="p-6">
                    <label class="flex items-start">
                        <input type="checkbox" name="allow_guest_checkout" value="1" 
                               {{ config('commerce.checkout.allow_guest_checkout', true) ? 'checked' : '' }}
                               class="mt-1 mr-3">
                        <div>
                            <div class="font-medium text-gray-900">Allow guest checkout</div>
                            <div class="text-sm text-gray-600">Let customers complete purchases without creating an account. They can optionally create an account after checkout.</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Customer Information</h2>
                    <p class="text-sm text-gray-600 mt-1">Configure which fields to collect at checkout</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Full Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Full name</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="name_format" value="last_only" class="mr-3">
                                <span class="text-sm text-gray-700">Only require last name</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="name_format" value="first_and_last" checked class="mr-3">
                                <span class="text-sm text-gray-700">Require first and last name</span>
                            </label>
                        </div>
                    </div>

                    {{-- Company Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Company name</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="company_name" value="hidden" class="mr-3">
                                <span class="text-sm text-gray-700">Don't include</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="company_name" value="optional" checked class="mr-3">
                                <span class="text-sm text-gray-700">Optional</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="company_name" value="required" class="mr-3">
                                <span class="text-sm text-gray-700">Required</span>
                            </label>
                        </div>
                    </div>

                    {{-- Address Line 2 --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Address line 2 (apartment, unit, etc.)</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="address_line_2" value="hidden" class="mr-3">
                                <span class="text-sm text-gray-700">Don't include</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="address_line_2" value="optional" checked class="mr-3">
                                <span class="text-sm text-gray-700">Optional</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="address_line_2" value="required" class="mr-3">
                                <span class="text-sm text-gray-700">Required</span>
                            </label>
                        </div>
                    </div>

                    {{-- Phone Number --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Shipping address phone number</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="phone_number" value="hidden" class="mr-3">
                                <span class="text-sm text-gray-700">Don't include</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="phone_number" value="optional" class="mr-3">
                                <span class="text-sm text-gray-700">Optional</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="phone_number" value="required" checked class="mr-3">
                                <span class="text-sm text-gray-700">Required</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Marketing Options --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Marketing Options</h2>
                    <p class="text-sm text-gray-600 mt-1">Display checkboxes for customers to sign up for marketing</p>
                </div>

                <div class="p-6 space-y-4">
                    <label class="flex items-start">
                        <input type="checkbox" name="marketing_email" value="1" checked class="mt-1 mr-3">
                        <div>
                            <div class="font-medium text-gray-900">Email</div>
                            <div class="text-sm text-gray-600">Display a checkbox for customers to sign up for email marketing</div>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="marketing_sms" value="1" class="mt-1 mr-3">
                        <div>
                            <div class="font-medium text-gray-900">SMS</div>
                            <div class="text-sm text-gray-600">Display a checkbox for customers to sign up for SMS marketing</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Checkout Rules --}}
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Checkout Rules</h2>
                            <p class="text-sm text-gray-600 mt-1">Manage discounts and rules applied at checkout</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.settings.checkout-rules.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                View All
                            </a>
                            <a href="{{ route('admin.settings.checkout-rules.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Rule
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if($recentRules->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentRules as $rule)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-gray-300">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h3 class="font-medium text-gray-900">{{ $rule->name }}</h3>
                                            @if($rule->is_active)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $rule->description }}</p>
                                    </div>
                                    <a href="{{ route('admin.settings.checkout-rules.edit', $rule->id) }}" class="ml-4 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        Edit
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No checkout rules</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new checkout rule.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.settings.checkout-rules.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Checkout Rule
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Save Button --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

</x-app>
