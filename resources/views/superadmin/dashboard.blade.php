<x-app-layout>
    {{-- Super Admin Dashboard Header --}}
    <div class="bg-gradient-to-r from-red-600 to-yellow-500 text-white py-8 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold flex items-center">
                        Super Admin Dashboard
                    </h1>
                    <p class="text-red-100 mt-1">Complete system oversight for KABZS EVENT Ghana</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-red-100">{{ now()->format('l, F j, Y') }}</p>
                    <p class="text-xs text-red-100">{{ now()->format('h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Key Metrics Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Users --}}
            <x-card class="bg-gradient-to-br from-purple-50 to-purple-100 border-l-4 border-purple-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-purple-900">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="text-4xl">👥</div>
                </div>
            </x-card>

            {{-- Total Vendors --}}
            <x-card class="bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Vendors</p>
                        <p class="text-3xl font-bold text-blue-900">{{ $stats['total_vendors'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ $stats['verified_vendors'] }} verified
                        </p>
                    </div>
                    <div class="text-4xl">🏪</div>
                </div>
            </x-card>

            {{-- Pending Verifications --}}
            <x-card class="bg-gradient-to-br from-yellow-50 to-yellow-100 border-l-4 border-yellow-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Verifications</p>
                        <p class="text-3xl font-bold text-yellow-900">{{ $stats['pending_verifications'] }}</p>
                        <a href="{{ route('admin.verifications.index') }}" class="text-xs text-yellow-700 hover:underline">
                            Review now →
                        </a>
                    </div>
                    <div class="text-4xl">⏳</div>
                </div>
            </x-card>

            {{-- Subscription Revenue --}}
            <x-card class="bg-gradient-to-br from-green-50 to-green-100 border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Revenue</p>
                        <p class="text-3xl font-bold text-green-900">
                            GH₵ {{ number_format($stats['subscription_revenue'], 2) }}
                        </p>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ $stats['total_subscriptions'] }} active subscriptions
                        </p>
                    </div>
                    <div class="text-4xl">💰</div>
                </div>
            </x-card>
        </div>

        {{-- Secondary Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-card>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Services & Content</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Services</span>
                        <span class="font-bold text-gray-900">{{ $stats['total_services'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Active Services</span>
                        <span class="font-bold text-green-600">{{ $stats['active_services'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Categories</span>
                        <span class="font-bold text-gray-900">{{ $stats['total_categories'] }}</span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reviews & Ratings</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Reviews</span>
                        <span class="font-bold text-gray-900">{{ $stats['total_reviews'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Pending Reviews</span>
                        <span class="font-bold text-yellow-600">{{ $stats['pending_reviews'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Approval Rate</span>
                        <span class="font-bold text-green-600">
                            {{ $stats['total_reviews'] > 0 ? round(($stats['total_reviews'] / ($stats['total_reviews'] + $stats['pending_reviews'])) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Subscription Breakdown</h3>
                <div class="space-y-3">
                    @foreach($subscriptionBreakdown as $plan)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">{{ $plan->plan }} Plan</span>
                            <div class="text-right">
                                <span class="font-bold text-gray-900">{{ $plan->count }}</span>
                                @if($plan->revenue > 0)
                                    <span class="text-xs text-gray-500 block">
                                        GH₵ {{ number_format($plan->revenue, 0) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        {{-- Recent Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Recent Vendors --}}
            <x-card>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Vendor Registrations</h3>
                <div class="space-y-3">
                    @forelse($recentVendors as $vendor)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <p class="font-medium text-gray-900">{{ $vendor->business_name }}</p>
                                <p class="text-sm text-gray-600">{{ $vendor->user->email }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $vendor->created_at->diffForHumans() }}</p>
                                @if($vendor->is_verified)
                                    <span class="text-xs text-green-600">✓ Verified</span>
                                @else
                                    <span class="text-xs text-gray-400">Unverified</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent registrations</p>
                    @endforelse
                </div>
            </x-card>

            {{-- Pending Verifications --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Pending Verifications</h3>
                    <a href="{{ route('admin.verifications.index') }}" class="text-sm text-purple-600 hover:text-purple-800">
                        View All →
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentVerifications as $request)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                            <div>
                                <p class="font-medium text-gray-900">{{ $request->vendor->business_name }}</p>
                                <p class="text-sm text-gray-600">{{ $request->vendor->phone }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $request->submitted_at->diffForHumans() }}</p>
                                <span class="text-xs px-2 py-1 bg-yellow-200 text-yellow-800 rounded">Pending</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No pending verifications</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        {{-- Quick Actions --}}
        <x-card class="bg-gradient-to-r from-purple-50 to-teal-50 border-2 border-purple-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.verifications.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">✓</span>
                    <span class="text-sm font-medium text-gray-700">Verify Vendors</span>
                </a>
                <a href="{{ route('search.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">🔍</span>
                    <span class="text-sm font-medium text-gray-700">Browse Vendors</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition opacity-50 cursor-not-allowed">
                    <span class="text-3xl mb-2">📊</span>
                    <span class="text-sm font-medium text-gray-700">Analytics</span>
                    <span class="text-xs text-gray-500">(Coming Soon)</span>
                </a>
                <a href="#" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition opacity-50 cursor-not-allowed">
                    <span class="text-3xl mb-2">⚙️</span>
                    <span class="text-sm font-medium text-gray-700">Settings</span>
                    <span class="text-xs text-gray-500">(Coming Soon)</span>
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>

