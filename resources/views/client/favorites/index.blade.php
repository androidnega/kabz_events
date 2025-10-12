<x-client-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">My Favorites</h2>
        <p class="text-sm text-gray-600 mt-1">Vendors you've bookmarked</p>
    </x-slot>

    @if($favorites->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($favorites as $bookmark)
                @php
                    $vendor = $bookmark->vendor;
                @endphp
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    {{-- Vendor Image --}}
                    <div class="h-48 bg-gradient-to-br from-teal-400 to-blue-500 relative">
                        @if($vendor->logo)
                            <img src="{{ asset('storage/' . $vendor->logo) }}" alt="{{ $vendor->business_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-white text-5xl font-bold">{{ strtoupper(substr($vendor->business_name, 0, 1)) }}</span>
                            </div>
                        @endif
                        
                        {{-- Verified Badge --}}
                        @if($vendor->is_verified)
                            <div class="absolute top-3 right-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Verified
                            </div>
                        @endif
                    </div>

                    {{-- Vendor Info --}}
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $vendor->business_name }}</h3>
                        
                        @if($vendor->category)
                            <p class="text-sm text-teal-600 mb-2">{{ $vendor->category->name }}</p>
                        @endif

                        <div class="flex items-center mb-3">
                            <div class="flex items-center text-yellow-500 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($vendor->rating_cached))
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">({{ $vendor->rating_cached ? number_format($vendor->rating_cached, 1) : '0.0' }})</span>
                        </div>

                        @if($vendor->city)
                            <p class="text-sm text-gray-600 mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $vendor->city }}
                            </p>
                        @endif

                        <div class="flex space-x-2">
                            <a href="{{ route('vendors.show', $vendor->slug) }}" class="flex-1 text-center bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition text-sm font-medium">
                                View Profile
                            </a>
                            <form action="{{ route('client.favorites.destroy', $vendor->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-600 px-4 py-2 rounded-lg hover:bg-red-200 transition text-sm font-medium">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </path>
                                </button>
                            </form>
                        </div>

                        <p class="text-xs text-gray-400 mt-3">Added {{ $bookmark->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-700 mb-2">No Favorites Yet</h3>
            <p class="text-gray-600 mb-6">Start exploring and bookmark your favorite vendors!</p>
            <a href="{{ route('vendors.index') }}" class="inline-block bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition font-medium">
                Browse Vendors
            </a>
        </div>
    @endif
</x-client-layout>

