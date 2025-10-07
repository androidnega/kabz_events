<x-app-layout>
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-teal-500 text-white py-12 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Find Trusted Event Vendors in Ghana 🇬🇭</h1>
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
                <div class="grid md:grid-cols-4 gap-4">
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

