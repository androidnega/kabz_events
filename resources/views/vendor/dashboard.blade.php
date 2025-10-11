<x-vendor-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Welcome Header --}}
    <div class="bg-purple-50 rounded-lg p-4 sm:p-6 mb-4 sm:mb-6 border border-purple-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-1">Welcome back!</h1>
                <p class="text-sm sm:text-base text-gray-600">{{ $vendor->business_name }}</p>
            </div>
            @if($stats['is_verified'])
                <div class="bg-green-50 border border-green-200 px-3 sm:px-4 py-2 rounded-lg font-medium flex items-center text-green-700 text-sm sm:text-base self-start sm:self-auto">
                    <i class="fas fa-check-circle mr-2"></i>
                    Verified
                </div>
            @else
                <a href="{{ route('vendor.verification') }}" 
                   class="bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 px-3 sm:px-4 py-2 rounded-lg font-medium transition text-yellow-700 text-sm sm:text-base inline-flex items-center self-start sm:self-auto">
                    Get Verified →
                </a>
            @endif
        </div>
    </div>

    {{-- Subscription Banner --}}
    @if($subscriptionInfo)
        <div class="mb-4 sm:mb-6 bg-blue-50 border border-blue-100 rounded-lg p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-1">{{ $subscriptionInfo['plan'] }} Plan Active</h3>
                    @if($subscriptionInfo['ends_at'])
                        <p class="text-sm text-gray-600">
                            Expires: {{ $subscriptionInfo['ends_at']->format('M d, Y') }}
                            @if($subscriptionInfo['days_remaining'] > 0)
                                ({{ $subscriptionInfo['days_remaining'] }} days left)
                            @endif
                        </p>
                    @else
                        <p class="text-sm text-gray-600">Lifetime Access</p>
                    @endif
                </div>
                <a href="{{ route('vendor.subscriptions') }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm sm:text-base self-start sm:self-auto">
                    Upgrade
                </a>
            </div>
        </div>
    @endif

    {{-- Key Metrics Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
        <!-- Total Services -->
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-center w-10 h-10 bg-blue-50 rounded-lg mb-2 mx-auto">
                <i class="fas fa-th-large text-blue-600 text-lg"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 text-center mb-1">{{ $stats['total_services'] }}</p>
            <p class="text-xs font-medium text-gray-600 text-center">Services</p>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-center w-10 h-10 bg-amber-50 rounded-lg mb-2 mx-auto">
                <i class="fas fa-star text-amber-600 text-lg"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 text-center mb-1">{{ $stats['average_rating'] }}</p>
            <p class="text-xs font-medium text-gray-600 text-center">Rating</p>
        </div>

        <!-- Total Reviews -->
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-center w-10 h-10 bg-emerald-50 rounded-lg mb-2 mx-auto">
                <i class="fas fa-comments text-emerald-600 text-lg"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 text-center mb-1">{{ $stats['total_reviews'] }}</p>
            <p class="text-xs font-medium text-gray-600 text-center">Reviews</p>
        </div>

        <!-- Verification Status -->
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-center w-10 h-10 {{ $stats['is_verified'] ? 'bg-green-50' : 'bg-gray-50' }} rounded-lg mb-2 mx-auto">
                <i class="fas {{ $stats['is_verified'] ? 'fa-check-circle text-green-600' : 'fa-clock text-gray-500' }} text-lg"></i>
            </div>
            <p class="text-sm font-bold {{ $stats['is_verified'] ? 'text-green-600' : 'text-gray-600' }} text-center mb-1">
                {{ $stats['verification_status'] }}
            </p>
            <p class="text-xs font-medium text-gray-600 text-center">Status</p>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
        {{-- Recent Services --}}
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Recent Services</h3>
                    <a href="{{ route('vendor.services.index') }}" 
                       class="text-xs sm:text-sm text-purple-600 hover:text-purple-700 font-medium">
                        View All →
                    </a>
                </div>

                <div class="space-y-2 sm:space-y-3">
                    @forelse($recentServices as $service)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="min-w-0 flex-1 pr-3">
                                <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $service->title }}</p>
                                <p class="text-xs sm:text-sm text-gray-600">{{ $service->category->name }}</p>
                            </div>
                            <span class="text-xs sm:text-sm font-semibold text-purple-600 whitespace-nowrap">
                                GH₵ {{ number_format($service->price_min, 0) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 sm:py-8">
                            <i class="fas fa-inbox text-gray-300 text-3xl sm:text-4xl mb-3"></i>
                            <p class="text-sm sm:text-base text-gray-500 mb-4">No services yet</p>
                            <a href="{{ route('vendor.services.create') }}" 
                               class="inline-flex items-center px-3 sm:px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                                <i class="fas fa-plus mr-2"></i>
                                Add Your First Service
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Reviews --}}
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Recent Reviews</h3>

                <div class="space-y-2 sm:space-y-3">
                    @forelse($recentReviews as $review)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-start mb-2 gap-2">
                                <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $review->user->name }}</p>
                                <div class="flex items-center text-amber-500 flex-shrink-0">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }} text-xs"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-700">{{ Str::limit($review->comment, 100) }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="text-center py-6 sm:py-8">
                            <i class="fas fa-comments text-gray-300 text-3xl sm:text-4xl mb-3"></i>
                            <p class="text-sm sm:text-base text-gray-500">No reviews yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <a href="{{ route('vendor.services.create') }}" 
               class="flex flex-col items-center justify-center p-3 sm:p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition border border-purple-100">
                <i class="fas fa-plus text-purple-600 text-xl sm:text-2xl mb-1 sm:mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-gray-900 text-center">Add Service</span>
            </a>

            <a href="{{ route('vendor.subscriptions') }}" 
               class="flex flex-col items-center justify-center p-3 sm:p-4 bg-amber-50 rounded-lg hover:bg-amber-100 transition border border-amber-100">
                <i class="fas fa-crown text-amber-600 text-xl sm:text-2xl mb-1 sm:mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-gray-900 text-center">Upgrade Plan</span>
            </a>

            @if(!$stats['is_verified'])
            <a href="{{ route('vendor.verification') }}" 
               class="flex flex-col items-center justify-center p-3 sm:p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition border border-emerald-100">
                <i class="fas fa-certificate text-emerald-600 text-xl sm:text-2xl mb-1 sm:mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-gray-900 text-center">Get Verified</span>
            </a>
            @endif

            <a href="{{ route('vendors.show', $vendor->slug) }}" 
               class="flex flex-col items-center justify-center p-3 sm:p-4 bg-sky-50 rounded-lg hover:bg-sky-100 transition border border-sky-100">
                <i class="fas fa-eye text-sky-600 text-xl sm:text-2xl mb-1 sm:mb-2"></i>
                <span class="text-xs sm:text-sm font-medium text-gray-900 text-center">View Profile</span>
            </a>
        </div>
    </div>
</x-vendor-layout>
