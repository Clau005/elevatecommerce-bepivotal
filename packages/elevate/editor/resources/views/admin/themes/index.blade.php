<x-app pageTitle="Themes" title="Themes - Admin" description="Manage your themes">
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Themes</h1>
        <a href="{{ route('admin.themes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Register Theme
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($themes as $theme)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden {{ $theme->is_active ? 'ring-2 ring-blue-500' : '' }}">
                {{-- Theme Preview --}}
                @if($theme->preview_image)
                    <img src="{{ $theme->preview_image }}" alt="{{ $theme->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                @endif

                {{-- Theme Info --}}
                <div class="p-6">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $theme->name }}</h3>
                        @if($theme->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @endif
                    </div>

                    @if($theme->description)
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($theme->description, 100) }}</p>
                    @endif

                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        @if($theme->version)
                            <span>v{{ $theme->version }}</span>
                        @endif
                        @if($theme->author)
                            <span>by {{ $theme->author }}</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2">
                        {{-- Customize Button --}}
                        @php
                            $homepage = $theme->pages()->where(function($query) {
                                $query->where('slug', '/')->orWhere('slug', 'homepage');
                            })->first();
                        @endphp
                        <a href="{{ $homepage ? route('admin.visual-editor.pages', ['theme' => $theme->id, 'page' => $homepage->id]) : route('admin.pages.index') }}" 
                           class="block w-full text-center bg-black hover:bg-gray-800 text-white px-3 py-2 rounded text-sm font-medium">
                            Customize
                        </a>

                        <div class="flex gap-2">
                            @if(!$theme->is_active)
                                <form action="{{ route('admin.themes.activate', $theme) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm font-medium">
                                        Activate
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('admin.themes.show', $theme) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-medium">
                                Details
                            </a>
                            
                            <a href="{{ route('admin.themes.edit', $theme) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm font-medium">
                                Edit
                            </a>

                            @if(!$theme->is_active)
                                <form action="{{ route('admin.themes.destroy', $theme) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 px-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No themes</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by registering a theme.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.themes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Register Theme
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

</x-app>
