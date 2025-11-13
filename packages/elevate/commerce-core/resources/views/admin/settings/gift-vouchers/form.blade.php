@php
    $isEdit = isset($giftVoucher) && $giftVoucher->exists;
    $pageTitle = $isEdit ? 'Edit Gift Voucher' : 'Create Gift Voucher';
    $formAction = $isEdit ? route('admin.settings.gift-vouchers.update', $giftVoucher) : route('admin.settings.gift-vouchers.store');
@endphp

<x-app pageTitle="{{ $pageTitle }}" title="{{ $pageTitle }} - Admin" description="{{ $isEdit ? 'Edit gift voucher information' : 'Add a new gift voucher to your catalog' }}">

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Edit Gift Voucher' : 'Create New Gift Voucher' }}</h1>
                    <p class="text-gray-600 mt-1">{{ $isEdit ? 'Update gift voucher information' : 'Add a new gift voucher to your catalog' }}</p>
                </div>
                <a href="{{ route('admin.settings.gift-vouchers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Gift Vouchers
                </a>
            </div>
        </div>

        {{-- Form Card --}}
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            {{-- Basic Information Section --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Basic Information</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Voucher Code --}}
                    <div>
                        <x-bladewind::input
                            name="code"
                            label="Voucher Code"
                            placeholder="Leave empty to auto-generate"
                            value="{{ old('code', isset($giftVoucher) ? $giftVoucher->code : '') }}"
                            error_message="{{ $errors->first('code') }}" />
                        <p class="text-xs text-gray-500 mt-1">Unique code customers will use. Auto-generated if left empty.</p>
                    </div>

                    {{-- Value --}}
                    <div>
                        <x-bladewind::input
                            name="value"
                            label="Value (£)"
                            placeholder="50.00"
                            prefix="currency-pound"
                            prefix_is_icon="true"
                            value="{{ old('value', isset($giftVoucher) ? number_format($giftVoucher->value / 100, 2, '.', '') : '') }}"
                            required="true"
                            error_message="{{ $errors->first('value') }}" />
                    </div>

                    {{-- Title --}}
                    <div class="lg:col-span-2">
                        <x-bladewind::input
                            name="title"
                            label="Title"
                            placeholder="e.g., £50 Gift Voucher"
                            value="{{ old('title', isset($giftVoucher) ? $giftVoucher->title : '') }}"
                            required="true"
                            error_message="{{ $errors->first('title') }}" />
                    </div>

                    {{-- Description --}}
                    <div class="lg:col-span-2">
                        <x-bladewind::textarea
                            name="description"
                            label="Description"
                            placeholder="Optional description for the gift voucher"
                            rows="3"
                            selected_value="{{ old('description', isset($giftVoucher) ? $giftVoucher->description : '') }}"
                            error_message="{{ $errors->first('description') }}" />
                    </div>

                </div>
            </div>

            {{-- Gift Voucher Image Section --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Gift Voucher Image</h3>
                
                {{-- Current Image (Edit Mode) --}}
                @if($isEdit && isset($giftVoucher) && $giftVoucher->image_url)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-4">Current Image</h4>
                        <div class="relative inline-block">
                            <img src="{{ $giftVoucher->image_url }}" 
                                 alt="Gift voucher image" 
                                 class="w-48 h-32 object-cover rounded-lg border border-gray-200">
                            <div class="absolute top-2 right-2">
                                <x-bladewind::button
                                    color="red"
                                    size="tiny"
                                    icon="trash"
                                    onclick="removeCurrentImage()"
                                    class="text-xs">
                                    Remove
                                </x-bladewind::button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Image Upload --}}
                <div>
                    <x-bladewind::filepicker
                        name="voucher_image"
                        max_files="1"
                        multiple="false"
                        max_file_size="5mb"
                        accepted_file_types="image/*"
                        placeholder_line1="Choose gift voucher image or drag and drop"
                        placeholder_line2="Single image, 5MB max"
                        show_image_preview="true"
                        can_drop="true"
                        can_browse="true"
                        validate_file_size="true"
                        auto_upload="false"
                        :selected_value="[]" />
                    <p class="text-sm text-gray-500 mt-2">
                        Upload an image for the gift voucher. Recommended size: 400x300px or similar aspect ratio.
                        Supported formats: JPG, PNG, GIF, WebP. Max size: 5MB.
                    </p>
                </div>

                {{-- Alternative: Image URL Input --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-4">Or provide an image URL</h4>
                    <x-bladewind::input
                        name="image_url"
                        label="Image URL"
                        placeholder="https://example.com/voucher-image.jpg"
                        value="{{ old('image_url', isset($giftVoucher) ? $giftVoucher->image_url : '') }}"
                        error_message="{{ $errors->first('image_url') }}" />
                    <p class="text-xs text-gray-500 mt-1">If you prefer to use an external image URL instead of uploading</p>
                </div>

                {{-- Hidden input for image removal --}}
                <input type="hidden" name="remove_current_image" id="remove_current_image" value="0">
            </div>

            {{-- Validity Settings Section --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Validity Settings</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Valid From --}}
                    <div>
                        <x-bladewind::input
                            name="valid_from"
                            label="Valid From"
                            type="datetime-local"
                            value="{{ old('valid_from', isset($giftVoucher) && $giftVoucher->valid_from ? $giftVoucher->valid_from->format('Y-m-d\TH:i') : '') }}"
                            error_message="{{ $errors->first('valid_from') }}" />
                        <p class="text-xs text-gray-500 mt-1">Leave empty for immediate validity</p>
                    </div>

                    {{-- Valid Until --}}
                    <div>
                        <x-bladewind::input
                            name="valid_until"
                            label="Valid Until"
                            type="datetime-local"
                            value="{{ old('valid_until', isset($giftVoucher) && $giftVoucher->valid_until ? $giftVoucher->valid_until->format('Y-m-d\TH:i') : '') }}"
                            error_message="{{ $errors->first('valid_until') }}" />
                        <p class="text-xs text-gray-500 mt-1">Leave empty for no expiry</p>
                    </div>
                </div>
            </div>

            {{-- Usage Limits Section --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Usage Limits</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Total Usage Limit --}}
                    <div>
                        <x-bladewind::input
                            name="usage_limit"
                            label="Total Usage Limit"
                            placeholder="Unlimited"
                            numeric="true"
                            value="{{ old('usage_limit', isset($giftVoucher) ? $giftVoucher->usage_limit : '') }}"
                            error_message="{{ $errors->first('usage_limit') }}" />
                        <p class="text-xs text-gray-500 mt-1">Maximum number of times this voucher can be used</p>
                    </div>

                    {{-- Per Customer Limit --}}
                    <div>
                        <x-bladewind::input
                            name="per_customer_limit"
                            label="Per Customer Limit"
                            placeholder="Unlimited"
                            numeric="true"
                            value="{{ old('per_customer_limit', isset($giftVoucher) ? $giftVoucher->per_customer_limit : '') }}"
                            error_message="{{ $errors->first('per_customer_limit') }}" />
                        <p class="text-xs text-gray-500 mt-1">Maximum uses per customer</p>
                    </div>
                </div>
            </div>

            {{-- SEO Settings Section --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">SEO Settings</h3>
                
                <div class="space-y-6">
                    {{-- Meta Title --}}
                    <div>
                        <x-bladewind::input
                            name="meta_title"
                            label="Meta Title"
                            placeholder="SEO title for search engines"
                            value="{{ old('meta_title', isset($giftVoucher) ? $giftVoucher->meta_title : '') }}"
                            error_message="{{ $errors->first('meta_title') }}" />
                    </div>

                    {{-- Meta Description --}}
                    <div>
                        <x-bladewind::textarea
                            name="meta_description"
                            label="Meta Description"
                            placeholder="SEO description for search engines"
                            rows="3"
                            max_length="500"
                            selected_value="{{ old('meta_description', isset($giftVoucher) ? $giftVoucher->meta_description : '') }}"
                            show_character_count="true"
                            error_message="{{ $errors->first('meta_description') }}" />
                    </div>

                    {{-- Meta Keywords --}}
                    <div>
                        <x-bladewind::input
                            name="meta_keywords"
                            label="Meta Keywords"
                            placeholder="gift voucher, gift card, present"
                            value="{{ old('meta_keywords', isset($giftVoucher) && $giftVoucher->meta_keywords ? implode(', ', $giftVoucher->meta_keywords) : '') }}"
                            error_message="{{ $errors->first('meta_keywords') }}" />
                        <p class="text-xs text-gray-500 mt-1">Comma-separated keywords</p>
                    </div>
                </div>
            </div>

            {{-- Status & Settings Section --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Status & Settings</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Status --}}
                    <div>
                        @php
                            $currentStatus = old('status', isset($giftVoucher) ? $giftVoucher->status : 'active');
                            $statusOptions = [
                                ['label' => 'Active', 'value' => 'active', 'selected' => $currentStatus === 'active'],
                                ['label' => 'Inactive', 'value' => 'inactive', 'selected' => $currentStatus === 'inactive'],
                                ['label' => 'Expired', 'value' => 'expired', 'selected' => $currentStatus === 'expired']
                            ];
                        @endphp
                        <x-bladewind::select
                            name="status"
                            label="Status"
                            selected_value="{{ old('status', isset($giftVoucher) ? $giftVoucher->status : 'active') }}"
                            :data="$statusOptions"
                            required="true" />
                    </div>

                    {{-- Sort Order --}}
                    <div>
                        <x-bladewind::input
                            name="sort_order"
                            label="Sort Order"
                            numeric="true"
                            value="{{ old('sort_order', isset($giftVoucher) ? $giftVoucher->sort_order : 0) }}"
                            error_message="{{ $errors->first('sort_order') }}" />
                    </div>

                    {{-- Featured --}}
                    <div class="lg:col-span-2">
                        <x-bladewind::checkbox
                            name="is_featured"
                            label="Featured Gift Voucher"
                            value="1"
                            checked="{{ old('is_featured', isset($giftVoucher) ? $giftVoucher->is_featured : false) ? 'true' : 'false' }}" />
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-4">
                        @if(!$isEdit)
                        <x-bladewind::checkbox 
                            name="create_another"
                            label="Create another gift voucher after saving"
                            value="1"
                            checked="false" />
                        @endif
                    </div>
                    
                    <div class="flex gap-3">
                        <x-bladewind::button 
                            color="gray" 
                            outline="true"
                            onclick="window.location.href='{{ route('admin.settings.show', 'gift-vouchers') }}'">
                            Cancel
                        </x-bladewind::button>
                        
                        <x-bladewind::button 
                            type="primary" 
                            can_submit="true"
                            icon="{{ $isEdit ? 'pencil' : 'plus' }}"
                            icon_left="true">
                            {{ $isEdit ? 'Update Gift Voucher' : 'Create Gift Voucher' }}
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Help Section --}}
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Gift Voucher Creation Tips</h4>
                    <ul class="text-sm text-blue-700 mt-2 space-y-1">
                        <li>• Voucher code will be auto-generated if left empty</li>
                        <li>• Value should be entered in pounds (e.g., 50.00 for £50)</li>
                        <li>• Set validity dates to control when vouchers can be used</li>
                        <li>• Usage limits help prevent abuse and track redemptions</li>
                        <li>• Featured vouchers appear prominently in listings</li>
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

    <script>
    // Auto-generate code from title if code is empty
    document.addEventListener('DOMContentLoaded', function() {
        const titleField = document.querySelector('input[name="title"]');
        const codeField = document.querySelector('input[name="code"]');
        
        if (titleField && codeField) {
            titleField.addEventListener('input', function() {
                if (!codeField.value) {
                    const title = this.value;
                    const code = title.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 8);
                    if (code.length >= 4) {
                        codeField.value = code;
                    }
                }
            });
        }
    });

    // Generate random code button functionality
    function generateRandomCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const codeField = document.querySelector('input[name="code"]');
        if (codeField) {
            codeField.value = code;
        }
    }

    // Remove current image functionality
    function removeCurrentImage() {
        if (confirm('Are you sure you want to remove the current image?')) {
            // Set the hidden input to indicate image removal
            document.getElementById('remove_current_image').value = '1';
            
            // Hide the current image display
            const currentImageDiv = document.querySelector('.mb-6');
            if (currentImageDiv) {
                currentImageDiv.style.display = 'none';
            }
            
            // Show success message
            showNotification('Current image will be removed when you save the form', 'info');
        }
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg font-bold">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }
    </script>

</x-app>