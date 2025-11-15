@extends('core::admin.layouts.app')

@section('title', 'Currencies')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.settings.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Currencies</h1>
            </div>
            <p class="mt-1 text-sm text-gray-600">Manage currencies and exchange rates</p>
        </div>
        <a 
            href="{{ route('admin.settings.currencies.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
        >
            <i class="fas fa-plus mr-2"></i>
            Add Currency
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

    <!-- Currencies Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Currency</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Decimal Places</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exchange Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($currencies as $currency)
                    <tr class="{{ $currency->is_default ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $currency->code }}</span>
                                        @if($currency->is_default)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Default
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $currency->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg">{{ $currency->symbol }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $currency->decimal_places }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($currency->exchange_rate, 6) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($currency->is_enabled)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Disabled
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @if(!$currency->is_default)
                                    <form action="{{ route('admin.settings.currencies.set-default', $currency) }}" method="POST">
                                        @csrf
                                        <button 
                                            type="submit"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Set as default"
                                        >
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('admin.settings.currencies.toggle-enabled', $currency) }}" method="POST">
                                    @csrf
                                    <button 
                                        type="submit"
                                        class="text-gray-600 hover:text-gray-900"
                                        title="{{ $currency->is_enabled ? 'Disable' : 'Enable' }}"
                                    >
                                        <i class="fas fa-{{ $currency->is_enabled ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>

                                <a 
                                    href="{{ route('admin.settings.currencies.edit', $currency) }}"
                                    class="text-indigo-600 hover:text-indigo-900"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if(!$currency->is_default)
                                    <form action="{{ route('admin.settings.currencies.destroy', $currency) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this currency?')">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-dollar-sign text-4xl mb-4"></i>
                            <p>No currencies found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">About Currencies</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>All prices are stored in the smallest currency unit (pence, cents, etc.)</li>
                        <li>The default currency is used throughout the store</li>
                        <li>Exchange rates are relative to your base currency</li>
                        <li>Use <code class="bg-blue-100 px-1 rounded">@@currency($amount)</code> in Blade templates to format amounts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
