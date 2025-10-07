<x-app-layout>
    {{-- Super Admin Dashboard Header --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-shield-alt text-indigo-600 mr-2"></i>Super Admin Dashboard
                    </h1>
                    <p class="text-gray-600">Complete system oversight and management</p>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
                    <p class="text-sm font-medium text-gray-700">
                        <i class="far fa-calendar text-indigo-600 mr-2"></i>{{ now()->format('l, F j, Y') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="far fa-clock text-gray-400 mr-2"></i>{{ now()->format('h:i A') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Key Metrics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Users --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                            <p class="text-xs text-gray-500 mt-2">All registered accounts</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-4">
                            <i class="fas fa-users text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Vendors --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Vendors</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_vendors'] }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $stats['verified_vendors'] }} verified</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-4">
                            <i class="fas fa-store text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Verifications --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Pending Verifications</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_verifications'] }}</p>
                            <a href="{{ route('admin.verifications.index') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium mt-2 inline-block">
                                Review now <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="bg-amber-100 rounded-full p-4">
                            <i class="fas fa-clock text-2xl text-amber-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subscription Revenue --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 mb-1">Active Revenue</p>
                            <p class="text-3xl font-bold text-gray-900">₵{{ number_format($stats['subscription_revenue'], 0) }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $stats['total_subscriptions'] }} subscriptions</p>
                        </div>
                        <div class="bg-emerald-100 rounded-full p-4">
                            <i class="fas fa-money-bill-wave text-2xl text-emerald-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Configuration Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                <div class="bg-indigo-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-cog text-xl text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">System Configuration</h2>
                    <p class="text-sm text-gray-600">Manage platform settings and integrations</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- SMS Configuration --}}
                <a href="{{ route('superadmin.sms.test') }}" class="group bg-emerald-50 hover:bg-emerald-100 border-2 border-emerald-200 hover:border-emerald-300 rounded-lg p-5 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-emerald-500 text-white rounded-lg p-2.5">
                            <i class="fas fa-sms text-lg"></i>
                        </div>
                        <span class="text-emerald-700 text-xs font-semibold bg-emerald-200 px-2.5 py-1 rounded-full">Active</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-emerald-700 transition">SMS Settings</h3>
                    <p class="text-xs text-gray-600">Arkassel gateway config</p>
                </a>

                {{-- Database Backups --}}
                <a href="{{ route('superadmin.backups.index') }}" class="group bg-blue-50 hover:bg-blue-100 border-2 border-blue-200 hover:border-blue-300 rounded-lg p-5 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-blue-500 text-white rounded-lg p-2.5">
                            <i class="fas fa-database text-lg"></i>
                        </div>
                        <span class="text-blue-700 text-xs font-semibold bg-blue-200 px-2.5 py-1 rounded-full">Secure</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-blue-700 transition">Database Backups</h3>
                    <p class="text-xs text-gray-600">Create & manage backups</p>
                </a>

                {{-- Location Management --}}
                <a href="{{ route('superadmin.locations.index') }}" class="group bg-purple-50 hover:bg-purple-100 border-2 border-purple-200 hover:border-purple-300 rounded-lg p-5 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-purple-500 text-white rounded-lg p-2.5">
                            <i class="fas fa-map-marked-alt text-lg"></i>
                        </div>
                        <span class="text-purple-700 text-xs font-semibold bg-purple-200 px-2.5 py-1 rounded-full">Ghana</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-purple-700 transition">Locations</h3>
                    <p class="text-xs text-gray-600">Regions & districts</p>
                </a>

                {{-- CSV Import --}}
                <a href="{{ route('superadmin.locations.upload') }}" class="group bg-orange-50 hover:bg-orange-100 border-2 border-orange-200 hover:border-orange-300 rounded-lg p-5 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-orange-500 text-white rounded-lg p-2.5">
                            <i class="fas fa-file-upload text-lg"></i>
                        </div>
                        <span class="text-orange-700 text-xs font-semibold bg-orange-200 px-2.5 py-1 rounded-full">Import</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 group-hover:text-orange-700 transition">CSV Upload</h3>
                    <p class="text-xs text-gray-600">Bulk import locations</p>
                </a>
            </div>
        </div>

        {{-- Content Statistics --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Services & Content --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4 pb-3 border-b border-gray-200">
                    <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                        <i class="fas fa-boxes text-indigo-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Services & Content</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Total Services</span>
                        <span class="text-lg font-bold text-gray-900">{{ $stats['total_services'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Active Services</span>
                        <span class="text-lg font-bold text-emerald-600">{{ $stats['active_services'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Categories</span>
                        <span class="text-lg font-bold text-gray-900">{{ $stats['total_categories'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Reviews & Ratings --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4 pb-3 border-b border-gray-200">
                    <div class="bg-amber-100 rounded-lg p-2 mr-3">
                        <i class="fas fa-star text-amber-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Reviews & Ratings</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Total Reviews</span>
                        <span class="text-lg font-bold text-gray-900">{{ $stats['total_reviews'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Pending Reviews</span>
                        <span class="text-lg font-bold text-amber-600">{{ $stats['pending_reviews'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Approval Rate</span>
                        <span class="text-lg font-bold text-emerald-600">
                            {{ $stats['total_reviews'] > 0 ? round(($stats['total_reviews'] / ($stats['total_reviews'] + $stats['pending_reviews'])) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>

            {{-- Subscription Breakdown --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4 pb-3 border-b border-gray-200">
                    <div class="bg-emerald-100 rounded-lg p-2 mr-3">
                        <i class="fas fa-chart-pie text-emerald-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Subscriptions</h3>
                </div>
                <div class="space-y-3">
                    @forelse($subscriptionBreakdown as $plan)
                        <div class="flex justify-between items-center py-2">
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Recent Vendors --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-store text-blue-600 mr-3"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Vendor Registrations</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @forelse($recentVendors as $vendor)
                            <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-blue-50 rounded-lg transition">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $vendor->business_name }}</p>
                                    <p class="text-sm text-gray-600 truncate">{{ $vendor->user->email }}</p>
                                </div>
                                <div class="text-right ml-4 flex-shrink-0">
                                    <p class="text-xs text-gray-500 whitespace-nowrap">{{ $vendor->created_at->diffForHumans() }}</p>
                                    @if($vendor->is_verified)
                                        <span class="inline-flex items-center text-xs text-emerald-700 font-medium mt-1">
                                            <i class="fas fa-check-circle mr-1"></i>Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-xs text-gray-500 mt-1">
                                            <i class="fas fa-circle text-xs mr-1"></i>Unverified
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-store text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">No recent registrations</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Pending Verifications --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-amber-600 mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-900">Pending Verifications</h3>
                        </div>
                        <a href="{{ route('admin.verifications.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @forelse($recentVerifications as $request)
                            <div class="flex items-center justify-between p-3 bg-amber-50 hover:bg-amber-100 rounded-lg transition">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $request->vendor->business_name }}</p>
                                    <p class="text-sm text-gray-600 truncate">{{ $request->vendor->phone }}</p>
                                </div>
                                <div class="text-right ml-4 flex-shrink-0">
                                    <p class="text-xs text-gray-500 whitespace-nowrap">{{ $request->submitted_at->diffForHumans() }}</p>
                                    <span class="inline-flex items-center text-xs px-2 py-1 bg-amber-200 text-amber-800 rounded-full font-medium mt-1">
                                        <i class="fas fa-hourglass-half mr-1"></i>Pending
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-check-double text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">No pending verifications</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                <div class="bg-indigo-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-bolt text-xl text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Quick Actions</h2>
                    <p class="text-sm text-gray-600">Frequently used admin operations</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="{{ route('admin.verifications.index') }}" class="bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-lg p-4 text-center transition-all group">
                    <i class="fas fa-user-check text-3xl text-gray-400 group-hover:text-indigo-600 mb-2 transition"></i>
                    <span class="text-xs font-medium text-gray-700 group-hover:text-indigo-700 block transition">Verify Vendors</span>
                </a>
                <a href="{{ route('admin.clients.index') }}" class="bg-gray-50 hover:bg-purple-50 border border-gray-200 hover:border-purple-300 rounded-lg p-4 text-center transition-all group">
                    <i class="fas fa-users text-3xl text-gray-400 group-hover:text-purple-600 mb-2 transition"></i>
                    <span class="text-xs font-medium text-gray-700 group-hover:text-purple-700 block transition">Manage Clients</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-300 rounded-lg p-4 text-center transition-all group">
                    <i class="fas fa-flag text-3xl text-gray-400 group-hover:text-red-600 mb-2 transition"></i>
                    <span class="text-xs font-medium text-gray-700 group-hover:text-red-700 block transition">View Reports</span>
                </a>
                <a href="{{ route('search.index') }}" class="bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded-lg p-4 text-center transition-all group">
                    <i class="fas fa-search text-3xl text-gray-400 group-hover:text-blue-600 mb-2 transition"></i>
                    <span class="text-xs font-medium text-gray-700 group-hover:text-blue-700 block transition">Browse Vendors</span>
                </a>
                <a href="{{ route('vendors.index') }}" class="bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-lg p-4 text-center transition-all group">
                    <i class="fas fa-store-alt text-3xl text-gray-400 group-hover:text-emerald-600 mb-2 transition"></i>
                    <span class="text-xs font-medium text-gray-700 group-hover:text-emerald-700 block transition">All Vendors</span>
                </a>
                <a href="{{ route('home') }}" class="bg-gray-50 hover:bg-amber-50 border border-gray-200 hover:border-amber-300 rounded-lg p-4 text-center transition-all group">
                    <i class="fas fa-home text-3xl text-gray-400 group-hover:text-amber-600 mb-2 transition"></i>
                    <span class="text-xs font-medium text-gray-700 group-hover:text-amber-700 block transition">Public Site</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
