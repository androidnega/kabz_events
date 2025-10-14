{{-- Location Selection Modal with Alpine.js --}}
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
@endphp

<div x-data="locationModal()" 
     x-show="isOpen" 
     x-cloak
     @keydown.escape.window="closeModal()"
     class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto z-50 flex items-center justify-center p-4"
     @click.self="closeModal()">
    
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white z-10">
            <h3 class="text-lg md:text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-text="currentView === 'regions' ? 'Select Your Location' : selectedRegion"></span>
            </h3>
            <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-4 overflow-y-auto" style="max-height: calc(90vh - 180px);">
            {{-- Search Bar --}}
            <div class="mb-4" x-show="currentView === 'regions'">
                <input 
                    type="text" 
                    x-model="searchTerm"
                    placeholder="Search regions..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors"
                >
            </div>

            {{-- Use GPS Button --}}
            <button 
                @click="useGPS()"
                :disabled="gpsLoading"
                class="w-full mb-4 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm disabled:opacity-50"
                x-show="currentView === 'regions'"
            >
                <svg class="w-4 h-4" :class="{ 'animate-spin': gpsLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                <span x-text="gpsLoading ? 'Getting location...' : 'Use My GPS Location'"></span>
            </button>

            {{-- Regions List --}}
            <div x-show="currentView === 'regions'" class="space-y-2">
                <template x-for="(towns, region) in filteredRegions" :key="region">
                    <div @click="showTowns(region, towns)" 
                         class="border border-gray-200 hover:border-purple-500 rounded-lg p-3 cursor-pointer transition-all duration-200 hover:bg-purple-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm md:text-base" x-text="region"></h4>
                                <p class="text-xs text-gray-600"><span x-text="towns.length"></span> towns</p>
                            </div>
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Towns List --}}
            <div x-show="currentView === 'towns'">
                {{-- Back Button --}}
                <button 
                    @click="backToRegions()"
                    class="mb-4 flex items-center gap-2 text-purple-600 hover:text-purple-800 font-medium transition-colors text-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Back to Regions</span>
                </button>

                {{-- Towns Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <template x-for="town in currentTowns" :key="town">
                        <div @click="selectLocation(town)" 
                             class="border border-gray-200 hover:border-purple-500 rounded-lg p-2.5 cursor-pointer transition-all duration-200 hover:bg-purple-50">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <span class="font-medium text-gray-900 text-sm" x-text="town"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-xs md:text-sm text-gray-600">
                    <span x-text="selectedLocationText"></span>
                </div>
                <button 
                    @click="clearLocation()"
                    class="text-xs md:text-sm text-red-600 hover:text-red-800 font-medium transition-colors"
                >
                    Clear
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function locationModal() {
    return {
        isOpen: false,
        currentView: 'regions',
        selectedRegion: '',
        selectedTown: '',
        searchTerm: '',
        currentTowns: [],
        gpsLoading: false,
        
        regions: @json($locationsData),
        
        get filteredRegions() {
            if (!this.searchTerm) return this.regions;
            
            const search = this.searchTerm.toLowerCase();
            const filtered = {};
            
            for (const [region, towns] of Object.entries(this.regions)) {
                if (region.toLowerCase().includes(search) || 
                    towns.some(town => town.toLowerCase().includes(search))) {
                    filtered[region] = towns;
                }
            }
            
            return filtered;
        },
        
        get selectedLocationText() {
            if (this.selectedRegion && this.selectedTown) {
                return `${this.selectedTown}, ${this.selectedRegion}`;
            }
            return 'No location selected';
        },
        
        openModal() {
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeModal() {
            this.isOpen = false;
            document.body.style.overflow = 'auto';
        },
        
        showTowns(region, towns) {
            this.selectedRegion = region;
            this.currentTowns = towns;
            this.currentView = 'towns';
        },
        
        backToRegions() {
            this.currentView = 'regions';
            this.searchTerm = '';
        },
        
        selectLocation(town) {
            this.selectedTown = town;
            
            // Update hidden form fields
            document.getElementById('selectedRegion').value = this.selectedRegion;
            document.getElementById('selectedDistrict').value = town;
            
            // Update display button
            const display = document.getElementById('locationDisplay');
            if (display) {
                display.textContent = `${town}, ${this.selectedRegion}`;
                display.classList.remove('text-gray-400');
                display.classList.add('text-gray-900', 'font-medium');
            }
            
            this.closeModal();
            
            // Trigger search
            if (typeof performLiveSearch === 'function') {
                performLiveSearch();
            }
        },
        
        clearLocation() {
            this.selectedRegion = '';
            this.selectedTown = '';
            
            // Clear form fields
            document.getElementById('selectedRegion').value = '';
            document.getElementById('selectedDistrict').value = '';
            document.getElementById('gpsLat').value = '';
            document.getElementById('gpsLng').value = '';
            
            // Update display
            const display = document.getElementById('locationDisplay');
            if (display) {
                display.textContent = 'Select';
                display.classList.remove('text-gray-900', 'font-medium');
                display.classList.add('text-gray-400');
            }
            
            // Trigger search
            if (typeof performLiveSearch === 'function') {
                performLiveSearch();
            }
        },
        
        async useGPS() {
            this.gpsLoading = true;
            
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                this.gpsLoading = false;
                return;
            }
            
            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject);
                });
                
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Set GPS coordinates
                document.getElementById('gpsLat').value = lat;
                document.getElementById('gpsLng').value = lng;
                
                this.selectedRegion = 'GPS';
                this.selectedTown = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                
                // Update display
                const display = document.getElementById('locationDisplay');
                if (display) {
                    display.textContent = 'GPS Location';
                    display.classList.remove('text-gray-400');
                    display.classList.add('text-gray-900', 'font-medium');
                }
                
                this.closeModal();
                
                // Trigger search
                if (typeof performLiveSearch === 'function') {
                    performLiveSearch();
                }
            } catch (error) {
                alert('Unable to get location. Please enable location services.');
            } finally {
                this.gpsLoading = false;
            }
        }
    }
}

// Global function for opening modal from outside Alpine component
function openLocationModal() {
    // Dispatch event that Alpine component can listen to
    window.dispatchEvent(new CustomEvent('open-location-modal'));
}
</script>

<script>
// Listen for global event to open modal
window.addEventListener('open-location-modal', function() {
    // Find the Alpine component and open it
    const modalElement = document.querySelector('[x-data*="locationModal"]');
    if (modalElement && modalElement.__x) {
        modalElement.__x.$data.openModal();
    }
});
</script>
