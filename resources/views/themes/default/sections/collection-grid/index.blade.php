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
                            
                            <!-- Price -->
                            <div class="mb-4">
                                <div class="flex items-baseline space-x-2">
                                    <span class="text-xl font-bold text-gray-900">@currency($product->price)</span>
                                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                        <span class="text-sm text-gray-400 line-through">@currency($product->compare_at_price)</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="space-y-2">
                                @if($product->canPurchase())
                                    <form action="{{ route('purchasable.cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="purchasable_type" value="{{ get_class($product) }}">
                                        <input type="hidden" name="purchasable_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                            <i class="fas fa-shopping-cart mr-2"></i>
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="w-full bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-ban mr-2"></i>
                                        Unavailable
                                    </button>
                                @endif

                                <form action="{{ route('purchasable.wishlist.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="purchasable_type" value="{{ get_class($product) }}">
                                    <input type="hidden" name="purchasable_id" value="{{ $product->id }}">
                                    <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                                        <i class="far fa-heart mr-2"></i>
                                        Add to Wishlist
                                    </button>
                                </form>
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
