<x-app pageTitle="Create Product" title="Create Product - Admin">

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Product</h1>
            <p class="text-gray-600 mt-1">Add a new product to your catalog</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Products
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    
                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                                Slug
                            </label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('slug') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from name</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Short Description --}}
                        <div>
                            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">
                                Short Description
                            </label>
                            <textarea name="short_description" id="short_description" rows="2"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('short_description') border-red-300 @enderror">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="6"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Price --}}
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">£</span>
                                <input type="number" name="price" id="price" value="{{ old('price', '0.00') }}" step="0.01" min="0" required
                                    class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('price') border-red-300 @enderror">
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Compare At Price --}}
                        <div>
                            <label for="compare_at_price" class="block text-sm font-medium text-gray-700 mb-1">
                                Compare At Price
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">£</span>
                                <input type="number" name="compare_at_price" id="compare_at_price" value="{{ old('compare_at_price') }}" step="0.01" min="0"
                                    class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('compare_at_price') border-red-300 @enderror">
                            </div>
                            @error('compare_at_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Cost Per Item --}}
                        <div>
                            <label for="cost_per_item" class="block text-sm font-medium text-gray-700 mb-1">
                                Cost Per Item
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">£</span>
                                <input type="number" name="cost_per_item" id="cost_per_item" value="{{ old('cost_per_item') }}" step="0.01" min="0"
                                    class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('cost_per_item') border-red-300 @enderror">
                            </div>
                            @error('cost_per_item')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Inventory --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Inventory</h2>
                    
                    <div class="space-y-4">
                        {{-- SKU --}}
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                                SKU
                            </label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('sku') border-red-300 @enderror">
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Track Inventory --}}
                        <div class="flex items-center">
                            <input type="checkbox" name="track_inventory" id="track_inventory" value="1" {{ old('track_inventory') ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="track_inventory" class="ml-2 block text-sm text-gray-700">
                                Track inventory for this product
                            </label>
                        </div>

                        {{-- Stock --}}
                        <div id="stock-field" style="display: {{ old('track_inventory') ? 'block' : 'none' }};">
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">
                                Stock Quantity
                            </label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('stock') border-red-300 @enderror">
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Shipping --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping</h2>
                    
                    <div class="space-y-4">
                        {{-- Requires Shipping --}}
                        <div class="flex items-center">
                            <input type="checkbox" name="requires_shipping" id="requires_shipping" value="1" {{ old('requires_shipping', true) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="requires_shipping" class="ml-2 block text-sm text-gray-700">
                                This product requires shipping
                            </label>
                        </div>

                        {{-- Weight --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">
                                    Weight
                                </label>
                                <input type="number" name="weight" id="weight" value="{{ old('weight') }}" step="0.01" min="0"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('weight') border-red-300 @enderror">
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="weight_unit" class="block text-sm font-medium text-gray-700 mb-1">
                                    Unit
                                </label>
                                <select name="weight_unit" id="weight_unit"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="kg" {{ old('weight_unit', 'kg') === 'kg' ? 'selected' : '' }}>kg</option>
                                    <option value="g" {{ old('weight_unit') === 'g' ? 'selected' : '' }}>g</option>
                                    <option value="lb" {{ old('weight_unit') === 'lb' ? 'selected' : '' }}>lb</option>
                                    <option value="oz" {{ old('weight_unit') === 'oz' ? 'selected' : '' }}>oz</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Product Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="simple" {{ old('type', 'simple') === 'simple' ? 'selected' : '' }}>Simple Product</option>
                                <option value="variable" {{ old('type') === 'variable' ? 'selected' : '' }}>Variable Product</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Variable products have multiple variants</p>
                        </div>
                    </div>
                </div>

                {{-- Template --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Template</h2>
                    
                    <div>
                        <label for="template_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Page Template
                        </label>
                        <select name="template_id" id="template_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Default Template</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tags --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tags</h2>
                    <x-commerce::tag-selector 
                        :allTags="$allTags" 
                        :selectedTags="[]" 
                        name="tags"
                    />
                </div>

                {{-- Tax --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tax</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_taxable" id="is_taxable" value="1" {{ old('is_taxable') ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_taxable" class="ml-2 block text-sm text-gray-700">
                                This product is taxable
                            </label>
                        </div>

                        <div id="tax-rate-field" style="display: {{ old('is_taxable') ? 'block' : 'none' }};">
                            <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-1">
                                Tax Rate
                            </label>
                            <div class="relative">
                                <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', '0.20') }}" step="0.01" min="0" max="1"
                                    class="block w-full pr-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">%</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Enter as decimal (e.g., 0.20 for 20%)</p>
                        </div>
                    </div>
                </div>

                {{-- Sort Order --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Display</h2>
                    
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                            Sort Order
                        </label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-black text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                Create Product
            </button>
        </div>
    </form>
</div>

<script>
    // Toggle stock field based on track_inventory checkbox
    document.getElementById('track_inventory').addEventListener('change', function() {
        document.getElementById('stock-field').style.display = this.checked ? 'block' : 'none';
    });

    // Toggle tax rate field based on is_taxable checkbox
    document.getElementById('is_taxable').addEventListener('change', function() {
        document.getElementById('tax-rate-field').style.display = this.checked ? 'block' : 'none';
    });

    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });
</script>

</x-app>
