<x-app pageTitle="{{ isset($checkoutRule) ? 'Edit Checkout Rule' : 'Create Checkout Rule' }}" 
       title="{{ isset($checkoutRule) ? 'Edit Checkout Rule' : 'Create Checkout Rule' }} - Settings" 
       description="{{ isset($checkoutRule) ? 'Edit checkout rule settings' : 'Create a new checkout rule' }}">

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ isset($checkoutRule) ? 'Edit Checkout Rule' : 'Create Checkout Rule' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isset($checkoutRule) ? 'Update checkout rule settings' : 'Configure automatic discounts and checkout conditions' }}
                    </p>
                </div>
                <a href="{{ route('admin.settings.checkout-rules.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Checkout Rules
                </a>
            </div>
        </div>

        <form method="POST" action="{{ isset($checkoutRule) ? route('admin.settings.checkout-rules.update', $checkoutRule->id) : route('admin.settings.checkout-rules.store') }}" class="space-y-8">
            @csrf
            @if(isset($checkoutRule))
                @method('PUT')
            @endif

            {{-- Basic Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rule Name <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $checkoutRule->name ?? '') }}" 
                                   placeholder="e.g., Free Shipping Over £50"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Handle (Optional)</label>
                            <input type="text" 
                                   name="handle" 
                                   value="{{ old('handle', $checkoutRule->handle ?? '') }}" 
                                   placeholder="Auto-generated from name"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" 
                                  rows="3" 
                                  placeholder="Describe what this rule does..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $checkoutRule->description ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rule Type <span class="text-red-500">*</span></label>
                            <select name="type" 
                                    required
                                    onchange="updateFormFields(this.value)"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select a type</option>
                                @foreach($types as $key => $type)
                                    <option value="{{ $key }}" {{ old('type', $checkoutRule->type ?? '') == $key ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <input type="number" 
                                   name="priority" 
                                   value="{{ old('priority', $checkoutRule->priority ?? 0) }}" 
                                   placeholder="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Higher numbers = higher priority</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conditions --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Conditions</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div id="threshold-amount-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Threshold Amount (£)</label>
                            <input type="number" 
                                   name="threshold_amount" 
                                   step="0.01"
                                   value="{{ old('threshold_amount', $checkoutRule->threshold_amount ?? '') }}" 
                                   placeholder="0.00"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div id="threshold-quantity-field" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Threshold Quantity</label>
                            <input type="number" 
                                   name="threshold_quantity" 
                                   value="{{ old('threshold_quantity', $checkoutRule->threshold_quantity ?? '') }}" 
                                   placeholder="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Groups (Optional)</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($customerGroups as $group)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="customer_groups[]" 
                                           value="{{ $group->id }}"
                                           {{ in_array($group->id, old('customer_groups', $checkoutRule->customer_groups ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ $group->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Action Type <span class="text-red-500">*</span></label>
                            <select name="action_type" 
                                    required
                                    onchange="updateActionFields(this.value)"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select an action</option>
                                @foreach($actionTypes as $key => $actionType)
                                    <option value="{{ $key }}" {{ old('action_type', $checkoutRule->action_type ?? '') == $key ? 'selected' : '' }}>
                                        {{ $actionType['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="action-value-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Action Value</label>
                            <input type="number" 
                                   name="action_value" 
                                   step="0.01"
                                   value="{{ old('action_value', $checkoutRule->action_value ?? '') }}" 
                                   placeholder="0.00"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1" id="action-value-help">Enter percentage (without %) or fixed amount (£)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Settings</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date (Optional)</label>
                            <input type="date" 
                                   name="starts_at" 
                                   value="{{ old('starts_at', $checkoutRule->starts_at ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date (Optional)</label>
                            <input type="date" 
                                   name="expires_at" 
                                   value="{{ old('expires_at', $checkoutRule->expires_at ?? '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Usage Limit (Optional)</label>
                            <input type="number" 
                                   name="usage_limit" 
                                   value="{{ old('usage_limit', $checkoutRule->usage_limit ?? '') }}" 
                                   placeholder="Unlimited"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="flex items-center">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $checkoutRule->is_active ?? true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.settings.checkout-rules.index') }}" 
                   class="inline-flex items-center px-6 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    {{ isset($checkoutRule) ? 'Update Rule' : 'Create Rule' }}
                </button>
            </div>
        </form>
    </div>

    {{-- JavaScript for dynamic form behavior --}}
    <script>
        function updateFormFields(type) {
            const thresholdAmount = document.getElementById('threshold-amount-field');
            const thresholdQuantity = document.getElementById('threshold-quantity-field');
            
            // Hide all fields first
            thresholdAmount.style.display = 'none';
            thresholdQuantity.style.display = 'none';
            
            // Show relevant fields based on type
            switch(type) {
                case 'free_shipping_threshold':
                case 'minimum_order_amount':
                case 'bulk_discount':
                    thresholdAmount.style.display = 'block';
                    break;
                case 'quantity_discount':
                    thresholdQuantity.style.display = 'block';
                    break;
            }
        }

        function updateActionFields(actionType) {
            const actionValueField = document.getElementById('action-value-field');
            const actionValueHelp = document.getElementById('action-value-help');
            
            switch(actionType) {
                case 'free_shipping':
                    actionValueField.style.display = 'none';
                    break;
                case 'percentage_discount':
                    actionValueField.style.display = 'block';
                    actionValueHelp.textContent = 'Enter percentage (e.g., 10 for 10%)';
                    break;
                case 'fixed_discount':
                    actionValueField.style.display = 'block';
                    actionValueHelp.textContent = 'Enter fixed amount in pounds (e.g., 5.00)';
                    break;
                default:
                    actionValueField.style.display = 'block';
                    actionValueHelp.textContent = 'Enter value for this action';
            }
        }

        // Initialize form on page load
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.querySelector('select[name="type"]');
            const actionTypeSelect = document.querySelector('select[name="action_type"]');
            
            if (typeSelect && typeSelect.value) {
                updateFormFields(typeSelect.value);
            }
            
            if (actionTypeSelect && actionTypeSelect.value) {
                updateActionFields(actionTypeSelect.value);
            }
        });
    </script>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg" role="alert">
            <strong class="font-bold">Validation Error!</strong>
            <span class="block sm:inline">Please check the form for errors.</span>
        </div>
    @endif

</x-app>
