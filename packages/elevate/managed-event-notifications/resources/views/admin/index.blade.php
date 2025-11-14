<x-app pageTitle="Managed Notifications" title="Managed Notifications - Admin" description="Manage and preview all customer and staff notifications">

<div class="space-y-8" x-data="notificationManager()">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Notification Management</h1>
        <p class="mt-2 text-gray-600">Manage and preview all customer and staff notifications</p>
    </div>

    <!-- Customer Notifications -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-user text-indigo-600 text-xl mr-3"></i>
                <h2 class="text-xl font-semibold text-gray-900">Customer Notifications</h2>
                <span class="ml-3 text-sm text-gray-500">({{ count($customerNotifications) }} types)</span>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($customerNotifications as $key => $config)
            <div class="px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h3 class="text-lg font-medium text-gray-900">{{ ucwords(str_replace(['.', '_'], ' ', $key)) }}</h3>
                            @if($config['enabled'])
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Enabled
                            </span>
                            @else
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-times-circle mr-1"></i> Disabled
                            </span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ $config['subject'] }}</p>
                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                            <span><i class="fas fa-envelope mr-1"></i> Template: {{ $config['template'] }}</span>
                            <span><i class="fas fa-layer-group mr-1"></i> Channels: {{ implode(', ', $config['channels']) }}</span>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center space-x-2">
                        <a href="{{ route('admin.managed-notifications.preview', $key) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-eye mr-2"></i> Preview
                        </a>
                        <button @click="sendTest('{{ $key }}')" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-paper-plane mr-2"></i> Send Test
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Staff Notifications -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-users-cog text-indigo-600 text-xl mr-3"></i>
                <h2 class="text-xl font-semibold text-gray-900">Staff Notifications</h2>
                <span class="ml-3 text-sm text-gray-500">({{ count($staffNotifications) }} types)</span>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($staffNotifications as $key => $config)
            <div class="px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h3 class="text-lg font-medium text-gray-900">{{ ucwords(str_replace(['.', '_'], ' ', str_replace('staff.', '', $key))) }}</h3>
                            @if($config['enabled'])
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Enabled
                            </span>
                            @else
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-times-circle mr-1"></i> Disabled
                            </span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ $config['subject'] }}</p>
                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                            <span><i class="fas fa-envelope mr-1"></i> Template: {{ $config['template'] }}</span>
                            <span><i class="fas fa-users mr-1"></i> Recipients: {{ $config['recipients'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center space-x-2">
                        <a href="{{ route('admin.managed-notifications.preview', $key) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-eye mr-2"></i> Preview
                        </a>
                        <button @click="sendTest('{{ $key }}')" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-paper-plane mr-2"></i> Send Test
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Test Email Modal -->
    <div x-show="showModal" 
         x-cloak
         class="fixed z-10 inset-0 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="showModal = false"></div>

            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
                        <i class="fas fa-paper-plane text-indigo-600 text-xl"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Send Test Notification
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Enter an email address to receive a test notification
                            </p>
                        </div>
                        <div class="mt-4">
                            <input type="email" 
                                   x-model="testEmail"
                                   placeholder="email@example.com"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="button" 
                            @click="confirmSendTest()"
                            :disabled="sending"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm disabled:opacity-50">
                        <span x-text="sending ? 'Sending...' : 'Send Test'"></span>
                    </button>
                    <button type="button" 
                            @click="showModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationManager() {
    return {
        showModal: false,
        testEmail: '',
        sending: false,
        currentType: null,

        sendTest(type) {
            this.currentType = type;
            this.showModal = true;
            this.testEmail = '';
        },

        async confirmSendTest() {
            if (!this.testEmail) {
                alert('Please enter an email address');
                return;
            }

            this.sending = true;

            try {
                const response = await fetch(`/admin/managed-notifications/test/${this.currentType}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: this.testEmail })
                });

                const data = await response.json();

                if (response.ok) {
                    alert(data.message);
                    this.showModal = false;
                } else {
                    alert(data.error || 'Failed to send test notification');
                }
            } catch (error) {
                alert('An error occurred: ' + error.message);
            } finally {
                this.sending = false;
            }
        }
    }
}
</script>
@endpush

</x-app>
