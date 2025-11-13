<x-app pageTitle="Product Variants" title="Product Variants - Admin">

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Product Variants</h1>
            <p class="text-gray-600 mt-1">{{ $product->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Product
            </a>
            <button type="button" onclick="document.getElementById('add-variant-form').classList.toggle('hidden')" class="inline-flex items-center px-4 py-2 bg-black text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Variant
            </button>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Add Variant Form --}}
    <div id="add-variant-form" class="hidden bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Add New Variant</h2>
        
        <form action="{{ route('products.variants.store', $product) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Variant Name --}}
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Variant Name
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        placeholder="e.g., Black / Large"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from options</p>
                </div>

                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                        SKU
                    </label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                {{-- Price --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                        Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">£</span>
                        <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                            class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                {{-- Compare At Price --}}
                <div>
                    <label for="compare_at_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Compare At Price
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">£</span>
                        <input type="number" name="compare_at_price" id="compare_at_price" value="{{ old('compare_at_price') }}" step="0.01" min="0"
                            class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                {{-- Stock --}}
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">
                        Stock Quantity
                    </label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                {{-- Option 1 --}}
                <div>
                    <label for="option1_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Option 1 Name
                    </label>
                    <input type="text" name="option1_name" id="option1_name" value="{{ old('option1_name', 'Color') }}"
                        placeholder="e.g., Color"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="option1_value" class="block text-sm font-medium text-gray-700 mb-1">
                        Option 1 Value
                    </label>
                    <input type="text" name="option1_value" id="option1_value" value="{{ old('option1_value') }}"
                        placeholder="e.g., Black"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                {{-- Option 2 --}}
                <div>
                    <label for="option2_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Option 2 Name
                    </label>
                    <input type="text" name="option2_name" id="option2_name" value="{{ old('option2_name', 'Size') }}"
                        placeholder="e.g., Size"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="option2_value" class="block text-sm font-medium text-gray-700 mb-1">
                        Option 2 Value
                    </label>
                    <input type="text" name="option2_value" id="option2_value" value="{{ old('option2_value') }}"
                        placeholder="e.g., Large"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                {{-- Option 3 --}}
                <div>
                    <label for="option3_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Option 3 Name
                    </label>
                    <input type="text" name="option3_name" id="option3_name" value="{{ old('option3_name') }}"
                        placeholder="e.g., Material"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div>
                    <label for="option3_value" class="block text-sm font-medium text-gray-700 mb-1">
                        Option 3 Value
                    </label>
                    <input type="text" name="option3_value" id="option3_value" value="{{ old('option3_value') }}"
                        placeholder="e.g., Cotton"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="flex items-center mt-4">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Active
                </label>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('add-variant-form').classList.add('hidden')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-black text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    Add Variant
                </button>
            </div>
        </form>
    </div>

    {{-- Variants List --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        @if($product->variants->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Variant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            SKU
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stock
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($product->variants as $variant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($variant->image)
                                        <img src="{{ $variant->image }}" alt="{{ $variant->getVariantTitle() }}"
                                             class="h-10 w-10 rounded object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $variant->getVariantTitle() }}
                                        </div>
                                        @if($variant->name)
                                            <div class="text-sm text-gray-500">
                                                {{ $variant->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $variant->sku ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                £{{ number_format($variant->price, 2) }}
                                @if($variant->compare_at_price)
                                    <span class="text-gray-400 line-through ml-2">
                                        £{{ number_format($variant->compare_at_price, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($variant->track_inventory)
                                    {{ $variant->stock ?? 0 }}
                                @else
                                    <span class="text-gray-400">Not tracked</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $variant->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $variant->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" onclick="editVariant({{ $variant->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                    Edit
                                </button>
                                <form action="{{ route('products.variants.destroy', [$product, $variant]) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this variant?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No variants</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding a variant to this product.</p>
                <div class="mt-6">
                    <button type="button" onclick="document.getElementById('add-variant-form').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Variant
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    function editVariant(variantId) {
        // TODO: Implement edit functionality
        alert('Edit variant functionality coming soon!');
    }
</script>

</x-app>
