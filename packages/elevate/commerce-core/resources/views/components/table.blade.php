@props([
    'title' => null,
    'description' => null,
    'data' => [],
    'columns' => [],
    'paginator' => null,
    'filters' => null,
    'emptyMessage' => 'No records found.',
])

<div class="space-y-6">
    {{-- Header --}}
    @if($title || $description)
        <div>
            @if($title)
                <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            @endif
            @if($description)
                <p class="text-gray-600 mt-1">{{ $description }}</p>
            @endif
        </div>
    @endif

    {{-- Filters (optional slot) --}}
    @if($filters)
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                {{ $filters }}
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($columns as $column)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $column['label'] ?? '' }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $row)
                        <tr class="hover:bg-gray-50">
                            @foreach($columns as $key => $column)
                                <td class="px-6 py-4 {{ $column['class'] ?? 'text-sm text-gray-900' }}">
                                    @if(isset($column['render']))
                                        {!! $column['render']($row) !!}
                                    @else
                                        {{ $row[$key] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="px-6 py-12 text-center text-sm text-gray-500">
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($paginator && $paginator->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $paginator->links() }}
            </div>
        @endif
    </div>
</div>
