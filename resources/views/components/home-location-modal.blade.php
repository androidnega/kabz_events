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
@endphp

<div x-data="homeLocationModal()" x-cloak data-locations='@json($ghanaLocations)'>
    <!-- Modal Overlay -->
    <div x-show="isOpen" 
         @keydown.escape.window="closeModal()"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <!-- Background Overlay -->
        <div class="fixed inset-0 bg-purple-900 bg-opacity-40 backdrop-blur-sm transition-opacity"
             @click="closeModal()"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                 @click.stop>
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-5 border-b border-purple-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900" x-text="currentView === 'regions' ? 'Select Location' : selectedRegion"></h3>
                                <p class="text-sm text-gray-600" x-show="currentView === 'regions'">Choose your region to find vendors nearby</p>
                                <button x-show="currentView === 'towns'" @click="backToRegions()" 
                                        class="text-sm text-purple-600 hover:text-purple-700 font-medium flex items-center gap-1">
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
                                class="w-full pl-10 pr-4 py-3 border border-purple-200 rounded-lg text-sm focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all bg-purple-50 bg-opacity-50"
                            >
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-purple-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Regions List -->
                    <div x-show="currentView === 'regions'" class="px-6 pb-6 space-y-2">
                        <template x-for="(towns, region) in filteredRegions" :key="region">
                            <div @click="showTowns(region, towns)" 
                                 class="group bg-gradient-to-r from-purple-50 to-indigo-50 hover:from-purple-100 hover:to-indigo-100 border border-purple-100 hover:border-purple-300 rounded-xl p-4 cursor-pointer transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:shadow">
                                            <i class="fas fa-map-pin text-purple-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 text-base" x-text="region"></h4>
                                            <p class="text-sm text-gray-600">
                                                <span x-text="towns.length"></span> towns available
                                            </p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-purple-400 group-hover:text-purple-600 transition-colors"></i>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Towns Grid -->
                    <div x-show="currentView === 'towns'" class="px-6 pb-6">
                        <div class="grid grid-cols-2 gap-3">
                            <template x-for="town in currentTowns" :key="town">
                                <div @click="selectTown(town)" 
                                     class="bg-purple-50 hover:bg-purple-100 border border-purple-100 hover:border-purple-300 rounded-lg p-3 cursor-pointer transition-all duration-200 group">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-building text-purple-500 text-sm group-hover:scale-110 transition-transform"></i>
                                        <span class="font-medium text-gray-800 text-sm" x-text="town"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- GPS Option -->
                    <div class="px-6 pb-4" x-show="currentView === 'regions'">
                        <div class="border-t border-purple-100 pt-4">
                            <button @click="useGPS()" :disabled="gpsLoading"
                                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 shadow-md hover:shadow-lg">
                                <i class="fas fa-crosshairs" :class="{ 'fa-spin': gpsLoading }"></i>
                                <span x-text="gpsLoading ? 'Getting your location...' : 'Use My Current Location'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-t border-purple-100">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            <i class="fas fa-map-marked-alt text-purple-600 mr-2"></i>
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
        
        init() {
            // Load regions from data attribute after Alpine initializes
            try {
                this.regions = JSON.parse(this.$el.getAttribute('data-locations') || '{}');
            } catch (e) {
                console.error('Failed to parse locations data:', e);
                this.regions = {};
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

// Global function to open modal
function openHomeLocationModal() {
    window.dispatchEvent(new CustomEvent('open-home-location-modal'));
}
</script>

<script>
// Listen for global event to open modal
window.addEventListener('open-home-location-modal', function() {
    const modalElement = document.querySelector('[x-data*="homeLocationModal"]');
    if (modalElement && modalElement.__x) {
        modalElement.__x.$data.openModal();
    }
});
</script>

