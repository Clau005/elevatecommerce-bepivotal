<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-slate-900 overflow-hidden">
    <!-- Header -->
    @include('core::admin.layouts.partials.header')

    <div class="flex pt-16 h-screen">
        <!-- Sidebar -->
        @include('core::admin.layouts.partials.sidebar')

        <!-- Main Content Area -->
        <main class="flex-1 ml-64 bg-slate-100 rounded-tr-xl overflow-y-auto">
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
