{{-- Product Detail Section --}}
<section class="product-detail-section py-16 px-8" style="background-color: {{ $background_color ?? '#ffffff' }};">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Product Images --}}
            <div class="product-images">
                @if(!empty($product->image_url))
                    <img src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-auto rounded-lg shadow-lg">
                @else
                    <div class="w-full h-96 bg-gray-100 flex items-center justify-center rounded-lg">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="product-info">
                {{-- Product Name --}}
                <h1 class="text-4xl font-bold mb-4">{{ $product->name }}</h1>

                {{-- Price --}}
                <div class="mb-6">
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <div class="flex items-center gap-3">
                            <span class="text-3xl font-bold text-red-600">
                                @currency($product->price)
                            </span>
                            <span class="text-xl text-gray-500 line-through">
                                @currency($product->compare_at_price)
                            </span>
                            <span class="bg-red-100 text-red-800 text-sm font-semibold px-2 py-1 rounded">
                                Save {{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}%
                            </span>
                        </div>
                    @else
                        <span class="text-3xl font-bold">
                            @currency($product->price)
                        </span>
                    @endif
                </div>

                {{-- SKU --}}
                @if($product->sku)
                    <p class="text-sm text-gray-600 mb-4">SKU: {{ $product->sku }}</p>
                @endif

                {{-- Stock Status --}}
                @if($product->track_inventory)
                    <div class="mb-6">
                        @if($product->inStock())
                            <span class="inline-flex items-center text-green-600">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                In Stock ({{ $product->stock_quantity }} available)
                            </span>
                        @else
                            <span class="inline-flex items-center text-red-600">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Out of Stock
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Description --}}
                @if($product->description)
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-3">Description</h2>
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif

                {{-- Add to Cart Button --}}
                @if($product->inStock())
                    <form action="/cart/add" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="purchasable_type" value="{{ get_class($product) }}">
                        <input type="hidden" name="purchasable_id" value="{{ $product->id }}">
                        
                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button type="button" 
                                        onclick="this.nextElementSibling.stepDown()"
                                        class="px-4 py-2 text-gray-600 hover:bg-gray-100">
                                    -
                                </button>
                                <input type="number" 
                                       name="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $product->track_inventory ? $product->stock_quantity : 999 }}"
                                       class="w-16 text-center border-x border-gray-300 py-2">
                                <button type="button" 
                                        onclick="this.previousElementSibling.stepUp()"
                                        class="px-4 py-2 text-gray-600 hover:bg-gray-100">
                                    +
                                </button>
                            </div>
                            
                            <button type="submit" 
                                    class="flex-1 bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                Add to Cart
                            </button>
                        </div>
                    </form>
                @else
                    <button disabled 
                            class="w-full bg-gray-300 text-gray-600 px-8 py-3 rounded-lg font-semibold cursor-not-allowed">
                        Out of Stock
                    </button>
                @endif

                {{-- Product Options --}}
                @if($product->options && count($product->options) > 0)
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Product Details</h3>
                        <dl class="space-y-2">
                            @foreach($product->options as $key => $value)
                                <div class="flex">
                                    <dt class="font-medium text-gray-700 w-1/3">{{ ucfirst($key) }}:</dt>
                                    <dd class="text-gray-600 w-2/3">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
