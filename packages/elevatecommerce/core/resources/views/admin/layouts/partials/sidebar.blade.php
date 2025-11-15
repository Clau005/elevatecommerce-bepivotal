@php
    $navigation = \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::get('admin');
@endphp

<aside class="w-64 bg-slate-200 text-slate-800 rounded-tl-xl flex flex-col fixed left-0 top-16 bottom-0 z-10">
    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-3">
            @foreach($navigation as $item)
                @if(empty($item['children']))
                    <!-- Single Item -->
                    <li>
                        <a 
                            href="{{ $item['url'] ?? ($item['route'] ? route($item['route']) : '#') }}" 
                            class="flex items-center px-3 py-2 rounded-lg transition-colors
                                {{ request()->routeIs($item['route'] ?? '') || (isset($item['active']) && request()->routeIs($item['active'])) 
                                    ? 'bg-slate-400 text-slate-900 font-medium' 
                                    : 'text-slate-700 hover:bg-slate-400 hover:text-slate-900' }}"
                        >
                            @if($item['icon'])
                                <i class="{{ $item['icon'] }} w-5 text-center mr-3"></i>
                            @endif
                            <span class="flex-1">{{ $item['label'] }}</span>
                            @if($item['badge'])
                                <span class="ml-auto px-2 py-0.5 text-xs font-semibold bg-blue-600 text-white rounded-full">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    </li>
                @else
                    <!-- Item with Children -->
                    <li x-data="{ open: {{ request()->routeIs($item['active'] ?? '') ? 'true' : 'false' }} }">
                        <button 
                            @click="open = !open"
                            class="w-full flex items-center px-3 py-2 rounded-lg transition-colors text-slate-700 hover:bg-slate-400 hover:text-slate-900"
                        >
                            @if($item['icon'])
                                <i class="{{ $item['icon'] }} w-5 text-center mr-3"></i>
                            @endif
                            <span class="flex-1 text-left">{{ $item['label'] }}</span>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        
                        <ul x-show="open" x-collapse class="mt-1 ml-8 space-y-1">
                            @foreach($item['children'] as $child)
                                <li>
                                    <a 
                                        href="{{ $child['url'] ?? ($child['route'] ? route($child['route']) : '#') }}" 
                                        class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors
                                            {{ request()->routeIs($child['route'] ?? '') 
                                                ? 'bg-slate-400 text-slate-900 font-medium' 
                                                : 'text-slate-700 hover:bg-slate-400 hover:text-slate-900' }}"
                                    >
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>

    <!-- Settings (Bottom) -->
    <div class="border-t border-slate-400 p-3">
        <a 
            href="{{ route('admin.settings.index') }}" 
            class="flex items-center px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-400 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-slate-400 text-slate-900 font-medium' : '' }}"
        >
            <i class="fas fa-cog w-5 text-center mr-3"></i>
            <span>Settings</span>
        </a>
    </div>
</aside>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
