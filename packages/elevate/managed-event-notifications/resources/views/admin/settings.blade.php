<x-app pageTitle="Notification Settings" title="Notification Settings - Admin" description="Configure notification preferences and recipients">

<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Notification Settings</h1>
        <p class="mt-2 text-gray-600">Configure notification preferences and recipients</p>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-yellow-800">Configuration File</h3>
                <p class="mt-1 text-sm text-yellow-700">
                    Notification settings are managed in <code class="bg-yellow-100 px-1 rounded">config/managed-notifications.php</code>. 
                    Changes made here will be reflected after updating the configuration file.
                </p>
            </div>
        </div>
    </div>

    <!-- Email Configuration -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Email Configuration</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Provider</label>
                <p class="mt-1 text-sm text-gray-900">{{ $config['email_provider'] ?? 'Not configured' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">From Address</label>
                <p class="mt-1 text-sm text-gray-900">{{ $config['resend']['from']['address'] ?? 'Not configured' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">From Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ $config['resend']['from']['name'] ?? 'Not configured' }}</p>
            </div>
        </div>
    </div>

    <!-- Staff Recipients -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Staff Recipients</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Store Owner</label>
                <p class="mt-1 text-sm text-gray-900">{{ $config['staff_recipients']['store_owner'] ?? 'Not configured' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Order Notifications</label>
                <p class="mt-1 text-sm text-gray-900">
                    @if(!empty($config['staff_recipients']['all_orders']))
                        {{ implode(', ', array_filter($config['staff_recipients']['all_orders'])) }}
                    @else
                        Not configured
                    @endif
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">General Notifications</label>
                <p class="mt-1 text-sm text-gray-900">
                    @if(!empty($config['staff_recipients']['notification_subscribers']))
                        {{ implode(', ', array_filter($config['staff_recipients']['notification_subscribers'])) }}
                    @else
                        Not configured
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Queue Settings -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Queue Settings</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Queue Enabled</label>
                <p class="mt-1 text-sm text-gray-900">
                    @if($config['queue'])
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> Yes
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-times-circle mr-1"></i> No
                        </span>
                    @endif
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Queue Connection</label>
                <p class="mt-1 text-sm text-gray-900">{{ $config['queue_connection'] ?? 'default' }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('admin.managed-notifications.index') }}" 
                   class="block px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <div class="flex items-center">
                        <i class="fas fa-list text-gray-600 mr-3"></i>
                        <div>
                            <div class="text-sm font-medium text-gray-900">View All Notifications</div>
                            <div class="text-xs text-gray-500">Browse and preview all notification types</div>
                        </div>
                    </div>
                </a>
                <a href="https://resend.com/dashboard" target="_blank" 
                   class="block px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                    <div class="flex items-center">
                        <i class="fas fa-external-link-alt text-gray-600 mr-3"></i>
                        <div>
                            <div class="text-sm font-medium text-gray-900">Resend Dashboard</div>
                            <div class="text-xs text-gray-500">View email analytics and delivery stats</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

</x-app>
