@extends('core::account.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="text-gray-600 mt-1">Complete your purchase</p>
        </div>
        <a href="{{ route('purchasable.cart.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>Back to Cart
        </a>
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

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Checkout Form -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('purchasable.checkout.process') }}" method="POST" id="checkoutForm">
                @csrf

                <!-- Billing Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Billing Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" id="billing_name" name="billing_name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                   value="{{ old('billing_name', auth()->user()->name ?? '') }}">
                        </div>

                        <div>
                            <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" id="billing_email" name="billing_email" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                   value="{{ old('billing_email', auth()->user()->email ?? '') }}">
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Method</h2>
                    
                    <div class="space-y-3">
                        @foreach($paymentMethods as $method)
                            @if($method['enabled'])
                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition duration-200 {{ $loop->first ? 'border-blue-500 bg-blue-50' : '' }}">
                                <input type="radio" name="payment_method" value="{{ $method['id'] }}" 
                                       class="mt-1 mr-3" {{ $loop->first ? 'checked' : '' }} required>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <i class="{{ $method['icon'] }} text-xl text-gray-700"></i>
                                        <span class="font-semibold text-gray-900">{{ $method['name'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $method['description'] }}</p>
                                </div>
                            </label>
                            @else
                            <div class="flex items-start p-4 border-2 border-gray-200 rounded-lg opacity-50 cursor-not-allowed">
                                <input type="radio" disabled class="mt-1 mr-3">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <i class="{{ $method['icon'] }} text-xl text-gray-400"></i>
                                        <span class="font-semibold text-gray-500">{{ $method['name'] }}</span>
                                        <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded">Coming Soon</span>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ $method['description'] }}</p>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="flex items-start">
                            <input type="checkbox" required class="mt-1 mr-3">
                            <span class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms & Conditions</a> 
                                and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition duration-200">
                        <i class="fas fa-lock mr-2"></i>
                        Proceed to Payment
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-lg p-6 sticky top-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>
                
                <!-- Items -->
                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                    @foreach($items as $item)
                    <div class="flex items-center space-x-3 pb-3 border-b border-gray-100">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
                            @if($item->purchasable->image_url ?? null)
                                <img src="{{ $item->purchasable->image_url }}" alt="{{ $item->purchasable->getPurchasableName() }}" class="w-full h-full object-cover rounded">
                            @else
                                <i class="fas fa-box text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $item->purchasable->getPurchasableName() }}</p>
                            <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-sm font-medium text-gray-900">
                            @currency($item->line_total)
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium text-gray-900">@currency($cart->subtotal)</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium text-gray-900">@currency($cart->shipping)</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium text-gray-900">@currency($cart->tax)</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-gray-900">@currency($cart->total)</span>
                    </div>
                </div>

                <!-- Security Badges -->
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                        <span>Secure SSL Encryption</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-lock text-blue-600 mr-2"></i>
                        <span>Safe & Secure Checkout</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-undo text-purple-600 mr-2"></i>
                        <span>30-Day Money Back Guarantee</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
