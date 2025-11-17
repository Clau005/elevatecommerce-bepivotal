@extends('core::account.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
            <p class="text-gray-600 mt-1">{{ $items->count() }} {{ Str::plural('item', $items->count()) }} in your cart</p>
        </div>
        @if($items->count() > 0)
        <div class="flex items-center space-x-2">
            <form action="{{ route('purchasable.cart.move-all-to-wishlist') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-heart mr-1"></i>
                    Move All to Wishlist
                </button>
            </form>
            <span class="text-gray-300">|</span>
            <form action="{{ route('purchasable.cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your cart?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                    <i class="fas fa-trash mr-1"></i>
                    Clear Cart
                </button>
            </form>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    @if($items->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cart Items -->
        <div class="lg:col-span-2 space-y-4">
            @foreach($items as $item)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-start space-x-4">
                    <!-- Product Image -->
                    <div class="flex-shrink-0 w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                        @if($item->purchasable->image_url ?? null)
                            <img src="{{ $item->purchasable->image_url }}" alt="{{ $item->purchasable->getPurchasableName() }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-box text-gray-400 text-3xl"></i>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->purchasable->getPurchasableName() }}</h3>
                        <p class="text-sm text-gray-500 mt-1">SKU: {{ $item->purchasable->getPurchasableSku() }}</p>
                        
                        @if($item->purchasable->description)
                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $item->purchasable->description }}</p>
                        @endif

                        <!-- Quantity Controls -->
                        <div class="flex items-center space-x-4 mt-4">
                            <form action="{{ route('purchasable.cart.update-quantity', $item) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                @method('PUT')
                                <label class="text-sm text-gray-600">Qty:</label>
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->purchasable->stock_quantity }}" class="w-20 px-3 py-1 border border-gray-300 rounded-md text-center">
                                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                    Update
                                </button>
                            </form>

                            @if($item->purchasable->track_inventory)
                            <span class="text-xs text-gray-500">
                                {{ $item->purchasable->stock_quantity }} in stock
                            </span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-4 mt-3">
                            <form action="{{ route('purchasable.cart.move-to-wishlist', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-heart mr-1"></i>
                                    Move to Wishlist
                                </button>
                            </form>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('purchasable.cart.remove', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash mr-1"></i>
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900">@currency($item->line_total)</div>
                        <div class="text-sm text-gray-500">@currency($item->price) each</div>
                        
                        @if($item->purchasable->compare_at_price && $item->purchasable->compare_at_price > $item->price)
                        <div class="text-xs text-gray-400 line-through">@currency($item->purchasable->compare_at_price)</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-lg p-6 sticky top-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal ({{ $cart->total_items }} items)</span>
                        <span class="font-medium text-gray-900">@currency($cart->subtotal)</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium text-gray-900">
                            @if($cart->shipping > 0)
                                @currency($cart->shipping)
                            @else
                                <span class="text-green-600">FREE</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax ({{ config('purchasable.tax.rate') * 100 }}%)</span>
                        <span class="font-medium text-gray-900">@currency($cart->tax)</span>
                    </div>
                    @if($cart->discount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-medium text-green-600">-@currency($cart->discount)</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-gray-200 pt-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-gray-900">@currency($cart->total)</span>
                    </div>
                </div>

                <a href="{{ route('purchasable.checkout.index') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition duration-200 mb-3">
                    Proceed to Checkout
                </a>

                <a href="{{ route('home') }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center transition duration-200">
                    Continue Shopping
                </a>

                <!-- Trust Badges -->
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                        <span>Secure Checkout</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-truck text-blue-600 mr-2"></i>
                        <span>Free Shipping Over $50</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-undo text-purple-600 mr-2"></i>
                        <span>30-Day Returns</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
        <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
        <p class="text-gray-600 mb-6">Add some items to get started!</p>
        <div class="flex items-center justify-center space-x-4">
            <a href="{{ route('home') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Start Shopping
            </a>
            <a href="{{ route('purchasable.wishlist.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200">
                View Wishlist
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
