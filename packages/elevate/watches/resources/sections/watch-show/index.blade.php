<section class="watch-show-section py-16 px-8" style="background-color: {{ $background_color ?? '#ffffff' }};">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">
            <!-- Watch Images -->
            <div class="watch-images">
                @if(isset($watch) && $watch->featured_image)
                    <div class="rounded-lg overflow-hidden border border-gray-200 shadow-lg">
                        <img src="{{ $watch->featured_image }}" alt="{{ $watch->name ?? 'Watch' }}" class="w-full h-auto object-cover">
                    </div>
                @else
                    <div class="w-full h-96 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-lg border border-gray-200">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>No Image Available</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Watch Details -->
            <div class="watch-details">
                @if($watch)
                    <h1 class="text-4xl font-bold mb-2 leading-tight text-gray-900">{{ $watch->name }}</h1>
                    
                    @if($watch->brand)
                        <p class="text-lg text-gray-600 mb-6">{{ $watch->brand }}</p>
                    @endif
                    
                    <div class="text-3xl font-bold text-blue-600 mb-6">
                        ${{ number_format($watch->price ?? 0, 2) }}
                        @if($watch->is_on_sale)
                            <span class="text-lg text-gray-500 line-through ml-2">${{ number_format($watch->compare_at_price, 2) }}</span>
                            <span class="text-sm text-red-600 ml-2">Save {{ $watch->discount_percentage }}%</span>
                        @endif
                    </div>
                    
                    @if($watch->description)
                        <div class="text-base leading-7 text-gray-700 mb-8 pb-8 border-b border-gray-200">
                            {!! nl2br(e($watch->description)) !!}
                        </div>
                    @endif
                    
                    <!-- Watch Specifications -->
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Specifications</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if($watch->movement_type)
                                <div>
                                    <span class="font-semibold text-gray-700">Movement:</span>
                                    <span class="text-gray-600">{{ $watch->movement_type }}</span>
                                </div>
                            @endif
                            @if($watch->case_material)
                                <div>
                                    <span class="font-semibold text-gray-700">Case Material:</span>
                                    <span class="text-gray-600">{{ $watch->case_material }}</span>
                                </div>
                            @endif
                            @if($watch->case_diameter)
                                <div>
                                    <span class="font-semibold text-gray-700">Case Diameter:</span>
                                    <span class="text-gray-600">{{ $watch->case_diameter }}mm</span>
                                </div>
                            @endif
                            @if($watch->water_resistance)
                                <div>
                                    <span class="font-semibold text-gray-700">Water Resistance:</span>
                                    <span class="text-gray-600">{{ $watch->water_resistance }}m</span>
                                </div>
                            @endif
                            @if($watch->strap_material)
                                <div>
                                    <span class="font-semibold text-gray-700">Strap Material:</span>
                                    <span class="text-gray-600">{{ $watch->strap_material }}</span>
                                </div>
                            @endif
                            @if($watch->model_number)
                                <div>
                                    <span class="font-semibold text-gray-700">Model Number:</span>
                                    <span class="text-gray-600">{{ $watch->model_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="mb-6">
                        @if($watch->is_in_stock)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                In Stock ({{ $watch->stock_quantity }} available)
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                Out of Stock
                            </span>
                        @endif
                    </div>
                    
                    <!-- Quantity -->
                    @if($watch->is_in_stock)
                        <div class="mb-6">
                            <label class="block font-semibold text-gray-700 mb-2">Quantity:</label>
                            <input type="number" value="1" min="1" max="{{ $watch->stock_quantity }}"
                                   class="w-24 px-4 py-3 border border-gray-300 rounded-md text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <!-- Add to Cart Button -->
                        <button class="w-full px-8 py-4 rounded-lg text-lg font-semibold text-white transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5"
                                style="background-color: {{ $button_color ?? '#3b82f6' }};">
                            {{ $button_text ?? 'Add to Cart' }}
                        </button>
                    @endif
                    
                    <!-- Watch Meta -->
                    <div class="mt-8 pt-8 border-t border-gray-200 space-y-3 text-sm text-gray-600">
                        @if($watch->sku)
                            <div><span class="font-semibold">SKU:</span> {{ $watch->sku }}</div>
                        @endif
                        @if($watch->weight)
                            <div><span class="font-semibold">Weight:</span> {{ $watch->weight }}g</div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">Watch information not available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
