@php
    $isEdit = isset($customer) && $customer->exists;
    $pageTitle = $isEdit ? 'Edit Customer' : 'Create Customer';
    $formAction = $isEdit ? route('admin.customers.update', $customer) : route('admin.customers.store');
@endphp

<x-app pageTitle="{{ $pageTitle }}" title="{{ $pageTitle }} - Admin" description="{{ $isEdit ? 'Edit customer information' : 'Add a new customer to your database' }}">

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Edit Customer' : 'Create New Customer' }}</h1>
                    <p class="text-gray-600 mt-1">{{ $isEdit ? 'Update customer information' : 'Add a new customer to your database' }}</p>
                </div>
                <div class="flex gap-3">
                    @if($isEdit)
                        <x-bladewind::button 
                            color="blue" 
                            outline="true"
                            onclick="window.location.href='{{ route('admin.customers.show', $customer) }}'">
                            View Customer
                        </x-bladewind::button>
                    @endif
                    <x-bladewind::button 
                        color="gray" 
                        outline="true"
                        onclick="window.location.href='{{ route('admin.customers.index') }}'">
                        Cancel
                    </x-bladewind::button>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" action="{{ $formAction }}" class="p-6 space-y-8">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                {{-- Personal Information Section --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Title --}}
                        <div>
                            <x-bladewind::select 
                                name="title"
                                label="Title"
                                placeholder="Select title"
                                selected_value="{{ old('title', $isEdit ? $customer->title : '') }}"
                                :data="[
                                    ['label' => 'Mr.', 'value' => 'Mr.'],
                                    ['label' => 'Mrs.', 'value' => 'Mrs.'],
                                    ['label' => 'Ms.', 'value' => 'Ms.'],
                                    ['label' => 'Dr.', 'value' => 'Dr.'],
                                    ['label' => 'Prof.', 'value' => 'Prof.']
                                ]" />
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- First Name --}}
                        <div>
                            <x-bladewind::input 
                                name="first_name"
                                label="First Name"
                                placeholder="Enter first name"
                                value="{{ old('first_name', $isEdit ? $customer->first_name : '') }}"
                                required="true" />
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <x-bladewind::input 
                                name="last_name"
                                label="Last Name"
                                placeholder="Enter last name"
                                value="{{ old('last_name', $isEdit ? $customer->last_name : '') }}"
                                required="true" />
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        {{-- Email --}}
                        <div>
                            <x-bladewind::input 
                                name="email"
                                type="email"
                                label="Email Address"
                                placeholder="Enter email address"
                                value="{{ old('email', $isEdit ? $customer->email : '') }}"
                                required="true" />
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        @if(!$isEdit)
                        <div>
                            <x-bladewind::input 
                                name="password"
                                type="password"
                                label="Password"
                                placeholder="Enter password"
                                required="true" />
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                        </div>
                        @else
                        <div>
                            <x-bladewind::input 
                                name="password"
                                type="password"
                                label="New Password"
                                placeholder="Leave empty to keep current password"
                                required="false" />
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current password</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Company Information Section --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Company Information
                        <span class="text-sm font-normal text-gray-500">(Optional)</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Company Name --}}
                        <div>
                            <x-bladewind::input 
                                name="company_name"
                                label="Company Name"
                                placeholder="Enter company name"
                                value="{{ old('company_name', $isEdit ? $customer->company_name : '') }}" />
                            @error('company_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tax Identifier --}}
                        <div>
                            <x-bladewind::input 
                                name="tax_identifier"
                                label="Tax Identifier"
                                placeholder="Enter tax ID/VAT number"
                                value="{{ old('tax_identifier', $isEdit ? $customer->tax_identifier : '') }}" />
                            @error('tax_identifier')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Account Settings Section --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Account Settings
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Account Reference --}}
                        <div>
                            <x-bladewind::input 
                                name="account_reference"
                                label="Account Reference"
                                placeholder="{{ $isEdit ? 'Current: ' . $customer->account_reference : 'Auto-generated if left empty' }}"
                                value="{{ old('account_reference', $isEdit ? $customer->account_reference : '') }}"
                                {{ $isEdit ? 'readonly' : '' }} />
                            @error('account_reference')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate (e.g., CUST123456)</p>
                        </div>

                        {{-- Customer Group --}}
                        <div>
                            <x-bladewind::select 
                                name="customer_group_id"
                                label="Customer Group"
                                placeholder="Select customer group"
                                selected_value="{{ old('customer_group_id', $isEdit ? $customer->customer_group_id : '') }}"
                                :data="[
                                    ['label' => 'Default', 'value' => '1'],
                                    ['label' => 'VIP', 'value' => '2'],
                                    ['label' => 'Wholesale', 'value' => '3'],
                                    ['label' => 'Retail', 'value' => '4']
                                ]" />
                            @error('customer_group_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-4">
                        @if(!$isEdit)
                        <x-bladewind::checkbox 
                            name="send_welcome_email"
                            label="Send welcome email to customer"
                            value="1"
                            checked="true" />
                        @else
                        <x-bladewind::checkbox 
                            name="send_notification_email"
                            label="Send update notification to customer"
                            value="1"
                            checked="false" />
                        @endif
                    </div>
                    
                    <div class="flex gap-3">
                        <x-bladewind::button 
                            color="gray" 
                            outline="true"
                            onclick="window.location.href='{{ route('admin.customers.index') }}'">
                            Cancel
                        </x-bladewind::button>
                        
                        <x-bladewind::button 
                            type="primary" 
                            can_submit="true">
                            @if($isEdit)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Update Customer
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Customer
                            @endif
                        </x-bladewind::button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Help Section --}}
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Customer Creation Tips</h4>
                    <ul class="text-sm text-blue-700 mt-2 space-y-1">
                        <li>• Account reference will be auto-generated if left empty</li>
                        <li>• Password must be at least 8 characters long</li>
                        <li>• Company information is optional but useful for B2B customers</li>
                        <li>• Welcome email will be sent automatically if checked</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-bladewind::notification 
            type="success"
            title="Success!"
            message="{{ session('success') }}"
            show_close_icon="true" />
    @endif

    @if(session('error'))
        <x-bladewind::notification 
            type="error"
            title="Error!"
            message="{{ session('error') }}"
            show_close_icon="true" />
    @endif

</x-app>