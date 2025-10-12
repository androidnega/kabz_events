<x-client-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">
            Dashboard Overview
        </h2>
        <p class="text-sm text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}!</p>
    </x-slot>

    {{-- Key Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- My Reviews Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">My Reviews</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_reviews'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Approved Reviews Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Approved</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['approved_reviews'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Reviews Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_reviews'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- My Reviews --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">My Recent Reviews</h3>
            @forelse($myReviews as $review)
                <div class="p-4 bg-gray-50 rounded-lg mb-3">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $review->vendor->business_name }}</p>
                            <span class="text-yellow-600 text-sm">
                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            </span>
                        </div>
                        <span class="text-xs px-2 py-1 rounded
                            @if($review->approved) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $review->approved ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                    <p class="text-gray-700 text-sm">{{ $review->comment }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-gray-500 mb-4">You haven't reviewed any vendors yet</p>
                    <a href="{{ route('search.index') }}" class="inline-block bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                        Find Vendors to Review
                    </a>
            </div>
        @endforelse
    </div>

    {{-- Recommended Vendors --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recommended Verified Vendors</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($recommendedVendors as $vendor)
                    <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex justify-between items-start mb-2">
                            <p class="font-semibold text-gray-900">{{ $vendor->business_name }}</p>
                            <span class="text-green-600 text-sm">✓</span>
                        </div>
                        <p class="text-yellow-600 text-sm mb-2">
                            ★ {{ number_format($vendor->rating_cached, 1) }}
                        </p>
                        <a href="{{ route('vendors.show', $vendor->slug) }}" class="text-teal-600 text-sm hover:underline">
                            View Profile →
                        </a>
                    </div>
                @empty
                <p class="text-gray-500 col-span-3 text-center py-4">No recommendations available yet</p>
            @endforelse
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('search.index') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 hover:shadow-md transition border border-gray-200">
                <span class="text-3xl mb-2">🔍</span>
                <span class="text-sm font-medium">Search Vendors</span>
            </a>
            <a href="{{ route('vendors.index') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 hover:shadow-md transition border border-gray-200">
                <span class="text-3xl mb-2">🏪</span>
                <span class="text-sm font-medium">Browse All</span>
            </a>
            <a href="{{ route('home') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 hover:shadow-md transition border border-gray-200">
                <span class="text-3xl mb-2">🏠</span>
                <span class="text-sm font-medium">Homepage</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 hover:shadow-md transition border border-gray-200">
                <span class="text-3xl mb-2">⚙️</span>
                <span class="text-sm font-medium">My Profile</span>
            </a>
        </div>
    </div>
</x-client-layout>

