<x-app-layout>
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-teal-500 text-white py-12 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Find Trusted Event Vendors in Ghana</h1>
            <p class="text-lg text-purple-100">Search from verified vendors across all regions - Live Results</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search and Filter Section --}}
        <div class="bg-white p-6 shadow-lg rounded-lg mb-8">
            {{-- Hidden form fields for AJAX --}}
            <input type="hidden" id="selectedRegion" value="{{ request('region') }}">
            <input type="hidden" id="selectedDistrict" value="{{ request('district') }}">
            <input type="hidden" id="gpsLat" value="{{ request('lat') }}">
            <input type="hidden" id="gpsLng" value="{{ request('lng') }}">
            <input type="hidden" id="searchRadius" value="{{ request('radius', 50) }}">

            <div class="space-y-4">
                {{-- Main Search Bar --}}
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchKeyword"
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
                        <select id="categoryFilter" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" @selected(request('category') == $cat->slug)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Location Filter (Opens Modal) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <button 
                            type="button"
                            onclick="openLocationModal()"
                            class="w-full border-2 border-gray-300 rounded-lg p-3 text-left hover:border-purple-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors flex items-center justify-between"
                        >
                            <span id="locationDisplay" class="text-gray-400">Select Location</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Rating Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Rating</label>
                        <select id="ratingFilter" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
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
                        <select id="sortFilter" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="rating" @selected(request('sort') == 'rating' || !request('sort'))>Top Rated</option>
                            <option value="recent" @selected(request('sort') == 'recent')>Most Recent</option>
                            <option value="name" @selected(request('sort') == 'name')>Alphabetical</option>
                            <option value="distance" @selected(request('sort') == 'distance')>Nearest First</option>
                        </select>
                    </div>

                    {{-- Clear Filters Button --}}
                    <div class="flex items-end">
                        <button 
                            type="button"
                            onclick="clearAllFilters()"
                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors duration-200"
                        >
                            Clear All
                        </button>
                    </div>
                </div>

                {{-- Advanced Search Link --}}
                <div class="text-center pt-2">
                    <a href="{{ route('search.advanced') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        Advanced Search with GPS & More Options
                    </a>
                </div>
            </div>
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

        {{-- Loading Indicator --}}
        <div id="loadingIndicator" class="hidden text-center py-12">
            <svg class="animate-spin h-12 w-12 text-purple-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 font-medium">Searching for vendors...</p>
        </div>

        {{-- Results Count --}}
        <div id="resultsCount" class="mb-6 flex justify-between items-center">
            <p class="text-gray-700">
                <span id="resultText">Loading results...</span>
            </p>
        </div>

        {{-- Vendor Cards Grid --}}
        <div id="vendorResults" class="grid md:grid-cols-3 gap-6 mb-8">
            {{-- Results will be loaded here via AJAX --}}
        </div>
    </div>

    {{-- Include Location Modal Component --}}
    <x-location-modal />

    {{-- Live Search JavaScript --}}
    <script>
        let searchTimeout = null;

        // Perform live search
        function performLiveSearch() {
            // Clear any pending search
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Show loading indicator
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('vendorResults').style.opacity = '0.5';

            // Get filter values
            const params = {
                q: document.getElementById('searchKeyword').value,
                category: document.getElementById('categoryFilter').value,
                region: document.getElementById('selectedRegion').value,
                district: document.getElementById('selectedDistrict').value,
                min_rating: document.getElementById('ratingFilter').value,
                sort: document.getElementById('sortFilter').value,
                lat: document.getElementById('gpsLat').value,
                lng: document.getElementById('gpsLng').value,
                radius: document.getElementById('searchRadius').value,
            };

            // Remove empty params
            Object.keys(params).forEach(key => {
                if (!params[key]) delete params[key];
            });

            // Build query string
            const queryString = new URLSearchParams(params).toString();

            // Perform AJAX request
            fetch(`{{ route('api.search.live') }}?${queryString}`)
                .then(response => response.json())
                .then(data => {
                    // Hide loading indicator
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('vendorResults').style.opacity = '1';

                    // Update results count
                    const resultText = data.count > 0 
                        ? `Found <span class="font-semibold">${data.count}</span> verified vendor${data.count !== 1 ? 's' : ''}`
                        : '<span class="font-semibold">No vendors found</span>';
                    document.getElementById('resultText').innerHTML = resultText;

                    // Render vendor cards
                    renderVendorCards(data.vendors);
                })
                .catch(error => {
                    console.error('Search error:', error);
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('vendorResults').style.opacity = '1';
                    document.getElementById('vendorResults').innerHTML = `
                        <div class="col-span-3 text-center py-16">
                            <p class="text-red-600 font-semibold">Error loading results. Please try again.</p>
                        </div>
                    `;
                });
        }

        // Render vendor cards
        function renderVendorCards(vendors) {
            const container = document.getElementById('vendorResults');

            if (vendors.length === 0) {
                container.innerHTML = `
                    <div class="col-span-3 text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-2xl font-semibold text-gray-700 mb-2">No vendors found matching your search</h3>
                        <p class="text-gray-600 mb-6">Try adjusting your filters or search with different keywords</p>
                        <button onclick="clearAllFilters()" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                            Clear Filters
                        </button>
                    </div>
                `;
                return;
            }

            container.innerHTML = vendors.map(vendor => `
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                    <a href="${vendor.url}" class="block">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">${vendor.business_name}</h3>
                        
                        <!-- Rating -->
                        <div class="flex items-center mb-3">
                            <div class="flex items-center text-yellow-400 mr-2">
                                ${generateStars(vendor.rating)}
                            </div>
                            <span class="text-sm font-semibold text-gray-700">${vendor.rating}</span>
                            <span class="text-sm text-gray-500 ml-1">(${vendor.review_count} review${vendor.review_count !== 1 ? 's' : ''})</span>
                            ${vendor.verified ? '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">✓ Verified</span>' : ''}
                        </div>

                        <!-- Categories -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            ${vendor.categories.slice(0, 3).map(cat => 
                                `<span class="inline-block bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">${cat}</span>`
                            ).join('')}
                        </div>

                        <!-- Location -->
                        <div class="text-sm text-gray-600 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            ${vendor.address}
                            ${vendor.distance ? `<span class="font-semibold text-teal-600 ml-2">(${vendor.distance})</span>` : ''}
                        </div>

                        <!-- Description -->
                        ${vendor.description ? `<p class="text-sm text-gray-600 line-clamp-2">${vendor.description}</p>` : ''}
                    </a>
                </div>
            `).join('');
        }

        // Generate star rating HTML
        function generateStars(rating) {
            const stars = [];
            const fullStars = Math.floor(parseFloat(rating));
            
            for (let i = 0; i < 5; i++) {
                if (i < fullStars) {
                    stars.push('<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>');
                } else {
                    stars.push('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>');
                }
            }
            
            return stars.join('');
        }

        // Clear all filters
        function clearAllFilters() {
            document.getElementById('searchKeyword').value = '';
            document.getElementById('categoryFilter').value = '';
            document.getElementById('ratingFilter').value = '';
            document.getElementById('sortFilter').value = 'rating';
            document.getElementById('selectedRegion').value = '';
            document.getElementById('selectedDistrict').value = '';
            document.getElementById('gpsLat').value = '';
            document.getElementById('gpsLng').value = '';
            document.getElementById('locationDisplay').textContent = 'Select Location';
            document.getElementById('locationDisplay').classList.remove('text-gray-900', 'font-medium');
            document.getElementById('locationDisplay').classList.add('text-gray-400');
            
            performLiveSearch();
        }

        // Attach event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Search keyword with debounce
            document.getElementById('searchKeyword').addEventListener('input', function() {
                if (searchTimeout) clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performLiveSearch, 500);
            });

            // Category filter
            document.getElementById('categoryFilter').addEventListener('change', performLiveSearch);

            // Rating filter
            document.getElementById('ratingFilter').addEventListener('change', performLiveSearch);

            // Sort filter
            document.getElementById('sortFilter').addEventListener('change', performLiveSearch);

            // Perform initial search
            performLiveSearch();
        });
    </script>
</x-app-layout>

