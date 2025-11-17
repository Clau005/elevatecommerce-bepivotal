<x-app pageTitle="Preview Notification" title="Preview Notification - Admin" description="Preview how this notification will look to recipients">

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.managed-notifications.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-2 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to all notifications
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ ucwords(str_replace(['.', '_'], ' ', $type)) }}</h1>
            <p class="mt-1 text-sm text-gray-600">Preview how this notification will look to recipients</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($config['enabled'])
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-2"></i> Enabled
            </span>
            @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                <i class="fas fa-times-circle mr-2"></i> Disabled
            </span>
            @endif
        </div>
    </div>

    <!-- Notification Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Notification Details</h2>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Subject Line</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $config['subject'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Template</dt>
                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $config['template'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Channels</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @foreach($config['channels'] as $channel)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                        {{ $channel }}
                    </span>
                    @endforeach
                </dd>
            </div>
            @if(isset($config['recipients']))
            <div>
                <dt class="text-sm font-medium text-gray-500">Recipients</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $config['recipients'] }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <!-- Email Preview -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Email Preview</h2>
            <button onclick="printPreview()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-print mr-2"></i> Print
            </button>
        </div>
        <div class="p-6">
            <!-- Email Container -->
            <div class="max-w-2xl mx-auto bg-gray-50 rounded-lg overflow-hidden" id="email-preview">
                <!-- Email Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="text-sm text-gray-600 mb-1">From: {{ config('managed-notifications.resend.from.name') }} &lt;{{ config('managed-notifications.resend.from.address') }}&gt;</div>
                    <div class="text-sm text-gray-600 mb-1">To: recipient@example.com</div>
                    <div class="text-sm font-semibold text-gray-900">Subject: {{ $config['subject'] }}</div>
                </div>
                
                <!-- Email Body -->
                <div class="bg-white p-6">
                    {!! $html !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Sample Data -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Sample Data Used</h2>
            <p class="mt-1 text-sm text-gray-600">This is the test data used to render the preview</p>
        </div>
        <div class="p-6">
            <pre class="bg-gray-50 rounded-lg p-4 text-sm overflow-x-auto"><code>{{ json_encode($sampleData, JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printPreview() {
    const preview = document.getElementById('email-preview').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Email Preview</title>
            <style>
                body { font-family: sans-serif; padding: 20px; }
            </style>
        </head>
        <body>
            ${preview}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush

</x-app>
