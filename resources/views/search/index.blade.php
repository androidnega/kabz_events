<x-app-layout>
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-teal-500 text-white py-12 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Find Trusted Event Vendors in Ghana</h1>
            <p class="text-lg text-purple-100">Search from verified vendors across all regions</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search and Filter Section --}}
        <div class="bg-white p-6 shadow-lg rounded-lg mb-8">
            <form action="{{ route('search.index') }}" method="GET" class="space-y-4">
                {{-- Main Search Bar --}}
                <div class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}" 
                        placeholder="Search for photographers, caterers, decorators..." 
                        class="w-full border-2 border-gray-300 rounded-lg p-4 pr-12 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors"
                    >
                    <svg class="absolute right-4 top-5 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                {{-- Filter Row --}}
                <div class="grid md:grid-cols-5 gap-4">
                    {{-- Category Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" @selected(request('category') == $cat->slug)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Region Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                        <select name="region" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region }}" @selected(request('region') == $region)>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rating Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Rating</label>
                        <select name="min_rating" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="">Any Rating</option>
                            <option value="4.5" @selected(request('min_rating') == '4.5')>4.5+ Stars</option>
                            <option value="4.0" @selected(request('min_rating') == '4.0')>4.0+ Stars</option>
                            <option value="3.5" @selected(request('min_rating') == '3.5')>3.5+ Stars</option>
                            <option value="3.0" @selected(request('min_rating') == '3.0')>3.0+ Stars</option>
                        </select>
                    </div>

                    {{-- Sort Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="rating" @selected(request('sort') == 'rating' || !request('sort'))>Top Rated</option>
                            <option value="recent" @selected(request('sort') == 'recent')>Most Recent</option>
                            <option value="name" @selected(request('sort') == 'name')>Alphabetical</option>
                        </select>
                    </div>

                    {{-- Search Button --}}
                    <div class="flex items-end">
                        <button 
                            type="submit"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
                        >
                            Search
                        </button>
                    </div>
                </div>

                {{-- Advanced Search Link --}}
                <div class="text-center pt-2">
                    <a href="{{ route('search.advanced') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Advanced Search with GPS Location & More Filters
                    </a>
                </div>

                {{-- Clear Filters --}}
                @if(request()->hasAny(['q', 'category', 'region', 'sort']))
                    <div class="text-center">
                        <a href="{{ route('search.index') }}" class="text-sm text-gray-600 hover:text-purple-600 underline">
                            Clear all filters
                        </a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Personalized Recommendations --}}
        @if($showPersonalized && $personalizedVendors->isNotEmpty())
            <div class="bg-gradient-to-r from-purple-50 to-teal-50 border border-purple-200 rounded-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-purple-900 flex items-center gap-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Recommended For You
                        </h3>
                        <p class="text-sm text-gray-600">Based on your search history and preferences</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($personalizedVendors as $vendor)
                        <a href="{{ route('vendors.show', $vendor->slug) }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-4">
                            <h4 class="font-semibold text-sm text-gray-900 mb-1 truncate">{{ $vendor->business_name }}</h4>
                            <div class="flex items-center text-xs text-yellow-600 mb-2">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                {{ number_format($vendor->rating_cached, 1) }}
                            </div>
                            <p class="text-xs text-gray-500 truncate">{{ Str::limit($vendor->address, 30) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Results Count --}}
        <div class="mb-6 flex justify-between items-center">
            <p class="text-gray-700">
                @if($vendors->total() > 0)
                    Showing <span class="font-semibold">{{ $vendors->firstItem() }}-{{ $vendors->lastItem() }}</span> 
                    of <span class="font-semibold">{{ $vendors->total() }}</span> verified vendors
                @else
                    <span class="font-semibold">No vendors found</span>
                @endif
            </p>

            @if(request('q'))
                <p class="text-sm text-gray-600">
                    Search results for: <span class="font-semibold text-purple-600">"{{ request('q') }}"</span>
                </p>
            @endif
        </div>

        {{-- Vendor Cards Grid --}}
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            @forelse($vendors as $vendor)
                <x-vendor-card :vendor="$vendor" />
            @empty
                <div class="col-span-3 text-center py-16">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2">No vendors found matching your search</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your filters or search with different keywords</p>
                    <a href="{{ route('search.index') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                        View All Vendors
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($vendors->hasPages())
            <div class="mb-8">
                {{ $vendors->links() }}
            </div>
        @endif

        {{-- Help Section --}}
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-purple-900 mb-3">💡 Search Tips for Finding the Right Vendor</h3>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span>Use specific keywords like "wedding photography" or "outdoor catering"</span>
                </li>
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span>Filter by your region to find vendors near you in Ghana</span>
                </li>
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span>Check the verified badge (✓) for trusted vendors</span>
                </li>
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span>Sort by "Top Rated" to see vendors with the best reviews</span>
                </li>
                <li class="flex items-start">
                    <span class="text-purple-600 mr-2">•</span>
                    <span>Read reviews from other Ghanaians to make an informed choice</span>
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>

