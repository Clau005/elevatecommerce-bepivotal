<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Vite Assets - CSS Only for Blade Pages --}}
    @vite(['resources/css/app.css'])

    {{-- BladewindUI Assets --}}
    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>

    {{-- Rich Text Laravel (Trix) Assets --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    {{-- Alpine.js for interactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      [x-cloak] { display: none !important; }
    </style>

  </head>
  <body class="antialiased bg-gray-100 h-screen overflow-hidden">
    
    {{-- HEADER - Fixed at top --}}
    <header class="fixed top-0 left-0 right-0 h-14 bg-gray-900 border-b border-gray-800 z-50">
      <div class="h-full flex items-center justify-between px-4">
        {{-- Logo --}}
        <div class="flex items-center gap-3 w-56">
          <div class="w-7 h-7 bg-white rounded flex items-center justify-center">
            <span class="text-gray-900 font-bold text-sm">EC</span>
          </div>
          <span class="text-white font-semibold text-sm">{{ config('app.name', 'Elevate Commerce') }}</span>
        </div>

        {{-- Search --}}
        <div class="flex-1 max-w-2xl mx-8">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" placeholder="Search" class="w-full pl-10 pr-3 py-1.5 border border-gray-700 rounded-lg bg-gray-800  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
          </div>
        </div>

        {{-- Right Icons --}}
        <div class="flex items-center gap-4">
          <button class="text-gray-300 hover:text-white">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
          </button>
          <button class="text-gray-300 hover:text-white relative">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500 ring-2 ring-gray-900"></span>
          </button>
          <div class="text-gray-300 text-sm border-l border-gray-700 pl-4">
            {{ config('app.name', 'My Store') }}
          </div>
          <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2 text-gray-300 hover:text-white">
              <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium">
                {{ substr(auth('staff')->user()->first_name ?? 'A', 0, 1) }}
              </div>
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
              <div class="py-1">
                <div class="px-4 py-2 text-sm text-gray-700 border-b">
                  <div class="font-medium">{{ auth('staff')->user()->first_name ?? 'Admin' }}</div>
                  <div class="text-xs text-gray-500">{{ auth('staff')->user()->email ?? '' }}</div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                  @csrf
                  <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    {{-- Flex container for sidebar and content (below fixed header) --}}
    <div class="flex h-screen pt-14">
      
      {{-- SIDEBAR - Fixed on left, scrollable --}}
      <aside class="fixed left-0 top-14 bottom-0 w-56 bg-gray-50 border-r border-gray-200 overflow-y-auto z-40">
        <nav class="p-3 space-y-0.5">
          @php
            $navigation = app('admin.navigation');
            $navGroups = $navigation->items();
          @endphp

          @foreach($navGroups as $groupName => $group)
            @if($groupName !== 'main' && isset($group['label']))
              <div class="px-3 pt-5 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ $group['label'] }}
              </div>
            @endif

            @foreach($group['items'] as $item)
              @php $isActive = $navigation->isActive($item); @endphp
              <a href="{{ $item['url'] }}" class="{{ $isActive ? 'bg-gray-200 text-gray-900 font-medium' : 'text-gray-700 hover:bg-gray-100' }} flex items-center px-3 py-2 text-sm rounded-md">
                <svg class="{{ $isActive ? 'text-gray-900' : 'text-gray-500' }} mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                </svg>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if($item['badge'])
                  <span class="ml-auto px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $item['badge_color'] }}-100 text-{{ $item['badge_color'] }}-700">
                    {{ $item['badge'] }}
                  </span>
                @endif
              </a>
            @endforeach
          @endforeach
        </nav>
      </aside>

      {{-- CONTENT - Scrollable, offset by sidebar width --}}
      <main class="flex-1 ml-56 overflow-y-auto bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {{ $slot }}
        </div>
      </main>

    </div>

 {{-- Trix Attachment Upload Handler --}}
 <script>
  document.addEventListener('trix-attachment-add', function(event) {
      const attachment = event.attachment;
      const file = attachment.file;

      if (file) {
          const formData = new FormData();
          formData.append('attachment', file);
          formData.append('_token', '{{ csrf_token() }}');

          fetch('{{ route("admin.attachments.upload") }}', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              if (data.url) {
                  attachment.setAttributes({
                      url: data.url,
                      href: data.href
                  });
              } else {
                  attachment.remove();
                  alert('Upload failed: ' + (data.error || 'Unknown error'));
              }
          })
          .catch(error => {
              attachment.remove();
              alert('Upload failed: ' + error.message);
          });
      }
  });
</script>

</body>
</html>
