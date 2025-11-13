<x-app pageTitle="{{ $discount ? 'Edit Discount' : 'Create Discount' }}" title="{{ $discount ? 'Edit Discount' : 'Create Discount' }} - Admin">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $discount ? 'Edit Discount' : 'Create New Discount' }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $discount ? 'Update discount details and rules' : 'Set up a new promotional discount' }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.settings.show', 'discounts') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Discounts
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ $discount ? route('admin.settings.discounts.update', $discount->id) : route('admin.settings.discounts.store') }}" class="p-6">
                @csrf
                @if($discount)
                    @method('PUT')
                @endif

                <div class="space-y-8">
                    {{-- Basic Information --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Discount Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Discount Name *</label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $discount->name ?? '') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-300 @enderror"
                                       placeholder="e.g., Summer Sale 2024"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Handle --}}
                            <div>
                                <label for="handle" class="block text-sm font-medium text-gray-700 mb-2">Handle</label>
                                <input type="text" 
                                       id="handle" 
                                       name="handle" 
                                       value="{{ old('handle', $discount->handle ?? '') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('handle') border-red-300 @enderror"
                                       placeholder="Auto-generated from name">
                                <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name</p>
                                @error('handle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="3"
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-300 @enderror"
                                          placeholder="Describe this discount offer...">{{ old('description', $discount->description ?? '') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Discount Configuration --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Discount Configuration</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Discount Type --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                                <select id="type" 
                                        name="type" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-300 @enderror"
                                        required onchange="toggleDiscountFields()">
                                    <option value="">Select discount type</option>
                                    <option value="percentage" {{ old('type', $discount->type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage Off</option>
                                    <option value="fixed_amount" {{ old('type', $discount->type ?? '') === 'fixed_amount' ? 'selected' : '' }}>Fixed Amount Off</option>
                                    <option value="free_shipping" {{ old('type', $discount->type ?? '') === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                                    <option value="buy_x_get_y" {{ old('type', $discount->type ?? '') === 'buy_x_get_y' ? 'selected' : '' }}>Buy X Get Y</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Discount Value --}}
                            <div id="value-field">
                                <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span id="value-label">Discount Value *</span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           id="value" 
                                           name="value" 
                                           value="{{ old('value', $discount ? $discount->display_value : '') }}"
                                           step="0.01"
                                           min="0"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('value') border-red-300 @enderror"
                                           placeholder="0.00">
                                    <div id="value-suffix" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm"></span>
                                    </div>
                                </div>
                                @error('value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Maximum Discount Amount --}}
                            <div id="max-discount-field" style="display: none;">
                                <label for="maximum_discount_amount" class="block text-sm font-medium text-gray-700 mb-2">Maximum Discount Amount</label>
                                <input type="number" 
                                       id="maximum_discount_amount" 
                                       name="maximum_discount_amount" 
                                       value="{{ old('maximum_discount_amount', $discount && $discount->maximum_discount_amount ? number_format($discount->maximum_discount_amount / 100, 2, '.', '') : '') }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('maximum_discount_amount') border-red-300 @enderror"
                                       placeholder="0.00">
                                <p class="mt-1 text-xs text-gray-500">Leave empty for no limit</p>
                                @error('maximum_discount_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Minimum Order Amount --}}
                            <div>
                                <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700 mb-2">Minimum Order Amount</label>
                                <input type="number" 
                                       id="minimum_order_amount" 
                                       name="minimum_order_amount" 
                                       value="{{ old('minimum_order_amount', $discount && $discount->minimum_order_amount ? number_format($discount->minimum_order_amount / 100, 2, '.', '') : '') }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('minimum_order_amount') border-red-300 @enderror"
                                       placeholder="0.00">
                                <p class="mt-1 text-xs text-gray-500">Leave empty for no minimum</p>
                                @error('minimum_order_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Application Method --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Application Method</h3>
                        <div class="space-y-4">
                            {{-- Automatic vs Coupon --}}
                            <div>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="is_automatic" 
                                               value="1" 
                                               {{ old('is_automatic', $discount->is_automatic ?? false) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                               onchange="toggleCouponField()">
                                        <span class="ml-2 text-sm text-gray-700">Automatic discount</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="is_automatic" 
                                               value="0" 
                                               {{ !old('is_automatic', $discount->is_automatic ?? false) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                               onchange="toggleCouponField()">
                                        <span class="ml-2 text-sm text-gray-700">Coupon code required</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Coupon Code --}}
                            <div id="coupon-field" style="{{ old('is_automatic', $discount->is_automatic ?? false) ? 'display: none;' : '' }}">
                                <label for="coupon_code" class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label>
                                <div class="flex space-x-2">
                                    <input type="text" 
                                           id="coupon_code" 
                                           name="coupon_code" 
                                           value="{{ old('coupon_code', $discount->coupon_code ?? '') }}"
                                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('coupon_code') border-red-300 @enderror"
                                           placeholder="e.g., SUMMER2024">
                                    <button type="button" onclick="generateCouponCode()" 
                                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">
                                        Generate
                                    </button>
                                </div>
                                @error('coupon_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Usage Limits --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Usage Limits</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Total Usage Limit --}}
                            <div>
                                <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">Total Usage Limit</label>
                                <input type="number" 
                                       id="usage_limit" 
                                       name="usage_limit" 
                                       value="{{ old('usage_limit', $discount->usage_limit ?? '') }}"
                                       min="1"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('usage_limit') border-red-300 @enderror"
                                       placeholder="Unlimited">
                                <p class="mt-1 text-xs text-gray-500">Leave empty for unlimited usage</p>
                                @error('usage_limit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Per Customer Usage Limit --}}
                            <div>
                                <label for="usage_limit_per_customer" class="block text-sm font-medium text-gray-700 mb-2">Usage Limit Per Customer</label>
                                <input type="number" 
                                       id="usage_limit_per_customer" 
                                       name="usage_limit_per_customer" 
                                       value="{{ old('usage_limit_per_customer', $discount->usage_limit_per_customer ?? '') }}"
                                       min="1"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('usage_limit_per_customer') border-red-300 @enderror"
                                       placeholder="Unlimited">
                                <p class="mt-1 text-xs text-gray-500">Leave empty for unlimited per customer</p>
                                @error('usage_limit_per_customer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Active Dates --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Active Dates</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Start Date --}}
                            <div>
                                <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time</label>
                                <input type="datetime-local" 
                                       id="starts_at" 
                                       name="starts_at" 
                                       value="{{ old('starts_at', $discount && $discount->starts_at ? $discount->starts_at->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('starts_at') border-red-300 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Leave empty to start immediately</p>
                                @error('starts_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- End Date --}}
                            <div>
                                <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">End Date & Time</label>
                                <input type="datetime-local" 
                                       id="expires_at" 
                                       name="expires_at" 
                                       value="{{ old('expires_at', $discount && $discount->expires_at ? $discount->expires_at->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('expires_at') border-red-300 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Leave empty for no expiration</p>
                                @error('expires_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Additional Settings --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Settings</h3>
                        <div class="space-y-4">
                            {{-- Priority --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                    <input type="number" 
                                           id="priority" 
                                           name="priority" 
                                           value="{{ old('priority', $discount->priority ?? 0) }}"
                                           min="0"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-300 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Higher numbers = higher priority (0 = lowest)</p>
                                    @error('priority')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Checkboxes --}}
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $discount->is_active ?? true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="combine_with_other_discounts" 
                                           value="1" 
                                           {{ old('combine_with_other_discounts', $discount->combine_with_other_discounts ?? false) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Can be combined with other discounts</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 mt-8">
                    <a href="{{ route('admin.settings.show', 'discounts') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <div class="flex space-x-3">
                        @if($discount)
                            <button type="submit" name="action" value="save" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                Update Discount
                            </button>
                        @else
                            <button type="submit" name="action" value="save" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                Create Discount
                            </button>
                            <button type="submit" name="action" value="save_and_add" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                                Save & Add Another
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleDiscountFields() {
            const type = document.getElementById('type').value;
            const valueField = document.getElementById('value-field');
            const valueLabel = document.getElementById('value-label');
            const valueSuffix = document.getElementById('value-suffix').querySelector('span');
            const maxDiscountField = document.getElementById('max-discount-field');
            const valueInput = document.getElementById('value');

            switch(type) {
                case 'percentage':
                    valueField.style.display = 'block';
                    valueLabel.textContent = 'Percentage Off *';
                    valueSuffix.textContent = '%';
                    valueInput.max = '100';
                    maxDiscountField.style.display = 'block';
                    break;
                case 'fixed_amount':
                    valueField.style.display = 'block';
                    valueLabel.textContent = 'Amount Off *';
                    valueSuffix.textContent = '£';
                    valueInput.removeAttribute('max');
                    maxDiscountField.style.display = 'none';
                    break;
                case 'free_shipping':
                    valueField.style.display = 'none';
                    maxDiscountField.style.display = 'none';
                    break;
                case 'buy_x_get_y':
                    valueField.style.display = 'none';
                    maxDiscountField.style.display = 'none';
                    break;
                default:
                    valueField.style.display = 'block';
                    valueLabel.textContent = 'Discount Value *';
                    valueSuffix.textContent = '';
                    valueInput.removeAttribute('max');
                    maxDiscountField.style.display = 'none';
            }
        }

        function toggleCouponField() {
            const isAutomatic = document.querySelector('input[name="is_automatic"]:checked').value === '1';
            const couponField = document.getElementById('coupon-field');
            couponField.style.display = isAutomatic ? 'none' : 'block';
        }

        function generateCouponCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 8; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('coupon_code').value = result;
        }

        // Initialize form state
        document.addEventListener('DOMContentLoaded', function() {
            toggleDiscountFields();
            toggleCouponField();
        });
    </script>
</x-app>