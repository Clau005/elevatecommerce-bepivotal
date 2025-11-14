<section class="collection-grid-section py-16 px-8" style="background-color: {{ $background_color ?? '#ffffff' }};">
    <div class="max-w-7xl mx-auto">
        @if(isset($title))
            <h2 class="text-3xl font-bold mb-8 text-{{ $title_alignment ?? 'left' }}">{{ $title }}</h2>
        @endif
        
        <!-- Product Grid -->
        <div class="grid gap-8" style="grid-template-columns: repeat(auto-fill, minmax({{ $column_width ?? '250px' }}, 1fr));">
            @php
                // Support multiple data sources
                $items = $collection->products;
            @endphp
            @if(count($collection->collectables) > 0)
                @foreach($collection->collectables as $collectable)
                    @php
                        // If item is a Collectable, get the actual product
                        $product = $collectable->collectable;
                    @endphp
                    <div class="product-card border border-gray-200 rounded-lg overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        @if($product->featured_image)
                            <img src="{{ $product->featured_image ?? '' }}" alt="{{ $product->name }}" class="w-full h-72 object-cover">
                        @else
                            <div class="w-full h-72 bg-gray-100 flex items-center justify-center text-gray-400">
                                No Image
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
                            
                            @if($product->description)
                                <p class="text-sm text-gray-600 mb-4 leading-relaxed">{{ Str::limit($product->description, 100) }}</p>
                            @endif
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-gray-900">@currency($product->price)</span>
                                
                                <a href="/products/{{ $product->slug }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-300">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="col-span-full text-center text-gray-500 py-12">
                    No products found in this collection.
                </p>
            @endif
        </div>
    </div>
</section>
