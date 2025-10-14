<x-layouts.base>
    <x-slot name="title">
        Find Trusted Event Vendors in Ghana | {{ config('app.name') }}
    </x-slot>

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-primary to-purple-700 text-white py-12">
        <div class="container mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Find Trusted Event Vendors in Ghana</h1>
            <p class="text-lg text-purple-100">Browse verified service providers for your special events</p>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="bg-white border-b border-gray-200 py-4 md:py-6">
        <div class="container mx-auto px-4">
            <form action="{{ route('vendors.index') }}" method="GET" class="space-y-3">
                <!-- Main Search Bar -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search for photographer, caterer, decorator..."
                        class="w-full pl-10 pr-4 py-2.5 md:py-3 rounded-lg border border-gray-300 text-sm md:text-base focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors"
                    >
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3">
                    <!-- Category Filter -->
                    <div class="col-span-2 md:col-span-1">
                        <select 
                            name="category" 
                            class="w-full px-3 py-2 md:py-2.5 rounded-lg border border-gray-300 text-xs md:text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors"
                        >
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            type="button"
                            @click="open = !open"
                            class="w-full px-3 py-2 md:py-2.5 rounded-lg border border-gray-300 text-xs md:text-sm text-left hover:border-purple-500 focus:border-purple-500 transition-colors flex items-center justify-between gap-1"
                        >
                            <span id="vendorLocationDisplay" class="text-gray-700 truncate flex-1">All Locations</span>
                            <i class="fas fa-chevron-down text-xs text-gray-400 flex-shrink-0"></i>
                        </button>
                        
                        <input type="hidden" name="region" id="vendorSelectedRegion" value="{{ request('region') }}">
                        
                        <!-- Location Dropdown -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-cloak
                             class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-xl border border-gray-200 max-h-64 overflow-y-auto z-50">
                            <div class="p-2">
                                @foreach(['Greater Accra', 'Ashanti', 'Western', 'Central', 'Northern', 'Eastern', 'Volta', 'Upper East', 'Upper West', 'Bono'] as $region)
                                <button 
                                    type="button"
                                    @click="document.getElementById('vendorSelectedRegion').value = '{{ $region }}'; document.getElementById('vendorLocationDisplay').textContent = '{{ $region }}'; open = false; this.closest('form').submit()"
                                    class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-purple-50 rounded transition"
                                >
                                    {{ $region }}
                                </button>
                                @endforeach
                                <div class="border-t border-gray-200 my-1"></div>
                                <button 
                                    type="button"
                                    @click="document.getElementById('vendorSelectedRegion').value = ''; document.getElementById('vendorLocationDisplay').textContent = 'All Locations'; open = false; this.closest('form').submit()"
                                    class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded transition"
                                >
                                    Clear Location
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort Filter -->
                    <div>
                        <select 
                            name="sort"
                            class="w-full px-3 py-2 md:py-2.5 rounded-lg border border-gray-300 text-xs md:text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-200 transition-colors"
                        >
                            <option value="rating" {{ request('sort') == 'rating' || !request('sort') ? 'selected' : '' }}>Top Rated</option>
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Recent</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>A-Z</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="col-span-2 md:col-span-1">
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 md:py-2.5 px-4 rounded-lg transition-colors text-xs md:text-sm">
                            <i class="fas fa-search mr-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Vendors Grid -->
    <div class="py-12 bg-neutral">
        <div class="container mx-auto">
            @if($vendors->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($vendors as $vendor)
                        <x-vendor-card :vendor="$vendor" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $vendors->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No vendors found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    <div class="mt-6">
                        <a href="{{ route('vendors.index') }}">
                            <x-button variant="primary">
                                View All Vendors
                            </x-button>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.base>

