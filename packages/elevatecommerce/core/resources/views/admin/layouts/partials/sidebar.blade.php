@php
    $navigation = \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::get('admin');
@endphp

<aside class="w-56 bg-slate-200 rounded-tl-xl overflow-y-auto flex flex-col">
    <!-- Navigation -->
    <nav class="p-2 flex-1 overflow-y-auto">
        <ul class="space-y-0.5">
            @foreach($navigation as $item)
                @if(empty($item['children']))
                    <!-- Single Item -->
                    <li>
                        <a 
                            href="{{ $item['url'] ?? ($item['route'] ? route($item['route']) : '#') }}" 
                            class="flex items-center px-2.5 py-1.5 rounded-md text-sm transition-colors
                                {{ request()->routeIs($item['route'] ?? '') 
                                    ? 'bg-slate-300 text-slate-900 font-medium' 
                                    : 'text-slate-700 hover:bg-slate-300 hover:text-slate-900' }}"
                        >
                            @if($item['icon'])
                                <i class="{{ $item['icon'] }} w-4 text-center mr-2.5 text-xs"></i>
                            @endif
                            <span>{{ $item['label'] }}</span>
                            @if($item['badge'])
                                <span class="ml-auto px-1.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    </li>
                @else
                    <!-- Parent with Children -->
                    <li x-data="{ open: {{ request()->routeIs($item['route'] ?? '') ? 'true' : 'false' }} }">
                        <button 
                            @click="open = !open"
                            class="flex items-center w-full px-2.5 py-1.5 rounded-md text-sm transition-colors
                                {{ request()->routeIs($item['route'] ?? '') 
                                    ? 'bg-slate-300 text-slate-900 font-medium' 
                                    : 'text-slate-700 hover:bg-slate-300 hover:text-slate-900' }}"
                        >
                            @if($item['icon'])
                                <i class="{{ $item['icon'] }} w-4 text-center mr-2.5 text-xs"></i>
                            @endif
                            <span class="flex-1 text-left">{{ $item['label'] }}</span>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        
                        <ul x-show="open" x-collapse class="mt-0.5 ml-7 space-y-0.5">
                            @foreach($item['children'] as $child)
                                <li>
                                    <a 
                                        href="{{ $child['url'] ?? ($child['route'] ? route($child['route']) : '#') }}" 
                                        class="flex items-center px-2.5 py-1.5 rounded-md text-sm transition-colors
                                            {{ request()->routeIs($child['route'] ?? '') 
                                                ? 'bg-slate-300 text-slate-900 font-medium' 
                                                : 'text-slate-700 hover:bg-slate-300 hover:text-slate-900' }}"
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
    <div class="border-t border-slate-400 p-2">
        <a 
            href="{{ route('admin.settings.index') }}" 
            class="flex items-center px-2.5 py-1.5 rounded-md text-sm text-slate-700 hover:bg-slate-300 hover:text-slate-900 transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-slate-300 text-slate-900 font-medium' : '' }}"
        >
            <i class="fas fa-cog w-4 text-center mr-2.5 text-xs"></i>
            <span>Settings</span>
        </a>
    </div>
</aside>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
