<x-layouts.base>
    <x-slot name="title">
        Search Event Vendors in Ghana | {{ config('app.name') }}
    </x-slot>
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-teal-500 text-white py-4 md:py-8 mb-4 md:mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-xl md:text-2xl lg:text-3xl font-bold mb-1 md:mb-2">Find Event Vendors in Ghana</h1>
            <p class="text-sm md:text-base text-purple-100">Verified vendors across all regions</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search and Filter Section --}}
        <div class="bg-white p-3 md:p-4 lg:p-6 border border-gray-200 rounded-lg mb-4 md:mb-6">
            {{-- Hidden form fields for AJAX --}}
            <input type="hidden" id="selectedRegion" value="{{ request('region') }}">
            <input type="hidden" id="selectedDistrict" value="{{ request('district') }}">
            <input type="hidden" id="gpsLat" value="{{ request('lat') }}">
            <input type="hidden" id="gpsLng" value="{{ request('lng') }}">
            <input type="hidden" id="searchRadius" value="{{ request('radius', 50) }}">

            <div class="space-y-3">
                {{-- Main Search Bar --}}
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchKeyword"
                        value="{{ request('q') }}" 
                        placeholder="Search vendors..." 
                        class="w-full border border-gray-300 rounded-lg pl-3 pr-10 py-2 md:py-2.5 text-sm md:text-base focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors"
                    >
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                {{-- Filter Row --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 md:gap-3">
                    {{-- Category Filter --}}
                    <div class="col-span-2 md:col-span-1 min-w-0">
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 truncate">Category</label>
                        <select id="categoryFilter" class="w-full border border-gray-300 rounded-lg pl-2 pr-6 py-2 text-xs md:text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors truncate">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" @selected(request('category') == $cat->slug)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Location Filter (Opens Modal) --}}
                    <div class="min-w-0">
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 truncate">Location</label>
                        <button 
                            type="button"
                            onclick="openLocationModal()"
                            class="w-full border border-gray-300 rounded-lg pl-2 pr-7 py-2 text-xs md:text-sm text-left hover:border-purple-500 focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors relative overflow-hidden"
                        >
                            <span id="locationDisplay" class="text-gray-400 truncate block pr-1">Select</span>
                            <svg class="w-3 h-3 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Rating Filter --}}
                    <div class="min-w-0">
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 truncate">Rating</label>
                        <select id="ratingFilter" class="w-full border border-gray-300 rounded-lg pl-2 pr-6 py-2 text-xs md:text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors">
                            <option value="">Any</option>
                            <option value="4.5" @selected(request('min_rating') == '4.5')>4.5+</option>
                            <option value="4.0" @selected(request('min_rating') == '4.0')>4.0+</option>
                            <option value="3.5" @selected(request('min_rating') == '3.5')>3.5+</option>
                            <option value="3.0" @selected(request('min_rating') == '3.0')>3.0+</option>
                        </select>
                    </div>

                    {{-- Sort Filter --}}
                    <div class="min-w-0">
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 truncate">Sort</label>
                        <select id="sortFilter" class="w-full border border-gray-300 rounded-lg pl-2 pr-6 py-2 text-xs md:text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors">
                            <option value="rating" @selected(request('sort') == 'rating' || !request('sort'))>Top Rated</option>
                            <option value="premium" @selected(request('sort') == 'premium')>Premium</option>
                            <option value="recent" @selected(request('sort') == 'recent')>Recent</option>
                            <option value="name" @selected(request('sort') == 'name')>A-Z</option>
                            <option value="distance" @selected(request('sort') == 'distance')>Nearest</option>
                        </select>
                    </div>

                    {{-- Clear Filters Button --}}
                    <div class="flex items-end col-span-2 md:col-span-1 min-w-0">
                        <button 
                            type="button"
                            onclick="clearAllFilters()"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-2 rounded-lg transition-colors text-xs md:text-sm truncate"
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

        {{-- Vendor Results List --}}
        <div id="vendorResults" class="space-y-2 md:space-y-3 mb-8">
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

            container.innerHTML = vendors.map(vendor => {
                // Get preview image or fallback
                const previewImage = vendor.preview_image || (vendor.sample_work_images && vendor.sample_work_images.length > 0 ? vendor.sample_work_images[0] : null);
                const hasImage = previewImage && previewImage !== '';
                const vendorInitial = vendor.business_name.charAt(0).toUpperCase();
                
                return `
                <a href="${vendor.url}" class="block group">
                    <div class="bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors overflow-hidden">
                        <div class="flex items-center gap-3 md:gap-4 p-2 md:p-3">
                            <!-- Vendor Image -->
                            <div class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0">
                                ${hasImage ? `
                                    <div class="w-full h-full bg-gray-100 rounded-lg overflow-hidden">
                                        <img src="${previewImage}" 
                                             alt="${vendor.business_name}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                             loading="lazy"
                                             onerror="this.parentElement.innerHTML='<div class=\\'h-full bg-gradient-to-br from-purple-100 to-teal-100 flex items-center justify-center\\'><div class=\\'text-2xl font-bold text-purple-300\\'>${vendorInitial}</div></div>'">
                                    </div>
                                ` : `
                                    <div class="w-full h-full bg-gradient-to-br from-purple-100 to-teal-100 rounded-lg flex items-center justify-center">
                                        <div class="text-2xl md:text-3xl font-bold text-purple-300">${vendorInitial}</div>
                                    </div>
                                `}
                            </div>
                            
                            <!-- Vendor Info (All in one line) -->
                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                <!-- Row 1: Name, Rating, Verified -->
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm md:text-base font-semibold text-gray-900 group-hover:text-purple-600 transition-colors truncate flex-shrink">
                                        ${vendor.business_name}
                                    </h3>
                                    ${vendor.verified ? '<svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' : ''}
                                    <div class="flex items-center text-yellow-400 flex-shrink-0">
                                        ${generateStars(vendor.rating)}
                                    </div>
                                    <span class="text-xs text-gray-600 flex-shrink-0">${vendor.rating}</span>
                                </div>
                                
                                <!-- Row 2: Category & Location -->
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    ${vendor.categories.length > 0 ? `<span class="inline-block bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-medium flex-shrink-0">${vendor.categories[0]}</span>` : ''}
                                    ${vendor.address ? `
                                        <span class="flex items-center gap-1 truncate">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            </svg>
                                            <span class="truncate">${vendor.address}</span>
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            `;
            }).join('');
        }

        // Generate star rating HTML
        function generateStars(rating) {
            const stars = [];
            const fullStars = Math.floor(parseFloat(rating));
            
            for (let i = 0; i < 5; i++) {
                if (i < fullStars) {
                    stars.push('<svg class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>');
                } else {
                    stars.push('<svg class="w-3 h-3 md:w-4 md:h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>');
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
</x-layouts.base>

