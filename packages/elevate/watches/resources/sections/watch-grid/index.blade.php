<section class="watch-grid py-12 px-4" style="background-color: {{ $background_color ?? '#f9fafb' }};">
    <div class="max-w-7xl mx-auto">
        @if(isset($collection) && $collection->products)
            <div class="grid grid-cols-1 md:grid-cols-{{ $columns ?? 3 }} gap-{{ $gap ?? 8 }}">
                @foreach($collection->products as $item)
                    @php
                        $watch = $item->collectable ?? $item;
                    @endphp
                    
                    @if($watch instanceof \Elevate\Watches\Models\Watch)
                        <div class="watch-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Watch Image -->
                            <a href="/watches/{{ $watch->slug }}" class="block">
                                @if($watch->featured_image)
                                    <div class="aspect-square overflow-hidden bg-gray-100">
                                        <img src="{{ $watch->featured_image }}" 
                                             alt="{{ $watch->name }}" 
                                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    </div>
                                @else
                                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            
                            <!-- Watch Info -->
                            <div class="p-6">
                                @if($watch->brand)
                                    <p class="text-sm text-gray-500 mb-1">{{ $watch->brand }}</p>
                                @endif
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="/watches/{{ $watch->slug }}" class="hover:text-blue-600">
                                        {{ $watch->name }}
                                    </a>
                                </h3>
                                
                                <!-- Price -->
                                <div class="mb-3">
                                    <span class="text-xl font-bold text-gray-900">
                                        ${{ number_format($watch->price, 2) }}
                                    </span>
                                    @if($watch->is_on_sale)
                                        <span class="text-sm text-gray-500 line-through ml-2">
                                            ${{ number_format($watch->compare_at_price, 2) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Specs Preview -->
                                @if($watch->movement_type || $watch->case_material)
                                    <div class="text-xs text-gray-600 mb-4 space-y-1">
                                        @if($watch->movement_type)
                                            <div>{{ $watch->movement_type }}</div>
                                        @endif
                                        @if($watch->case_material)
                                            <div>{{ $watch->case_material }}</div>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Stock Status -->
                                @if($watch->is_in_stock)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        In Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Out of Stock
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-500 text-lg">No watches found in this collection.</p>
            </div>
        @endif
    </div>
</section>
