@extends('commerce::storefront.layouts.commerce')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Shopping Cart</h1>
                <p class="mt-2 text-sm text-gray-600">Review your items before checkout</p>
            </div>
    
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
    
            <!-- Error Message -->
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
    
            @if($cart && $cart->lines->count() > 0)
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Cart Items -->
                    <div class="xl:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900">
                                    Cart Items 
                                    <span class="text-gray-500 font-normal">({{ $cart->lines->count() }})</span>
                                </h2>
                            </div>
                            
                            <div class="divide-y divide-gray-200">
                                @foreach($cart->lines as $line)
                                    <div class="px-4 sm:px-6 py-6">
                                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0">
                                                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                                    @if($line->preview)
                                                        <img src="{{ $line->preview }}" 
                                                             alt="{{ $line->description }}" 
                                                             class="w-full h-full object-cover">
                                                    @else
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Product Details -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <h3 class="text-base font-medium text-gray-900 line-clamp-2">{{ $line->description }}</h3>
                                                        <p class="mt-1 text-sm text-gray-500">{{ class_basename($line->purchasable_type) }}</p>
                                                        
                                                        {{-- Event Entry - Just show participant count --}}
                                                        @if($line->purchasable_type === 'App\\Models\\EventRaceEntry' && isset($line->meta['participants']) && is_array($line->meta['participants']))
                                                            <div class="mt-2">
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    {{ count($line->meta['participants']) }} {{ Str::plural('Participant', count($line->meta['participants'])) }} Registered
                                                                </span>
                                                            </div>
                                                        @endif
                                                        
                                                        {{-- Other Product Types --}}
                                                        @if($line->purchasable_type !== 'App\\Models\\EventRaceEntry' && $line->meta && count($line->meta) > 0)
                                                            <div class="mt-2 flex flex-wrap gap-1">
                                                                @foreach($line->meta as $key => $value)
                                                                    @if(in_array($key, ['sku']))
                                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                                            {{ $key }}: {{ $value }}
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Price (Desktop) -->
                                                    <div class="hidden sm:block text-right flex-shrink-0">
                                                        <p class="text-base font-semibold text-gray-900">£{{ number_format($line->unit_price / 100, 2) }}</p>
                                                        <p class="text-sm text-gray-500">each</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Quantity and Actions -->
                                                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                                    <div class="flex items-center gap-4">
                                                        <!-- Quantity Selector or Fixed Quantity -->
                                                        @if($line->purchasable_type === 'App\\Models\\EventRaceEntry')
                                                            <!-- Fixed quantity for event entries (can't register twice) -->
                                                            <div class="flex items-center">
                                                                <span class="text-sm font-medium text-gray-700 mr-2">Qty:</span>
                                                                <span class="text-sm font-medium text-gray-900 bg-gray-100 px-3 py-1.5 rounded-md">1</span>
                                                            </div>
                                                        @else
                                                            <!-- Adjustable quantity for other items -->
                                                            <form action="{{ route('storefront.cart.update', [$line->purchasable_type, $line->purchasable_id]) }}" method="POST" class="flex items-center">
                                                                @csrf
                                                                @method('POST')
                                                                <label for="quantity-{{ $line->id }}" class="text-sm font-medium text-gray-700 mr-2">Qty:</label>
                                                                <select name="quantity" id="quantity-{{ $line->id }}" 
                                                                        class="appearance-none rounded-md border-gray-300 py-1.5 pl-3 pr-8 text-sm font-medium text-gray-900 bg-white shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 cursor-pointer"
                                                                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2210%22%20height%3D%225%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M0%200l5%205%205-5z%22%20fill%3D%22%23666%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 0.625rem;"
                                                                        onchange="this.form.submit()">
                                                                    @for($i = 1; $i <= 10; $i++)
                                                                        <option value="{{ $i }}" {{ $line->quantity == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </form>
                                                        @endif
                                                        
                                                        <!-- Remove Button -->
                                                        <form action="{{ route('storefront.cart.remove', [$line->purchasable_type, $line->purchasable_id]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium transition-colors">
                                                                Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                    
                                                    <!-- Price and Total -->
                                                    <div class="flex items-center justify-between sm:justify-end gap-4">
                                                        <!-- Price (Mobile) -->
                                                        <div class="sm:hidden">
                                                            <p class="text-sm font-medium text-gray-900">£{{ number_format($line->unit_price / 100, 2) }} each</p>
                                                        </div>
                                                        
                                                        <!-- Total -->
                                                        <div class="text-right">
                                                            <p class="text-base font-semibold text-gray-900">£{{ number_format($line->total / 100, 2) }}</p>
                                                            <p class="text-sm text-gray-500">total</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Clear Cart -->
                            <div class="px-4 sm:px-6 py-4 border-t border-gray-200 bg-gray-50">
                                <form action="{{ route('storefront.cart.clear') }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-sm text-red-600 hover:text-red-800 font-medium transition-colors" 
                                            onclick="return confirm('Are you sure you want to clear your cart?')">
                                        Clear Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
    
                    <!-- Order Summary -->
                    <div class="xl:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">
                                        Subtotal ({{ $totals['item_count'] }} {{ Str::plural('item', $totals['item_count']) }})
                                    </span>
                                    <span class="font-medium text-gray-900">{{ $totals['formatted']['subtotal'] ?? '£0.00' }}</span>
                                </div>
                                
                                @if(isset($totals['tax']) && $totals['tax'] > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tax</span>
                                        <span class="font-medium text-gray-900">{{ $totals['formatted']['tax'] ?? '£0.00' }}</span>
                                    </div>
                                @endif
                                
                                <hr class="border-gray-200">
                                
                                <div class="flex justify-between text-base font-semibold">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-gray-900">{{ $totals['formatted']['total'] ?? '£0.00' }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <a href="{{ route('checkout') }}" 
                                   class="w-full bg-blue-800 hover:bg-blue-900 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-800 block uppercase">
                                    Proceed to Checkout
                                </a>
                                
                                <a href="{{ route('storefront.shop') }}" 
                                   class="w-full bg-white hover:bg-gray-50 text-gray-700 text-center py-3 px-4 rounded-lg font-medium border border-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 block">
                                    Continue Shopping
                                </a>
                            </div>
                            
                            <!-- Security Badge -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-center text-xs text-gray-500">
                                    <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Secure checkout
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-16 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L4 5H2m5 8h10m0 0l1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h3>
                        <p class="text-gray-500 mb-8 max-w-sm mx-auto">Start shopping to add items to your cart and see them here.</p>
                        <a href="/" 
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-800 hover:bg-blue-900 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-800 uppercase">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endif
</div>
@endsection