<x-app-layout>
    {{-- Vendor Dashboard Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-yellow-500 text-white py-8 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Welcome, {{ $vendor->business_name }}!</h1>
                    <p class="text-purple-100 mt-1">Manage your event services business</p>
                </div>
                @if($stats['is_verified'])
                    <div class="bg-green-500 px-4 py-2 rounded-full text-white font-semibold">
                        ✓ Verified Vendor
                    </div>
                @else
                    <a href="{{ route('vendor.verification') }}" class="bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded-full text-white font-semibold transition">
                        Get Verified →
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Subscription Banner --}}
        @if($subscriptionInfo)
            <div class="mb-6 bg-gradient-to-r from-purple-500 to-teal-500 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">{{ $subscriptionInfo['plan'] }} Plan Active</h3>
                        @if($subscriptionInfo['ends_at'])
                            <p class="text-purple-100">
                                Expires: {{ $subscriptionInfo['ends_at']->format('M d, Y') }}
                                @if($subscriptionInfo['days_remaining'] > 0)
                                    ({{ $subscriptionInfo['days_remaining'] }} days remaining)
                                @endif
                            </p>
                        @else
                            <p class="text-purple-100">Lifetime Access</p>
                        @endif
                    </div>
                    <a href="{{ route('vendor.subscriptions') }}" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition">
                        Upgrade Plan
                    </a>
                </div>
            </div>
        @else
            <x-alert type="info" class="mb-6">
                <div class="flex items-center justify-between">
                    <span>You're on the Free plan. Upgrade for more features!</span>
                    <a href="{{ route('vendor.subscriptions') }}" class="font-semibold underline">View Plans →</a>
                </div>
            </x-alert>
        @endif

        {{-- Key Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-card class="bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Services</p>
                        <p class="text-3xl font-bold text-blue-900">{{ $stats['total_services'] }}</p>
                    </div>
                    <div class="text-4xl">📦</div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-yellow-50 to-yellow-100 border-l-4 border-yellow-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Average Rating</p>
                        <p class="text-3xl font-bold text-yellow-900">{{ $stats['average_rating'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">★★★★★</p>
                    </div>
                    <div class="text-4xl">⭐</div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-green-50 to-green-100 border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                        <p class="text-3xl font-bold text-green-900">{{ $stats['total_reviews'] }}</p>
                    </div>
                    <div class="text-4xl">💬</div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-purple-50 to-purple-100 border-l-4 border-purple-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Verification</p>
                        <p class="text-lg font-bold 
                            @if($stats['is_verified']) text-green-900 @else text-gray-600 @endif">
                            {{ $stats['verification_status'] }}
                        </p>
                    </div>
                    <div class="text-4xl">
                        @if($stats['is_verified']) ✅ @else ⏳ @endif
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Recent Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Recent Services --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Services</h3>
                    <a href="{{ route('vendor.services.index') }}" class="text-sm text-purple-600 hover:underline">
                        Manage All →
                    </a>
                </div>
                @forelse($recentServices as $service)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded mb-2">
                        <div>
                            <p class="font-medium">{{ $service->title }}</p>
                            <p class="text-sm text-gray-600">{{ $service->category->name }}</p>
                        </div>
                        <span class="text-sm font-semibold text-purple-600">
                            GH₵ {{ number_format($service->price_min, 0) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">No services yet</p>
                        <a href="{{ route('vendor.services.create') }}" class="inline-block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                            Add Your First Service
                        </a>
                    </div>
                @endforelse
            </x-card>

            {{-- Recent Reviews --}}
            <x-card>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Reviews</h3>
                @forelse($recentReviews as $review)
                    <div class="p-3 bg-gray-50 rounded mb-2">
                        <div class="flex justify-between items-start mb-1">
                            <p class="font-medium">{{ $review->user->name }}</p>
                            <span class="text-yellow-600 text-sm">
                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700">{{ Str::limit($review->comment, 100) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No reviews yet</p>
                @endforelse
            </x-card>
        </div>

        {{-- Quick Actions --}}
        <x-card class="bg-purple-50 border-2 border-purple-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('vendor.services.create') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">➕</span>
                    <span class="text-sm font-medium">Add Service</span>
                </a>
                <a href="{{ route('vendor.subscriptions') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">💎</span>
                    <span class="text-sm font-medium">Upgrade Plan</span>
                </a>
                @if(!$stats['is_verified'])
                <a href="{{ route('vendor.verification') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">✓</span>
                    <span class="text-sm font-medium">Get Verified</span>
                </a>
                @endif
                <a href="{{ route('vendors.show', $vendor->slug) }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">👁️</span>
                    <span class="text-sm font-medium">View Public Profile</span>
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>
