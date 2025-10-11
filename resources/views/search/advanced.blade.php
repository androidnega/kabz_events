<x-app-layout>
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-teal-500 text-white py-12 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Advanced Vendor Search</h1>
            <p class="text-lg text-purple-100">Find vendors near you with GPS location and advanced filters</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search and Filter Section --}}
        <div class="bg-white p-6 shadow-lg rounded-lg mb-8">
            <form action="{{ route('search.advanced') }}" method="GET" id="advancedSearchForm" class="space-y-6">
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

                {{-- Location Section --}}
                <div class="bg-gradient-to-r from-teal-50 to-purple-50 border border-teal-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Location-Based Search
                        </h3>
                        <button 
                            type="button" 
                            id="useMyLocation"
                            class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Use My Location
                        </button>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Radius (km)</label>
                            <input 
                                type="number" 
                                name="radius" 
                                id="radius"
                                value="{{ request('radius', 50) }}" 
                                min="1" 
                                max="200"
                                class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-teal-500 focus:ring-2 focus:ring-teal-200"
                            >
                        </div>
                        <input type="hidden" name="lat" id="latitude" value="{{ request('lat', $userLat) }}">
                        <input type="hidden" name="lng" id="longitude" value="{{ request('lng', $userLng) }}">
                        
                        <div class="md:col-span-2" id="locationStatus">
                            @if($userLat && $userLng)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-6">
                                    <p class="text-sm text-green-800">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Location detected: {{ round($userLat, 4) }}, {{ round($userLng, 4) }}
                                    </p>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-6">
                                    <p class="text-sm text-yellow-800">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Click "Use My Location" to find vendors near you
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
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
                        <select name="region" id="regionSelect" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region }}" @selected(request('region') == $region)>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- District Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">District/Town</label>
                        <select name="district" id="districtSelect" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="">All Districts</option>
                            @if(request('region') && isset($districts[request('region')]))
                                @foreach($districts[request('region')] as $district)
                                    <option value="{{ $district }}" @selected(request('district') == $district)>
                                        {{ $district }}
                                    </option>
                                @endforeach
                            @endif
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
                </div>

                {{-- Sort and Submit --}}
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors">
                            <option value="recommended" @selected(request('sort') == 'recommended' || !request('sort'))>Recommended</option>
                            <option value="distance" @selected(request('sort') == 'distance')>Nearest First</option>
                            <option value="rating" @selected(request('sort') == 'rating')>Top Rated</option>
                            <option value="recent" @selected(request('sort') == 'recent')>Most Recent</option>
                            <option value="name" @selected(request('sort') == 'name')>Alphabetical</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button 
                            type="submit"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
                        >
                            Search with Filters
                        </button>
                    </div>
                </div>

                {{-- Clear Filters --}}
                @if(request()->hasAny(['q', 'category', 'region', 'district', 'min_rating', 'sort', 'lat', 'lng']))
                    <div class="text-center">
                        <a href="{{ route('search.advanced') }}" class="text-sm text-gray-600 hover:text-purple-600 underline">
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
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                    <a href="{{ route('vendors.show', $vendor->slug) }}" class="block">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $vendor->business_name }}</h3>
                        
                        {{-- Rating --}}
                        <div class="flex items-center mb-3">
                            <div class="flex items-center text-yellow-400 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($vendor->rating_cached))
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ number_format($vendor->rating_cached, 1) }}</span>
                            <span class="text-sm text-gray-500 ml-1">({{ $vendor->reviews->count() }} reviews)</span>
                        </div>

                        {{-- Categories --}}
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($vendor->services->pluck('category')->unique('id')->take(3) as $category)
                                <span class="inline-block bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>

                        {{-- Location and Distance --}}
                        <div class="text-sm text-gray-600 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $vendor->address }}
                            @if(isset($vendor->distance_formatted))
                                <span class="font-semibold text-teal-600 ml-2">({{ $vendor->distance_formatted }})</span>
                            @endif
                        </div>

                        {{-- Description --}}
                        @if($vendor->description)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($vendor->description, 100) }}</p>
                        @endif
                    </a>
                </div>
            @empty
                <div class="col-span-3 text-center py-16">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2">No vendors found matching your search</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your filters or expanding your search radius</p>
                    <a href="{{ route('search.advanced') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                        Reset Search
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
    </div>

    {{-- JavaScript for Location and Dynamic Filters --}}
    <div id="districts-data" 
         data-districts="{{ json_encode($districts) }}" 
         data-is-authenticated="{{ auth()->check() ? '1' : '0' }}"
         data-update-location-url="{{ route('api.location.update') }}"
         data-csrf-token="{{ csrf_token() }}"
         style="display:none;">
    </div>
    <script>
        // District data
        const districtsData = document.getElementById('districts-data').getAttribute('data-districts');
        const districts = JSON.parse(districtsData);
        
        // Region change handler
        document.getElementById('regionSelect').addEventListener('change', function() {
            const region = this.value;
            const districtSelect = document.getElementById('districtSelect');
            
            // Clear current options
            districtSelect.innerHTML = '<option value="">All Districts</option>';
            
            // Add new options
            if (region && districts[region]) {
                districts[region].forEach(district => {
                    const option = document.createElement('option');
                    option.value = district;
                    option.textContent = district;
                    districtSelect.appendChild(option);
                });
            }
        });

        // GPS Location Handler
        document.getElementById('useMyLocation').addEventListener('click', function() {
            const button = this;
            const statusDiv = document.getElementById('locationStatus');
            
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Getting Location...';
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        // Update hidden fields
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                        
                        // Update status
                        statusDiv.innerHTML = `
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-6">
                                <p class="text-sm text-green-800">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Location detected: ${lat.toFixed(4)}, ${lng.toFixed(4)}
                                </p>
                            </div>
                        `;
                        
                        // Update user location on server if logged in
                        const dataElement = document.getElementById('districts-data');
                        const isAuthenticated = dataElement.getAttribute('data-is-authenticated') === '1';
                        if (isAuthenticated) {
                            const updateUrl = dataElement.getAttribute('data-update-location-url');
                            const csrfToken = dataElement.getAttribute('data-csrf-token');
                            
                            fetch(updateUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    latitude: lat,
                                    longitude: lng
                                })
                            });
                        }
                        
                        // Reset button
                        button.disabled = false;
                        button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg> Use My Location';
                    },
                    function(error) {
                        statusDiv.innerHTML = `
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-6">
                                <p class="text-sm text-red-800">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Unable to get location. Please enable location services.
                                </p>
                            </div>
                        `;
                        button.disabled = false;
                        button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg> Use My Location';
                    }
                );
            } else {
                statusDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-6">
                        <p class="text-sm text-red-800">Geolocation is not supported by your browser.</p>
                    </div>
                `;
                button.disabled = false;
                button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg> Use My Location';
            }
        });
    </script>
</x-app-layout>

