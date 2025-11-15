<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ElevateCommerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <div class="min-h-screen">
        <nav class="bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-white">Admin Panel</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-300">{{ auth('admin')->user()->name }}</span>
                        @if(auth('admin')->user()->is_super_admin)
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded">Super Admin</span>
                        @endif
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-red-400 hover:text-red-300">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-white mb-4">Welcome, {{ auth('admin')->user()->first_name }}!</h2>
                        <p class="text-gray-400">This is your admin dashboard.</p>
                        
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-white mb-2">Admin Details:</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-400">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-200">{{ auth('admin')->user()->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-200">{{ auth('admin')->user()->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-400">Role</dt>
                                    <dd class="mt-1 text-sm text-gray-200">
                                        {{ auth('admin')->user()->is_super_admin ? 'Super Admin' : 'Admin' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
