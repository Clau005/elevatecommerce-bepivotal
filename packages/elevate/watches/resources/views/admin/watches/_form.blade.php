<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Basic Information --}}
    <div class="md:col-span-2">
        <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
        <input type="text" name="name" value="{{ old('name', $watch->name ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $watch->slug ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from name</p>
        @error('slug')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
        <textarea name="description" rows="4" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $watch->description ?? '') }}</textarea>
        @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Watch Specifications --}}
    <div class="md:col-span-2 mt-6">
        <h3 class="text-lg font-semibold mb-4">Watch Specifications</h3>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
        <input type="text" name="brand" value="{{ old('brand', $watch->brand ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('brand')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Model Number</label>
        <input type="text" name="model_number" value="{{ old('model_number', $watch->model_number ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('model_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Movement Type</label>
        <select name="movement_type" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Select...</option>
            <option value="Automatic" {{ old('movement_type', $watch->movement_type ?? '') == 'Automatic' ? 'selected' : '' }}>Automatic</option>
            <option value="Quartz" {{ old('movement_type', $watch->movement_type ?? '') == 'Quartz' ? 'selected' : '' }}>Quartz</option>
            <option value="Manual" {{ old('movement_type', $watch->movement_type ?? '') == 'Manual' ? 'selected' : '' }}>Manual</option>
        </select>
        @error('movement_type')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Case Material</label>
        <input type="text" name="case_material" value="{{ old('case_material', $watch->case_material ?? '') }}" 
            placeholder="e.g., Stainless Steel, Gold, Titanium"
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('case_material')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Case Diameter (mm)</label>
        <input type="number" step="0.1" name="case_diameter" value="{{ old('case_diameter', $watch->case_diameter ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('case_diameter')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Water Resistance (meters)</label>
        <input type="number" name="water_resistance" value="{{ old('water_resistance', $watch->water_resistance ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('water_resistance')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Strap Material</label>
        <input type="text" name="strap_material" value="{{ old('strap_material', $watch->strap_material ?? '') }}" 
            placeholder="e.g., Leather, Metal, Rubber"
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('strap_material')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Pricing & Inventory --}}
    <div class="md:col-span-2 mt-6">
        <h3 class="text-lg font-semibold mb-4">Pricing & Inventory</h3>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $watch->price ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        @error('price')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Compare at Price</label>
        <input type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price', $watch->compare_at_price ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Original price for showing discounts</p>
        @error('compare_at_price')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Cost</label>
        <input type="number" step="0.01" name="cost" value="{{ old('cost', $watch->cost ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('cost')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $watch->sku ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('sku')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $watch->stock_quantity ?? 0) }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('stock_quantity')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Threshold</label>
        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $watch->low_stock_threshold ?? 5) }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('low_stock_threshold')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Media & Settings --}}
    <div class="md:col-span-2 mt-6">
        <h3 class="text-lg font-semibold mb-4">Media & Settings</h3>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image URL</label>
        <input type="text" name="featured_image" value="{{ old('featured_image', $watch->featured_image ?? '') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('featured_image')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="draft" {{ old('status', $watch->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="active" {{ old('status', $watch->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="archived" {{ old('status', $watch->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
        @error('status')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
        <select name="template_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Default Template</option>
            @foreach($templates as $template)
                <option value="{{ $template->id }}" {{ old('template_id', $watch->template_id ?? '') == $template->id ? 'selected' : '' }}>
                    {{ $template->name }}
                </option>
            @endforeach
        </select>
        @error('template_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="flex items-center">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $watch->is_featured ?? false) ? 'checked' : '' }}
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-700">Featured Watch</span>
        </label>
        @error('is_featured')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
