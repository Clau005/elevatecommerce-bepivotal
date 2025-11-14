<section class="product-show-section py-16 px-8" style="background-color: {{ $background_color ?? '#ffffff' }};">
    <div class="max-w-7xl mx-auto">
        @php
            // Support both $product and $model (from dynamic template rendering)
            $product = $product ?? $model ?? null;
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">
            <!-- Product Images -->
            <div class="product-images">
                @if($product && $product->featured_image)
                    <div class="rounded-lg overflow-hidden border border-gray-200 shadow-lg">
                        <img src="{{ $product->featured_image }}" alt="{{ $product->name ?? 'Product' }}" class="w-full h-auto object-cover">
                    </div>
                @else
                    <div class="w-full h-96 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-lg border border-gray-200">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p>No Image Available</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Product Details -->
            <div class="product-details">
                @if($product)
                    <!-- Breadcrumbs -->
                    @if(isset($product->breadcrumbs))
                        <nav class="mb-4">
                            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                                @foreach($product->breadcrumbs as $index => $crumb)
                                    @if($index > 0)
                                        <li><span class="mx-2">/</span></li>
                                    @endif
                                    <li>
                                        @if($crumb['url'])
                                            <a href="{{ $crumb['url'] }}" class="hover:text-gray-700">{{ $crumb['name'] }}</a>
                                        @else
                                            <span class="text-gray-900 font-medium">{{ $crumb['name'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    @endif
                
                    <h1 class="text-4xl font-bold mb-4 leading-tight text-gray-900">{{ $product->name }}</h1>
                    
                    <div class="text-3xl font-bold text-blue-600 mb-6">
                        ${{ number_format($product->price ?? 0, 2) }}
                    </div>
                    
                    @if($product->description)
                        <div class="text-base leading-7 text-gray-700 mb-8 pb-8 border-b border-gray-200">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    @endif
                    
                    <!-- Stock Status -->
                    <div class="mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            In Stock
                        </span>
                    </div>
                    
                    <!-- Quantity -->
                    <div class="mb-6">
                        <label class="block font-semibold text-gray-700 mb-2">Quantity:</label>
                        <input type="number" value="1" min="1" max="99"
                               class="w-24 px-4 py-3 border border-gray-300 rounded-md text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Add to Cart Button -->
                    <button class="w-full px-8 py-4 rounded-lg text-lg font-semibold text-white transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5"
                            style="background-color: {{ $button_color ?? '#3b82f6' }};">
                        {{ $button_text ?? 'Add to Cart' }}
                    </button>
                    
                    <!-- Product Meta -->
                    <div class="mt-8 pt-8 border-t border-gray-200 space-y-3 text-sm text-gray-600">
                        @if($product->sku)
                            <div><span class="font-semibold">SKU:</span> {{ $product->sku }}</div>
                        @endif
                        @if($product->type)
                            <div><span class="font-semibold">Type:</span> {{ ucfirst($product->type) }}</div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">Product information not available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
