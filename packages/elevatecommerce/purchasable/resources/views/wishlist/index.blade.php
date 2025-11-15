@extends('core::account.layouts.app')

@section('title', 'Wishlist')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Wishlist</h1>
            <p class="text-gray-600 mt-1">{{ $items->count() }} {{ Str::plural('item', $items->count()) }} saved for later</p>
        </div>
        @if($items->count() > 0)
        <div class="flex items-center space-x-2">
            <form action="{{ route('purchasable.wishlist.move-all-to-cart') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-shopping-cart mr-1"></i>
                    Move All to Cart
                </button>
            </form>
            <span class="text-gray-300">|</span>
            <form action="{{ route('purchasable.wishlist.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your wishlist?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                    <i class="fas fa-trash mr-1"></i>
                    Clear Wishlist
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
    <!-- Wishlist Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $item)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-200">
            <!-- Product Image -->
            <div class="relative aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                @if($item->purchasable->image_url ?? null)
                    <img src="{{ $item->purchasable->image_url }}" alt="{{ $item->purchasable->getPurchasableName() }}" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-box text-gray-400 text-6xl"></i>
                @endif

                <!-- Stock Badge -->
                @if($item->purchasable->isInStock())
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded">
                        In Stock
                    </span>
                @else
                    <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">
                        Out of Stock
                    </span>
                @endif

                <!-- Discount Badge -->
                @if($item->purchasable->compare_at_price && $item->purchasable->compare_at_price > $item->purchasable->price)
                    @php
                        $discount = round((($item->purchasable->compare_at_price - $item->purchasable->price) / $item->purchasable->compare_at_price) * 100);
                    @endphp
                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">
                        -{{ $discount }}%
                    </span>
                @endif
            </div>

            <!-- Product Details -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-2">
                    {{ $item->purchasable->getPurchasableName() }}
                </h3>
                <p class="text-xs text-gray-500 mb-2">SKU: {{ $item->purchasable->getPurchasableSku() }}</p>

                @if($item->purchasable->description)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $item->purchasable->description }}</p>
                @endif

                <!-- Price -->
                <div class="mb-4">
                    <div class="flex items-baseline space-x-2">
                        <span class="text-xl font-bold text-gray-900">@currency($item->purchasable->price)</span>
                        @if($item->purchasable->compare_at_price && $item->purchasable->compare_at_price > $item->purchasable->price)
                            <span class="text-sm text-gray-400 line-through">@currency($item->purchasable->compare_at_price)</span>
                        @endif
                    </div>
                </div>

                <!-- Added Date -->
                <p class="text-xs text-gray-500 mb-4">
                    <i class="far fa-clock mr-1"></i>
                    Added {{ $item->created_at->diffForHumans() }}
                </p>

                <!-- Actions -->
                <div class="space-y-2">
                    @if($item->purchasable->canPurchase())
                        <form action="{{ route('purchasable.wishlist.move-to-cart', $item) }}" method="POST">
                            @csrf
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

                    <form action="{{ route('purchasable.wishlist.remove', $item) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-trash mr-2"></i>
                            Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty Wishlist -->
    <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
        <i class="fas fa-heart text-gray-300 text-6xl mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Your wishlist is empty</h2>
        <p class="text-gray-600 mb-6">Save items you love for later!</p>
        <div class="flex items-center justify-center space-x-4">
            <a href="{{ route('home') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Start Shopping
                </a>
            <a href="{{ route('purchasable.cart.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200">
                View Cart
            </a>
        </div>
    </div>
    @endif

    <!-- Wishlist Tips -->
    @if($items->count() > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i>
            Wishlist Tips
        </h3>
        <ul class="space-y-2 text-blue-800 text-sm">
            <li class="flex items-start">
                <i class="fas fa-check-circle text-blue-600 mt-0.5 mr-2"></i>
                <span>Items in your wishlist are saved across devices when you're logged in</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-blue-600 mt-0.5 mr-2"></i>
                <span>We'll notify you when items go on sale or are back in stock</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-blue-600 mt-0.5 mr-2"></i>
                <span>Share your wishlist with friends and family</span>
            </li>
        </ul>
    </div>
    @endif
</div>
@endsection
