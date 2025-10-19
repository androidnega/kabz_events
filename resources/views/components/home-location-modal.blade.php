{{-- Beautiful Hierarchical Location Modal for Homepage --}}
@php
    $ghanaLocations = [
        'Greater Accra' => ['Accra Metropolitan', 'Tema Metropolitan', 'Ga East', 'Ga West', 'Ga South', 'Adenta', 'Ashiaman', 'Dome', 'Madina', 'Kasoa', 'Spintex', 'East Legon', 'Osu', 'Labone'],
        'Ashanti' => ['Kumasi Metropolitan', 'Obuasi Municipal', 'Ejisu', 'Mampong Municipal', 'Asokore Mampong', 'Bantama', 'Suame', 'Adum', 'Kejetia', 'Asafo'],
        'Western' => ['Sekondi-Takoradi', 'Tarkwa', 'Prestea', 'Axim', 'Effiakuma', 'Takoradi Market Circle', 'Agona Nkwanta'],
        'Central' => ['Cape Coast Metropolitan', 'Kasoa', 'Winneba', 'Agona Swedru', 'University of Cape Coast', 'Elmina', 'Mankessim'],
        'Northern' => ['Tamale Metropolitan', 'Yendi', 'Savelugu', 'Gumani', 'Tolon', 'Kpandai'],
        'Eastern' => ['Koforidua', 'New Juaben', 'Akropong', 'Nsawam', 'Suhum', 'Akim Oda', 'Mpraeso'],
        'Volta' => ['Ho Municipal', 'Hohoe', 'Keta', 'Aflao', 'Sogakope', 'Denu', 'Kpando'],
        'Upper East' => ['Bolgatanga Municipal', 'Bongo', 'Navrongo', 'Bawku', 'Paga'],
        'Upper West' => ['Wa Municipal', 'Wechiau', 'Lawra', 'Jirapa', 'Tumu'],
        'Bono' => ['Sunyani Municipal', 'Berekum', 'Techiman', 'Wenchi', 'Dormaa Ahenkro'],
    ];
    
    $primaryColor = $appearance['primary_color'] ?? '#9333ea';
    $secondaryColor = $appearance['secondary_color'] ?? '#a855f7';
    
    // Get vendor counts
    $regionCounts = $locationCounts['regions'] ?? [];
    $townCounts = $locationCounts['towns'] ?? [];
@endphp

<div x-data="homeLocationModal()" 
     @open-location-modal.window="openModal()"
     x-cloak 
     data-locations='@json($ghanaLocations)'
     data-region-counts='@json($regionCounts)'
     data-town-counts='@json($townCounts)'
     data-primary-color="{{ $primaryColor }}"
     data-secondary-color="{{ $secondaryColor }}">
    
    <!-- Dynamic Styles -->
    <style>
        .location-modal-primary {
            background-color: {{ $primaryColor }};
        }
        .location-modal-primary-light {
            background-color: {{ $primaryColor }}10;
        }
        .location-modal-primary-text {
            color: {{ $primaryColor }};
        }
        .location-modal-border-primary {
            border-color: {{ $primaryColor }}40;
        }
        .location-modal-hover-primary:hover {
            background-color: {{ $primaryColor }}15;
        }
        .location-modal-hover-border-primary:hover {
            border-color: {{ $primaryColor }}60;
        }
    </style>
    
    <!-- Modal Overlay -->
    <div x-show="isOpen" 
         @keydown.escape.window="closeModal()"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <!-- Background Overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 backdrop-blur-sm transition-opacity"
             @click="closeModal()"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                 @click.stop>
                 
                <!-- Modal Header -->
                <div class="location-modal-primary-light px-6 py-5 border-b location-modal-border-primary">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 location-modal-primary-light rounded-full flex items-center justify-center border location-modal-border-primary">
                                <i class="fas fa-map-marker-alt location-modal-primary-text text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900" x-text="currentView === 'regions' ? 'Select Location' : selectedRegion"></h3>
                                <p class="text-sm text-gray-600" x-show="currentView === 'regions'">Choose your region to find vendors nearby</p>
                                <button x-show="currentView === 'towns'" @click="backToRegions()" 
                                        class="text-sm location-modal-primary-text hover:opacity-75 font-medium flex items-center gap-1">
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    Back to Regions
                                </button>
                            </div>
                        </div>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto" style="max-height: calc(90vh - 180px);">
                    <!-- Search Bar -->
                    <div class="p-6 pb-4" x-show="currentView === 'regions'">
                        <div class="relative">
                            <input 
                                type="text" 
                                x-model="searchTerm"
                                placeholder="Search regions or towns..."
                                class="w-full pl-10 pr-4 py-3 border location-modal-border-primary rounded-lg text-sm focus:ring-2 focus:ring-opacity-20 transition-all location-modal-primary-light bg-opacity-30"
                                :style="`focus:ring-color: ${primaryColor}`"
                            >
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 location-modal-primary-text text-sm opacity-60"></i>
                        </div>
                    </div>

                    <!-- Regions List -->
                    <div x-show="currentView === 'regions'" class="px-6 pb-6 space-y-2">
                        <template x-for="(towns, region) in filteredRegions" :key="region">
                            <div @click="showTowns(region, towns)" 
                                 class="group location-modal-primary-light location-modal-hover-primary border location-modal-border-primary location-modal-hover-border-primary rounded-xl p-4 cursor-pointer transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow border location-modal-border-primary">
                                            <i class="fas fa-map-pin location-modal-primary-text text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 text-base" x-text="region"></h4>
                                            <p class="text-sm text-gray-600">
                                                <span x-text="getRegionVendorCount(region)"></span> vendors • 
                                                <span x-text="towns.length"></span> towns
                                            </p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right location-modal-primary-text opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                </div>
                            </div>
                        </template>
                        
                        <!-- No Results -->
                        <div x-show="Object.keys(filteredRegions).length === 0" class="text-center py-8">
                            <i class="fas fa-search text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-600">No locations found matching your search</p>
                        </div>
                    </div>

                    <!-- Towns Grid -->
                    <div x-show="currentView === 'towns'" class="px-6 pb-6">
                        <div class="grid grid-cols-2 gap-3">
                            <template x-for="town in currentTowns" :key="town">
                                <div @click="selectTown(town)" 
                                     class="location-modal-primary-light location-modal-hover-primary border location-modal-border-primary location-modal-hover-border-primary rounded-lg p-3 cursor-pointer transition-all duration-200 group">
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-building location-modal-primary-text text-sm mt-0.5 group-hover:scale-110 transition-transform"></i>
                                        <div class="flex-1 min-w-0">
                                            <span class="font-medium text-gray-800 text-sm block truncate" x-text="town"></span>
                                            <span class="text-xs text-gray-600" x-text="getTownVendorCount(town) + ' vendors'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- GPS Option -->
                    <div class="px-6 pb-4" x-show="currentView === 'regions'">
                        <div class="border-t location-modal-border-primary pt-4">
                            <button @click="useGPS()" :disabled="gpsLoading"
                                    class="w-full location-modal-primary hover:opacity-90 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 shadow-md hover:shadow-lg">
                                <i class="fas fa-crosshairs" :class="{ 'fa-spin': gpsLoading }"></i>
                                <span x-text="gpsLoading ? 'Getting your location...' : 'Use My Current Location'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="location-modal-primary-light px-6 py-4 border-t location-modal-border-primary">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            <i class="fas fa-map-marked-alt location-modal-primary-text mr-2"></i>
                            <span class="font-medium" x-text="selectedLocationText"></span>
                        </div>
                        <button @click="clearLocation()" 
                                class="text-sm text-red-600 hover:text-red-700 font-medium transition-colors flex items-center gap-1">
                            <i class="fas fa-times-circle"></i>
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function homeLocationModal() {
    return {
        isOpen: false,
        currentView: 'regions',
        selectedRegion: '',
        selectedTown: '',
        searchTerm: '',
        currentTowns: [],
        gpsLoading: false,
        regions: {},
        regionCounts: {},
        townCounts: {},
        primaryColor: '',
        secondaryColor: '',
        
        init() {
            // Load data from attributes after Alpine initializes
            try {
                this.regions = JSON.parse(this.$el.getAttribute('data-locations') || '{}');
                this.regionCounts = JSON.parse(this.$el.getAttribute('data-region-counts') || '{}');
                this.townCounts = JSON.parse(this.$el.getAttribute('data-town-counts') || '{}');
                this.primaryColor = this.$el.getAttribute('data-primary-color') || '#9333ea';
                this.secondaryColor = this.$el.getAttribute('data-secondary-color') || '#a855f7';
            } catch (e) {
                console.error('Failed to parse location modal data:', e);
                this.regions = {};
                this.regionCounts = {};
                this.townCounts = {};
            }
        },
        
        get filteredRegions() {
            if (!this.searchTerm) return this.regions;
            
            const search = this.searchTerm.toLowerCase();
            const filtered = {};
            
            for (const [region, towns] of Object.entries(this.regions)) {
                if (region.toLowerCase().includes(search)) {
                    filtered[region] = towns;
                } else {
                    const matchingTowns = towns.filter(town => 
                        town.toLowerCase().includes(search)
                    );
                    if (matchingTowns.length > 0) {
                        filtered[region] = matchingTowns;
                    }
                }
            }
            
            return filtered;
        },
        
        get selectedLocationText() {
            if (this.selectedRegion && this.selectedTown) {
                return `${this.selectedTown}, ${this.selectedRegion}`;
            }
            if (this.selectedRegion) {
                return this.selectedRegion;
            }
            return 'No location selected';
        },
        
        getRegionVendorCount(region) {
            return this.regionCounts[region] || 0;
        },
        
        getTownVendorCount(town) {
            const key = `${this.selectedRegion}|${town}`;
            return this.townCounts[key] || 0;
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
        
        selectTown(town) {
            this.selectedTown = town;
            
            // Update hidden form fields
            const regionInput = document.getElementById('homeSelectedRegion');
            const districtInput = document.getElementById('homeSelectedDistrict');
            const locationDisplay = document.getElementById('homeLocationDisplay');
            
            if (regionInput) regionInput.value = this.selectedRegion;
            if (districtInput) districtInput.value = town;
            if (locationDisplay) {
                locationDisplay.textContent = town;
                locationDisplay.classList.remove('text-gray-700');
                locationDisplay.classList.add('text-gray-900', 'font-medium');
            }
            
            this.closeModal();
        },
        
        clearLocation() {
            this.selectedRegion = '';
            this.selectedTown = '';
            
            const regionInput = document.getElementById('homeSelectedRegion');
            const districtInput = document.getElementById('homeSelectedDistrict');
            const locationDisplay = document.getElementById('homeLocationDisplay');
            
            if (regionInput) regionInput.value = '';
            if (districtInput) districtInput.value = '';
            if (locationDisplay) {
                locationDisplay.textContent = 'All Locations';
                locationDisplay.classList.remove('text-gray-900', 'font-medium');
                locationDisplay.classList.add('text-gray-700');
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
                
                this.selectedRegion = 'GPS Location';
                this.selectedTown = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                
                const locationDisplay = document.getElementById('homeLocationDisplay');
                if (locationDisplay) {
                    locationDisplay.textContent = 'GPS Location';
                    locationDisplay.classList.remove('text-gray-700');
                    locationDisplay.classList.add('text-gray-900', 'font-medium');
                }
                
                this.closeModal();
            } catch (error) {
                alert('Unable to get your location. Please enable location services or select manually.');
            } finally {
                this.gpsLoading = false;
            }
        }
    }
}
</script>

