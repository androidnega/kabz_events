<x-app-layout>
    {{-- Client Dashboard Header --}}
    <div class="bg-gradient-to-r from-teal-600 to-blue-500 text-white py-8 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="text-teal-100 mt-1">Find and review trusted event vendors in Ghana</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Key Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-card class="bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">My Reviews</p>
                        <p class="text-3xl font-bold text-blue-900">{{ $stats['total_reviews'] }}</p>
                    </div>
                    <div class="text-4xl">⭐</div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-green-50 to-green-100 border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Approved Reviews</p>
                        <p class="text-3xl font-bold text-green-900">{{ $stats['approved_reviews'] }}</p>
                    </div>
                    <div class="text-4xl">✅</div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-yellow-50 to-yellow-100 border-l-4 border-yellow-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Reviews</p>
                        <p class="text-3xl font-bold text-yellow-900">{{ $stats['pending_reviews'] }}</p>
                    </div>
                    <div class="text-4xl">⏳</div>
                </div>
            </x-card>
        </div>

        {{-- My Reviews --}}
        <x-card class="mb-8">
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
        </x-card>

        {{-- Recommended Vendors --}}
        <x-card class="mb-8">
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
        </x-card>

        {{-- Quick Actions --}}
        <x-card class="bg-teal-50 border-2 border-teal-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('search.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">🔍</span>
                    <span class="text-sm font-medium">Search Vendors</span>
                </a>
                <a href="{{ route('vendors.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">🏪</span>
                    <span class="text-sm font-medium">Browse All</span>
                </a>
                <a href="{{ route('home') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">🏠</span>
                    <span class="text-sm font-medium">Homepage</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">⚙️</span>
                    <span class="text-sm font-medium">My Profile</span>
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>

