<x-vendor-layout>
    <x-slot name="title">Featured Ads</x-slot>

    {{-- Page Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Featured Ads</h2>
            <p class="text-sm text-gray-600">Boost your visibility with featured advertisements</p>
        </div>
        <a href="{{ route('vendor.featured-ads.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition">
            + Create Featured Ad
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="danger" class="mb-6">
            {{ session('error') }}
        </x-alert>
    @endif

    {{-- VIP Free Ads Banner --}}
    @if($freeAdsRemaining > 0)
        <div class="mb-6 bg-gradient-to-r from-purple-600 to-pink-500 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">🎉 VIP Benefit Active!</h3>
                    <p class="text-lg">You have <strong>{{ $freeAdsRemaining }}</strong> free featured ad(s) remaining this cycle.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-blue-900 mb-2">💡 About Featured Ads</h3>
        <p class="text-sm text-blue-800">
            Boost your visibility! Feature your services to appear at the top of search results and category pages. Choose a service, select a duration, and get noticed by more clients.
        </p>
    </div>

    {{-- Featured Ads List --}}
    @if($featuredAds->isEmpty())
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <div class="text-4xl mb-4">📢</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Featured Ads Yet</h3>
            <p class="text-gray-600 mb-4">Create your first featured ad to boost your visibility!</p>
            <a href="{{ route('vendor.featured-ads.create') }}" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg transition">
                Create Featured Ad
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($featuredAds as $ad)
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 
                    @if($ad->status === 'active') border-green-500
                    @elseif($ad->status === 'pending') border-yellow-500
                    @elseif($ad->status === 'expired') border-gray-400
                    @else border-red-500
                    @endif">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">{{ $ad->headline }}</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($ad->status === 'active') bg-green-100 text-green-800
                                    @elseif($ad->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($ad->status === 'expired') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($ad->status) }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-3">
                                <strong>Service:</strong> {{ $ad->service->title }}
                            </p>
                            
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <span>📍 <strong>Placement:</strong> {{ ucfirst($ad->placement) }}</span>
                                <span>📅 <strong>Duration:</strong> {{ $ad->start_date->format('M d') }} - {{ $ad->end_date->format('M d, Y') }}</span>
                                <span>💰 <strong>Price:</strong> GH₵ {{ number_format($ad->price, 2) }}</span>
                            </div>

                            @if($ad->status === 'active')
                                <div class="flex gap-4 mt-3 text-sm">
                                    <span class="text-blue-600">👁️ {{ $ad->views }} views</span>
                                    <span class="text-green-600">🖱️ {{ $ad->clicks }} clicks</span>
                                    <span class="text-purple-600">📊 CTR: {{ $ad->getCTR() }}%</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            @if($ad->status === 'pending')
                                <a href="{{ route('vendor.featured-ads.edit', $ad->id) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition">
                                    Edit
                                </a>
                                @if($ad->price > 0)
                                    <a href="{{ route('vendor.featured-ads.payment', $ad->id) }}" 
                                       class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm transition">
                                        Pay Now
                                    </a>
                                @endif
                            @endif
                            
                            <a href="{{ route('vendor.featured-ads.show', $ad->id) }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded text-sm transition">
                                View
                            </a>

                            @if(in_array($ad->status, ['pending', 'expired']))
                                <form action="{{ route('vendor.featured-ads.destroy', $ad->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this ad?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm transition">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $featuredAds->links() }}
        </div>
    @endif
</x-vendor-layout>

