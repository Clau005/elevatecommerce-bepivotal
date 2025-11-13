<x-app pageTitle="Dashboard" title="Dashboard - Admin" description="Overview of your store">

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Welcome back! Here's what's happening with your store today.</p>
            </div>
            <div class="text-sm text-gray-500">
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>

        {{-- Dashboard Lenses Grid --}}
        <div class="grid grid-cols-12 gap-6">
            @foreach($lenses as $lens)
                <div class="col-span-12 md:col-span-{{ $lens->width() }}">
                    <div class="{{ $lens->containerClasses() }}">
                        {{-- Lens Header --}}
                        @if($lens->name())
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $lens->name() }}</h3>
                                @if($lens->description())
                                    <p class="text-sm text-gray-600 mt-1">{{ $lens->description() }}</p>
                                @endif
                            </div>
                        @endif

                        {{-- Lens Content --}}
                        <div class="lens-content">
                            {!! $lens->toHtml() !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Empty State --}}
        @if($lenses->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No dashboard widgets</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by registering dashboard lenses.</p>
            </div>
        @endif
    </div>

</x-app>
