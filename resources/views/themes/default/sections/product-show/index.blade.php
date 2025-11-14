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
                        @currency($product->price ?? 0)
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
                    
                    <!-- Add to Cart Form -->
                    <form action="{{ route('storefront.cart.add') }}" method="POST" class="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="purchasable_type" value="{{ get_class($product) }}">
                        <input type="hidden" name="purchasable_id" value="{{ $product->id }}">
                        
                        <!-- Quantity -->
                        <div class="mb-6">
                            <label class="block font-semibold text-gray-700 mb-2">Quantity:</label>
                            <input type="number" name="quantity" value="1" min="1" max="99"
                                   class="w-24 px-4 py-3 border border-gray-300 rounded-md text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <!-- Add to Cart Button -->
                        <button type="submit" class="w-full px-8 py-4 rounded-lg text-lg font-semibold text-white transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                                style="background-color: {{ $button_color ?? '#3b82f6' }};">
                            <span class="button-text">{{ $button_text ?? 'Add to Cart' }}</span>
                            <span class="button-loading hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Adding...
                            </span>
                        </button>
                    </form>
                    
                    <!-- Success Message -->
                    <div class="cart-success-message hidden mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Added to cart successfully!</span>
                        </div>
                    </div>
                    
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.add-to-cart-form');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const button = form.querySelector('button[type="submit"]');
        const buttonText = button.querySelector('.button-text');
        const buttonLoading = button.querySelector('.button-loading');
        const successMessage = document.querySelector('.cart-success-message');
        
        // Disable button and show loading
        button.disabled = true;
        buttonText.classList.add('hidden');
        buttonLoading.classList.remove('hidden');
        successMessage.classList.add('hidden');
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin', // Important: Send cookies with request
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Show success message
                successMessage.classList.remove('hidden');
                
                // Update cart count if element exists
                const cartCount = document.querySelector('.cart-count');
                if (cartCount && data.cart_count) {
                    cartCount.textContent = data.cart_count;
                }
                
                // Hide success message after 3 seconds
                setTimeout(() => {
                    successMessage.classList.add('hidden');
                }, 3000);
            } else {
                alert(data.message || 'Failed to add item to cart');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            // Re-enable button
            button.disabled = false;
            buttonText.classList.remove('hidden');
            buttonLoading.classList.add('hidden');
        }
    });
});
</script>
