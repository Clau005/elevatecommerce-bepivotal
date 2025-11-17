<x-app pageTitle="Preview Error" title="Preview Error - Admin" description="Failed to render notification template">

<div class="space-y-6">
    <div>
        <a href="{{ route('admin.managed-notifications.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to all notifications
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Preview Error</h1>
    </div>

    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <div class="flex">
            <i class="fas fa-exclamation-circle text-red-600 text-2xl mr-4"></i>
            <div>
                <h3 class="text-lg font-medium text-red-900">Failed to render template</h3>
                <p class="mt-2 text-sm text-red-700">{{ $error }}</p>
                <div class="mt-4">
                    <p class="text-sm font-medium text-red-900">Notification Type:</p>
                    <p class="text-sm text-red-700 font-mono">{{ $type }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Troubleshooting</h2>
        <ul class="list-disc list-inside space-y-2 text-sm text-gray-600">
            <li>Check that the template file exists in <code class="bg-gray-100 px-1 rounded">resources/views/vendor/managed-notifications/</code></li>
            <li>Verify the template path in <code class="bg-gray-100 px-1 rounded">config/managed-notifications.php</code></li>
            <li>Ensure all required variables are available in the template</li>
            <li>Check the Laravel logs for more details: <code class="bg-gray-100 px-1 rounded">storage/logs/laravel.log</code></li>
        </ul>
    </div>
</div>

</x-app>
