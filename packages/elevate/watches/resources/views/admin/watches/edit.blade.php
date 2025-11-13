<x-app pageTitle="Watches" title="Watches - Admin" description="Edit a watch">
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Edit Watch: {{ $watch->name }}</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.watches.update', $watch) }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')
        @include('watches::admin.watches._form', ['watch' => $watch, 'templates' => $templates])
        
        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('admin.watches.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Update Watch
            </button>
        </div>
    </form>
</div>
</x-app>
