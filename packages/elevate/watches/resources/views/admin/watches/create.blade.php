<x-app pageTitle="Watches" title="Watches - Admin" description="Create a new watch">
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Add New Watch</h1>
    </div>

    <form action="{{ route('admin.watches.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @include('watches::admin.watches._form', ['watch' => null, 'templates' => $templates])
        
        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('admin.watches.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Create Watch
            </button>
        </div>
    </form>
</div>
</x-app>

