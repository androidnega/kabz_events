<x-app-layout>
    {{-- Super Admin Dashboard Header --}}
    <div class="bg-gradient-to-r from-red-600 via-red-500 to-yellow-500 text-white py-10 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">
                        🔐 Super Admin Dashboard
                    </h1>
                    <p class="text-red-100 text-sm md:text-base">Complete system oversight for KABZS EVENT</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3 text-right">
                    <p class="text-sm font-medium">{{ now()->format('l, F j, Y') }}</p>
                    <p class="text-xs text-red-100">{{ now()->format('h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Key Metrics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            {{-- Total Users --}}
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-4">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <p class="text-sm font-medium opacity-90">Total Users</p>
                            <p class="text-3xl font-bold mt-1">{{ $stats['total_users'] }}</p>
                        </div>
                        <div class="text-5xl opacity-80">👥</div>
                    </div>
                </div>
                <div class="px-4 py-2 bg-purple-50">
                    <p class="text-xs text-purple-700">All registered users</p>
                </div>
            </div>

            {{-- Total Vendors --}}
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <p class="text-sm font-medium opacity-90">Total Vendors</p>
                            <p class="text-3xl font-bold mt-1">{{ $stats['total_vendors'] }}</p>
                        </div>
                        <div class="text-5xl opacity-80">🏪</div>
                    </div>
                </div>
                <div class="px-4 py-2 bg-blue-50">
                    <p class="text-xs text-blue-700">{{ $stats['verified_vendors'] }} verified</p>
                </div>
            </div>

            {{-- Pending Verifications --}}
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-4">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <p class="text-sm font-medium opacity-90">Pending</p>
                            <p class="text-3xl font-bold mt-1">{{ $stats['pending_verifications'] }}</p>
                        </div>
                        <div class="text-5xl opacity-80">⏳</div>
                    </div>
                </div>
                <div class="px-4 py-2 bg-yellow-50">
                    <a href="{{ route('admin.verifications.index') }}" class="text-xs text-yellow-700 hover:underline font-medium">
                        Review now →
                    </a>
                </div>
            </div>

            {{-- Subscription Revenue --}}
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-4">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <p class="text-sm font-medium opacity-90">Revenue</p>
                            <p class="text-2xl md:text-3xl font-bold mt-1">₵{{ number_format($stats['subscription_revenue'], 0) }}</p>
                        </div>
                        <div class="text-5xl opacity-80">💰</div>
                    </div>
                </div>
                <div class="px-4 py-2 bg-green-50">
                    <p class="text-xs text-green-700">{{ $stats['total_subscriptions'] }} active subscriptions</p>
                </div>
            </div>
        </div>

        {{-- System Configuration & Management --}}
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex items-center mb-6">
                <div class="bg-indigo-100 rounded-lg p-3 mr-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">System Configuration</h2>
                    <p class="text-sm text-gray-600">Manage platform settings, integrations, and configurations</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                {{-- SMS Configuration --}}
                <a href="{{ route('superadmin.sms.test') }}" class="group bg-gradient-to-br from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 rounded-lg p-4 border-2 border-green-200 hover:border-green-400 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="bg-green-500 text-white rounded-lg p-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <span class="text-green-600 text-xs font-medium bg-green-200 px-2 py-1 rounded-full">Active</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-green-700 transition">SMS Settings</h3>
                    <p class="text-xs text-gray-600">Configure Arkassel SMS gateway</p>
                </a>

                {{-- Database Backups --}}
                <a href="{{ route('superadmin.backups.index') }}" class="group bg-gradient-to-br from-blue-50 to-cyan-50 hover:from-blue-100 hover:to-cyan-100 rounded-lg p-4 border-2 border-blue-200 hover:border-blue-400 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="bg-blue-500 text-white rounded-lg p-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                            </svg>
                        </div>
                        <span class="text-blue-600 text-xs font-medium bg-blue-200 px-2 py-1 rounded-full">Secure</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-700 transition">DB Backups</h3>
                    <p class="text-xs text-gray-600">Create and manage backups</p>
                </a>

                {{-- Location Management --}}
                <a href="{{ route('superadmin.locations.index') }}" class="group bg-gradient-to-br from-purple-50 to-pink-50 hover:from-purple-100 hover:to-pink-100 rounded-lg p-4 border-2 border-purple-200 hover:border-purple-400 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="bg-purple-500 text-white rounded-lg p-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="text-purple-600 text-xs font-medium bg-purple-200 px-2 py-1 rounded-full">Ghana</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-purple-700 transition">Locations</h3>
                    <p class="text-xs text-gray-600">Manage regions & districts</p>
                </a>

                {{-- CSV Import --}}
                <a href="{{ route('superadmin.locations.upload') }}" class="group bg-gradient-to-br from-orange-50 to-red-50 hover:from-orange-100 hover:to-red-100 rounded-lg p-4 border-2 border-orange-200 hover:border-orange-400 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="bg-orange-500 text-white rounded-lg p-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <span class="text-orange-600 text-xs font-medium bg-orange-200 px-2 py-1 rounded-full">Import</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-orange-700 transition">CSV Upload</h3>
                    <p class="text-xs text-gray-600">Bulk import locations</p>
                </a>
            </div>
        </div>

        {{-- Content Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8">
            {{-- Services & Content --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Services & Content</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Total Services</span>
                        <span class="text-lg font-bold text-gray-900">{{ $stats['total_services'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Active Services</span>
                        <span class="text-lg font-bold text-green-600">{{ $stats['active_services'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Categories</span>
                        <span class="text-lg font-bold text-gray-900">{{ $stats['total_categories'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Reviews & Ratings --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-100 rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Reviews & Ratings</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Total Reviews</span>
                        <span class="text-lg font-bold text-gray-900">{{ $stats['total_reviews'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Pending Reviews</span>
                        <span class="text-lg font-bold text-yellow-600">{{ $stats['pending_reviews'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Approval Rate</span>
                        <span class="text-lg font-bold text-green-600">
                            {{ $stats['total_reviews'] > 0 ? round(($stats['total_reviews'] / ($stats['total_reviews'] + $stats['pending_reviews'])) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>

            {{-- Subscription Breakdown --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Subscriptions</h3>
                </div>
                <div class="space-y-3">
                    @forelse($subscriptionBreakdown as $plan)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">{{ $plan->plan }} Plan</span>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900">{{ $plan->count }}</span>
                                @if($plan->revenue > 0)
                                    <span class="text-xs text-gray-500 block">₵{{ number_format($plan->revenue, 0) }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No active subscriptions</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-8">
            {{-- Recent Vendors --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Recent Vendor Registrations</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @forelse($recentVendors as $vendor)
                            <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-blue-50 rounded-lg transition">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $vendor->business_name }}</p>
                                    <p class="text-sm text-gray-600 truncate">{{ $vendor->user->email }}</p>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-xs text-gray-500 whitespace-nowrap">{{ $vendor->created_at->diffForHumans() }}</p>
                                    @if($vendor->is_verified)
                                        <span class="inline-block text-xs text-green-600 font-medium">✓ Verified</span>
                                    @else
                                        <span class="inline-block text-xs text-gray-400">Unverified</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">No recent registrations</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Pending Verifications --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Pending Verifications</h3>
                    <a href="{{ route('admin.verifications.index') }}" class="text-sm text-white hover:text-yellow-100 font-medium">
                        View All →
                    </a>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @forelse($recentVerifications as $request)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $request->vendor->business_name }}</p>
                                    <p class="text-sm text-gray-600 truncate">{{ $request->vendor->phone }}</p>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-xs text-gray-500 whitespace-nowrap">{{ $request->submitted_at->diffForHumans() }}</p>
                                    <span class="inline-block text-xs px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full font-medium">Pending</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">No pending verifications</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 rounded-xl shadow-md p-6 border-2 border-indigo-200">
            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <span class="bg-indigo-500 text-white rounded-lg p-2 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </span>
                Quick Actions
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 md:gap-4">
                <a href="{{ route('admin.verifications.index') }}" class="bg-white rounded-lg p-4 hover:shadow-md transition-all duration-200 text-center">
                    <div class="text-3xl mb-2">✓</div>
                    <span class="text-xs font-medium text-gray-700">Verify Vendors</span>
                </a>
                <a href="{{ route('admin.clients.index') }}" class="bg-white rounded-lg p-4 hover:shadow-md transition-all duration-200 text-center">
                    <div class="text-3xl mb-2">👥</div>
                    <span class="text-xs font-medium text-gray-700">Manage Clients</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="bg-white rounded-lg p-4 hover:shadow-md transition-all duration-200 text-center">
                    <div class="text-3xl mb-2">⚠️</div>
                    <span class="text-xs font-medium text-gray-700">View Reports</span>
                </a>
                <a href="{{ route('search.index') }}" class="bg-white rounded-lg p-4 hover:shadow-md transition-all duration-200 text-center">
                    <div class="text-3xl mb-2">🔍</div>
                    <span class="text-xs font-medium text-gray-700">Browse Vendors</span>
                </a>
                <a href="{{ route('vendors.index') }}" class="bg-white rounded-lg p-4 hover:shadow-md transition-all duration-200 text-center">
                    <div class="text-3xl mb-2">🏪</div>
                    <span class="text-xs font-medium text-gray-700">All Vendors</span>
                </a>
                <a href="{{ route('home') }}" class="bg-white rounded-lg p-4 hover:shadow-md transition-all duration-200 text-center">
                    <div class="text-3xl mb-2">🏠</div>
                    <span class="text-xs font-medium text-gray-700">Public Site</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
