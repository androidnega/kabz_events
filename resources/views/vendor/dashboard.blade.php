<x-vendor-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-yellow-500 rounded-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-1">Welcome back!</h1>
                <p class="text-purple-100">{{ $vendor->business_name }}</p>
            </div>
            @if($stats['is_verified'])
                <div class="bg-green-500 px-4 py-2 rounded-full font-semibold flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Verified
                </div>
            @else
                <a href="{{ route('vendor.verification') }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded-full font-semibold transition">
                    Get Verified →
                </a>
            @endif
        </div>
    </div>

    {{-- Subscription Banner --}}
    @if($subscriptionInfo)
        <div class="mb-6 bg-gradient-to-r from-purple-500 to-teal-500 rounded-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold mb-1">{{ $subscriptionInfo['plan'] }} Plan Active</h3>
                    @if($subscriptionInfo['ends_at'])
                        <p class="text-sm text-purple-100">
                            Expires: {{ $subscriptionInfo['ends_at']->format('M d, Y') }}
                            @if($subscriptionInfo['days_remaining'] > 0)
                                ({{ $subscriptionInfo['days_remaining'] }} days left)
                            @endif
                        </p>
                    @else
                        <p class="text-sm text-purple-100">Lifetime Access</p>
                    @endif
                </div>
                <a href="{{ route('vendor.subscriptions') }}" 
                   class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition">
                    Upgrade
                </a>
            </div>
        </div>
    @endif

    {{-- Key Metrics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Services -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                    <i class="fas fa-th-large text-blue-600 text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $stats['total_services'] }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600">Total Services</p>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $stats['average_rating'] }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600">Average Rating</p>
        </div>

        <!-- Total Reviews -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                    <i class="fas fa-comments text-green-600 text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $stats['total_reviews'] }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600">Total Reviews</p>
        </div>

        <!-- Verification Status -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center justify-center w-12 h-12 {{ $stats['is_verified'] ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg">
                    <i class="fas {{ $stats['is_verified'] ? 'fa-check-circle text-green-600' : 'fa-clock text-gray-600' }} text-xl"></i>
                </div>
                <span class="text-lg font-bold {{ $stats['is_verified'] ? 'text-green-600' : 'text-gray-600' }}">
                    {{ $stats['verification_status'] }}
                </span>
            </div>
            <p class="text-sm font-medium text-gray-600">Verification</p>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Recent Services --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Services</h3>
                    <a href="{{ route('vendor.services.index') }}" 
                       class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                        View All →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentServices as $service)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <p class="font-medium text-gray-900">{{ $service->title }}</p>
                                <p class="text-sm text-gray-600">{{ $service->category->name }}</p>
                            </div>
                            <span class="text-sm font-semibold text-purple-600">
                                GH₵ {{ number_format($service->price_min, 0) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500 mb-4">No services yet</p>
                            <a href="{{ route('vendor.services.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-plus mr-2"></i>
                                Add Your First Service
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Reviews --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Reviews</h3>

                <div class="space-y-3">
                    @forelse($recentReviews as $review)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <p class="font-medium text-gray-900">{{ $review->user->name }}</p>
                                <div class="flex items-center text-yellow-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }} text-sm"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-gray-700">{{ Str::limit($review->comment, 100) }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">No reviews yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('vendor.services.create') }}" 
               class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition border border-purple-200">
                <i class="fas fa-plus text-purple-600 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-gray-900">Add Service</span>
            </a>

            <a href="{{ route('vendor.subscriptions') }}" 
               class="flex flex-col items-center justify-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition border border-yellow-200">
                <i class="fas fa-crown text-yellow-600 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-gray-900">Upgrade Plan</span>
            </a>

            @if(!$stats['is_verified'])
            <a href="{{ route('vendor.verification') }}" 
               class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition border border-green-200">
                <i class="fas fa-certificate text-green-600 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-gray-900">Get Verified</span>
            </a>
            @endif

            <a href="{{ route('vendors.show', $vendor->slug) }}" 
               class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition border border-blue-200">
                <i class="fas fa-eye text-blue-600 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-gray-900">View Profile</span>
            </a>
        </div>
    </div>
</x-vendor-layout>
