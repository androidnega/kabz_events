{{-- Location Selection Modal --}}
@php
    $locationsData = [
        'Greater Accra' => ['Accra Metropolitan', 'Tema Metropolitan', 'Ga East', 'Ga West', 'Ga South', 'Adenta', 'Ashiaman', 'Dome', 'Madina', 'Kasoa'],
        'Ashanti' => ['Kumasi Metropolitan', 'Obuasi Municipal', 'Ejisu', 'Mampong Municipal', 'Asokore Mampong', 'Bantama', 'Suame', 'Adum'],
        'Western' => ['Sekondi-Takoradi', 'Tarkwa', 'Prestea', 'Axim', 'Effiakuma', 'Takoradi Market Circle'],
        'Central' => ['Cape Coast Metropolitan', 'Kasoa', 'Winneba', 'Agona Swedru', 'University of Cape Coast', 'Elmina'],
        'Northern' => ['Tamale Metropolitan', 'Yendi', 'Savelugu', 'Gumani', 'Tolon', 'Kpandai'],
        'Eastern' => ['Koforidua', 'New Juaben', 'Akropong', 'Nsawam', 'Suhum', 'Akim Oda'],
        'Volta' => ['Ho Municipal', 'Hohoe', 'Keta', 'Aflao', 'Sogakope', 'Denu'],
        'Upper East' => ['Bolgatanga Municipal', 'Bongo', 'Navrongo', 'Bawku', 'Paga'],
        'Upper West' => ['Wa Municipal', 'Wechiau', 'Lawra', 'Jirapa', 'Tumu'],
        'Bono' => ['Sunyani Municipal', 'Berekum', 'Techiman', 'Wenchi', 'Dormaa Ahenkro'],
    ];
    
    $modalConfig = [
        'isAuthenticated' => auth()->check(),
        'apiRoute' => route('api.location.update'),
        'csrfToken' => csrf_token()
    ];
@endphp
<div id="locationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50" 
     data-locations="{{ json_encode($locationsData) }}"
     data-config="{{ json_encode($modalConfig) }}">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span id="modalTitle">Select Your Location</span>
            </h3>
            <button onclick="closeLocationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="mt-4">
            {{-- Search Bar --}}
            <div class="mb-4">
                <input 
                    type="text" 
                    id="locationSearch" 
                    placeholder="Search for a region or town..."
                    class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-colors"
                >
            </div>

            {{-- Use My GPS Location Button --}}
            <button 
                id="useGPSLocation"
                onclick="useMyGPSLocation()"
                class="w-full mb-4 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                <span>Use My GPS Location</span>
            </button>

            {{-- Regions List --}}
            <div id="regionsList" class="space-y-2 max-h-96 overflow-y-auto">
                @foreach($locationsData as $region => $towns)
                    <div class="region-item border-2 border-gray-200 hover:border-purple-500 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md" 
                         data-region="{{ $region }}"
                         data-towns="{{ json_encode($towns) }}"
                         onclick="showTownsFromData(this)">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900 text-lg">{{ $region }}</h4>
                                <p class="text-sm text-gray-600">{{ count($towns) }} towns available</p>
                            </div>
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Towns List (Hidden by default) --}}
            <div id="townsList" class="hidden">
                {{-- Back Button --}}
                <button 
                    onclick="backToRegions()"
                    class="mb-4 flex items-center gap-2 text-purple-600 hover:text-purple-800 font-medium transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Back to Regions</span>
                </button>

                {{-- Towns Grid --}}
                <div id="townsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-96 overflow-y-auto">
                    {{-- Towns will be populated by JavaScript --}}
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span id="selectedLocationText">No location selected</span>
                </div>
                <button 
                    onclick="clearLocation()"
                    class="text-sm text-red-600 hover:text-red-800 font-medium transition-colors"
                >
                    Clear Location
                </button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for Location Modal --}}
<script>
    let CONFIG = {};
    let currentRegion = null;
    let currentTown = null;
    let allLocations = {};
    
    // Load configuration and locations from data attributes
    try {
        const modalElement = document.getElementById('locationModal');
        if (modalElement) {
            if (modalElement.dataset.config) {
                CONFIG = JSON.parse(modalElement.dataset.config);
            }
            if (modalElement.dataset.locations) {
                allLocations = JSON.parse(modalElement.dataset.locations);
            }
        }
    } catch (e) {
        console.error('Error loading modal data:', e);
    }

    function openLocationModal() {
        document.getElementById('locationModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeLocationModal() {
        document.getElementById('locationModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showTownsFromData(element) {
        const region = element.getAttribute('data-region');
        const towns = JSON.parse(element.getAttribute('data-towns'));
        showTowns(region, towns);
    }

    function showTowns(region, towns) {
        currentRegion = region;
        
        // Hide regions list
        document.getElementById('regionsList').classList.add('hidden');
        
        // Update title
        document.getElementById('modalTitle').textContent = region;
        
        // Populate towns
        const townsGrid = document.getElementById('townsGrid');
        townsGrid.innerHTML = '';
        
        towns.forEach(town => {
            const townElement = document.createElement('div');
            townElement.className = 'border-2 border-gray-200 hover:border-purple-500 rounded-lg p-3 cursor-pointer transition-all duration-200 hover:shadow-md';
            townElement.onclick = () => selectLocation(region, town);
            townElement.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    <span class="font-medium text-gray-900">${town}</span>
                </div>
            `;
            townsGrid.appendChild(townElement);
        });
        
        // Show towns list
        document.getElementById('townsList').classList.remove('hidden');
    }

    function backToRegions() {
        // Hide towns list
        document.getElementById('townsList').classList.add('hidden');
        
        // Show regions list
        document.getElementById('regionsList').classList.remove('hidden');
        
        // Reset title
        document.getElementById('modalTitle').textContent = 'Select Your Location';
        
        currentRegion = null;
    }

    function selectLocation(region, town) {
        currentRegion = region;
        currentTown = town;
        
        // Update hidden form fields
        document.getElementById('selectedRegion').value = region;
        document.getElementById('selectedDistrict').value = town;
        
        // Update display
        document.getElementById('selectedLocationText').textContent = `${town}, ${region}`;
        document.getElementById('locationDisplay').textContent = `${town}, ${region}`;
        document.getElementById('locationDisplay').classList.remove('text-gray-400');
        document.getElementById('locationDisplay').classList.add('text-gray-900', 'font-medium');
        
        // Close modal
        closeLocationModal();
        
        // Trigger search
        if (typeof performLiveSearch === 'function') {
            performLiveSearch();
        }
    }

    function clearLocation() {
        currentRegion = null;
        currentTown = null;
        
        // Clear form fields
        document.getElementById('selectedRegion').value = '';
        document.getElementById('selectedDistrict').value = '';
        
        // Update display
        document.getElementById('selectedLocationText').textContent = 'No location selected';
        document.getElementById('locationDisplay').textContent = 'Select Location';
        document.getElementById('locationDisplay').classList.remove('text-gray-900', 'font-medium');
        document.getElementById('locationDisplay').classList.add('text-gray-400');
        
        // Trigger search
        if (typeof performLiveSearch === 'function') {
            performLiveSearch();
        }
    }

    function useMyGPSLocation() {
        const button = document.getElementById('useGPSLocation');
        const originalHTML = button.innerHTML;
        
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Getting Location...';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Set GPS coordinates in hidden fields
                    document.getElementById('gpsLat').value = lat;
                    document.getElementById('gpsLng').value = lng;
                    
                    // Update display
                    document.getElementById('selectedLocationText').textContent = `GPS Location (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                    document.getElementById('locationDisplay').textContent = 'GPS Location';
                    document.getElementById('locationDisplay').classList.remove('text-gray-400');
                    document.getElementById('locationDisplay').classList.add('text-gray-900', 'font-medium');
                    
                    // Save to server if logged in
                    if (CONFIG.isAuthenticated) {
                        fetch(CONFIG.apiRoute, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CONFIG.csrfToken
                            },
                            body: JSON.stringify({
                                latitude: lat,
                                longitude: lng
                            })
                        });
                    }
                    
                    // Close modal
                    closeLocationModal();
                    
                    // Trigger search
                    if (typeof performLiveSearch === 'function') {
                        performLiveSearch();
                    }
                    
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                },
                function(error) {
                    alert('Unable to get your location. Please enable location services or select manually.');
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                }
            );
        } else {
            alert('Geolocation is not supported by your browser. Please select location manually.');
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
    }

    // Search functionality in modal
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('locationSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const regionItems = document.querySelectorAll('.region-item');
                
                regionItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    });

    // Close modal when clicking outside
    document.getElementById('locationModal')?.addEventListener('click', function(e) {
        if (e.target.id === 'locationModal') {
            closeLocationModal();
        }
    });
</script>

<style>
    /* Custom scrollbar for modal */
    #regionsList::-webkit-scrollbar,
    #townsGrid::-webkit-scrollbar {
        width: 8px;
    }

    #regionsList::-webkit-scrollbar-track,
    #townsGrid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #regionsList::-webkit-scrollbar-thumb,
    #townsGrid::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    #regionsList::-webkit-scrollbar-thumb:hover,
    #townsGrid::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

