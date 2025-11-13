<x-app pageTitle="Settings" title="Settings - Admin" description="Configure your store settings">

    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
            <p class="text-gray-600 mt-1">Manage your store configuration and preferences</p>
        </div>

        {{-- Settings Sections --}}
        @foreach($sections as $groupName => $groupSections)
            <div>
                {{-- Group Header --}}
                <h2 class="text-lg font-semibold text-gray-900 mb-4 capitalize">
                    {{ str_replace('-', ' ', $groupName) }}
                </h2>

                {{-- Sections Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($groupSections as $section)
                        <a href="{{ $section->url() }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:border-blue-500 hover:shadow-md transition-all group">
                            <div class="flex items-start gap-4">
                                {{-- Icon --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $section->icon() }}"/>
                                    </svg>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{ $section->name() }}
                                        </h3>
                                        @if($section->badge())
                                            @php
                                                $badgeColors = [
                                                    'blue' => 'bg-blue-100 text-blue-700',
                                                    'green' => 'bg-green-100 text-green-700',
                                                    'yellow' => 'bg-yellow-100 text-yellow-700',
                                                    'red' => 'bg-red-100 text-red-700',
                                                    'gray' => 'bg-gray-100 text-gray-700',
                                                ];
                                                $badgeColor = $badgeColors[$section->badgeColor()] ?? 'bg-gray-100 text-gray-700';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badgeColor }}">
                                                {{ $section->badge() }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $section->description() }}
                                    </p>
                                </div>

                                {{-- Arrow --}}
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Empty State --}}
        @if($sections->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No settings sections</h3>
                <p class="mt-1 text-sm text-gray-500">Settings sections will appear here when registered.</p>
            </div>
        @endif
    </div>

</x-app>
