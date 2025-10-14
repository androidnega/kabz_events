<x-vendor-layout>
    <x-slot name="title">Featured Ad Details</x-slot>

    <div class="mb-6">
        <a href="{{ route('vendor.featured-ads.index') }}" class="text-teal-600 hover:text-teal-700">← Back to Featured Ads</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        {{-- Status Badge --}}
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ $featuredAd->headline }}</h2>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                @if($featuredAd->status === 'active') bg-green-100 text-green-800
                @elseif($featuredAd->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($featuredAd->status === 'expired') bg-gray-100 text-gray-800
                @else bg-red-100 text-red-800
                @endif">
                {{ ucfirst($featuredAd->status) }}
            </span>
        </div>

        {{-- Image --}}
        @if($featuredAd->image_path)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $featuredAd->image_path) }}" alt="{{ $featuredAd->headline }}" 
                     class="w-full max-h-64 object-cover rounded-lg">
            </div>
        @endif

        {{-- Details Grid --}}
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Service</h3>
                <p class="text-gray-900">{{ $featuredAd->service->title }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Placement</h3>
                <p class="text-gray-900">{{ ucfirst($featuredAd->placement) }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Start Date</h3>
                <p class="text-gray-900">{{ $featuredAd->start_date->format('M d, Y') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">End Date</h3>
                <p class="text-gray-900">{{ $featuredAd->end_date->format('M d, Y') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Price Paid</h3>
                <p class="text-gray-900 text-lg font-bold">GH₵ {{ number_format($featuredAd->price, 2) }}</p>
            </div>
            @if($featuredAd->payment_ref)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Payment Reference</h3>
                    <p class="text-gray-900 font-mono text-sm">{{ $featuredAd->payment_ref }}</p>
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if($featuredAd->description)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
                <p class="text-gray-900">{{ $featuredAd->description }}</p>
            </div>
        @endif

        {{-- Performance Stats --}}
        @if($featuredAd->status === 'active' || $featuredAd->status === 'expired')
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-3">📊 Performance Statistics</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $featuredAd->views }}</p>
                        <p class="text-sm text-blue-800">Views</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $featuredAd->clicks }}</p>
                        <p class="text-sm text-green-800">Clicks</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ $featuredAd->getCTR() }}%</p>
                        <p class="text-sm text-purple-800">Click-Through Rate</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-vendor-layout>

