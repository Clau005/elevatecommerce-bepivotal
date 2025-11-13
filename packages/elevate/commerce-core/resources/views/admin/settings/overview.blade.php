<x-app pageTitle="Settings" title="Settings - Admin" description="Configure your application settings and preferences">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- Staff Management --}}
        <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/staff'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                            Staff Management
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Manage staff accounts, roles, and permissions
                        </p>
                        <x-bladewind::button size="small" color="blue" outline="true" onclick="window.location.href='/admin/settings/staff'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card>

        {{-- Customer Groups --}}
        <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/customer-groups'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-green-50 text-green-600 group-hover:bg-green-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">
                            Customer Groups
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Create and manage customer groups for targeted pricing and promotions
                        </p>
                        <x-bladewind::button size="small" color="green" outline="true" onclick="window.location.href='/admin/settings/customer-groups'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card>

        {{-- Roles & Permissions --}}
        <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/roles'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-orange-50 text-orange-600 group-hover:bg-orange-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
                            Roles & Permissions
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Manage staff roles and their specific permissions
                        </p>
                        <x-bladewind::button size="small" color="orange" outline="true" onclick="window.location.href='/admin/settings/roles'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card>

        {{-- Countries & Regions --}}
        <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/countries'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-purple-50 text-purple-600 group-hover:bg-purple-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors">
                            Countries & Regions
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Manage countries, states, and regional settings
                        </p>
                        <x-bladewind::button size="small" color="purple" outline="true" onclick="window.location.href='/admin/settings/countries'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card>

        {{-- Currencies --}}
        <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/currencies'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-yellow-50 text-yellow-600 group-hover:bg-yellow-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-yellow-600 transition-colors">
                            Currencies
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Configure supported currencies and exchange rates
                        </p>
                        <x-bladewind::button size="small" color="yellow" outline="true" onclick="window.location.href='/admin/settings/currencies'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card>

        {{-- Languages --}}
        <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/languages'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                            Languages
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Manage supported languages and localization settings
                        </p>
                        <x-bladewind::button size="small" color="indigo" outline="true" onclick="window.location.href='/admin/settings/languages'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card>

        {{-- Product Options --}}
        {{-- <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/product-options'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-red-50 text-red-600 group-hover:bg-red-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-red-600 transition-colors">
                            Product Options
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Set up product variants like color, size, material, etc.
                        </p>
                        <x-bladewind::button size="small" color="red" outline="true" onclick="window.location.href='/admin/settings/product-options'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card> --}}

        {{-- Tags --}}
        {{-- <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/tags'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-gray-50 text-gray-600 group-hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-gray-600 transition-colors">
                            Tags
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Manage product tags and categories for better organization
                        </p>
                        <x-bladewind::button size="small" color="gray" outline="true" onclick="window.location.href='/admin/settings/tags'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card> --}}

        {{-- Discounts --}}
        <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/discounts'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-green-50 text-green-600 group-hover:bg-green-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">
                            Discounts
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Create and manage coupons, promotions, and automatic discounts
                        </p>
                        <x-bladewind::button size="small" color="green" outline="true" onclick="window.location.href='/admin/settings/discounts'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card>

        {{-- Gift Vouchers --}}
        <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/settings/gift-vouchers'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-pink-50 text-pink-600 group-hover:bg-pink-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-pink-600 transition-colors">
                            Gift Vouchers
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Create and manage gift vouchers with usage tracking and validation
                        </p>
                        <x-bladewind::button size="small" color="pink" outline="true" onclick="window.location.href='/admin/settings/gift-vouchers'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card>

        {{-- Blog Management --}}
        {{-- <x-bladewind::card class="hover:shadow-lg transition-shadow cursor-pointer group" onclick="window.location.href='/admin/blogs'">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                            Blog Management
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Create and manage blog posts with comprehensive SEO features
                        </p>
                        <x-bladewind::button size="small" color="blue" outline="true" onclick="window.location.href='/admin/blogs'">
                            Configure
                        </x-bladewind::button>
                    </div>
                </div>
            </div>
        </x-bladewind::card> --}}

    </div>

    {{-- Additional styling for enhanced hover effects --}}
    <style>
        .group:hover {
            transform: translateY(-2px);
            transition: all 0.2s ease-in-out;
        }
        
        .group .transition-colors {
            transition: all 0.2s ease-in-out;
        }
    </style>

</x-app>