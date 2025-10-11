<x-layouts.base>
    <x-slot name="title">
        {{ config('app.name', 'KABZS EVENT') }} - Find Perfect Event Vendors
    </x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-primary to-purple-700 text-white">
        <div class="container mx-auto py-12 md:py-20 px-4">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 px-2">
                    Find the Perfect Vendors for Your Event in Ghana
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl mb-8 text-purple-100 px-2">
                    Connect with verified event service providers across Ghana
                </p>
                
                <!-- Search Bar -->
                <div class="max-w-3xl mx-auto px-4">
                    <form action="{{ route('search.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 sm:gap-0 sm:relative">
                        <input 
                            type="text" 
                            name="q"
                            placeholder="Search for photographers, caterers, decorators..."
                            class="w-full px-4 sm:px-6 py-3 sm:py-4 sm:pr-36 rounded-full text-gray-900 text-base sm:text-lg focus:outline-none focus:ring-2 focus:ring-purple-300 shadow-lg"
                        >
                        <button type="submit" class="w-full sm:w-auto sm:absolute sm:right-2 sm:top-1/2 sm:transform sm:-translate-y-1/2 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 sm:py-2 px-8 rounded-full transition-colors shadow-lg">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-12 md:py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8 md:mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 md:mb-4">Browse by Category</h2>
                <p class="text-base md:text-lg text-gray-600">Find exactly what you need for your special event</p>
            </div>

            @if($categories->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6 px-4 md:px-0">
                @foreach($categories as $category)
                <a href="{{ route('search.index', ['category' => $category->slug]) }}" class="block h-full">
                    <x-card hoverable class="h-full flex flex-col p-4 md:p-6 text-center group transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <!-- Icon with circular background -->
                        <div class="mb-3 flex justify-center">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-purple-100 to-indigo-100 group-hover:from-purple-200 group-hover:to-indigo-200 flex items-center justify-center transition-all duration-300">
                                @if($category->icon)
                                    <i class="fas fa-{{ $category->icon }} text-2xl md:text-3xl text-primary group-hover:scale-110 transition-transform duration-300"></i>
                                @else
                                    <i class="fas fa-box text-2xl md:text-3xl text-primary group-hover:scale-110 transition-transform duration-300"></i>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Category name with fixed height -->
                        <h3 class="font-semibold text-sm md:text-base text-gray-900 group-hover:text-primary mb-2 transition-colors duration-300 min-h-[2.5rem] flex items-center justify-center">
                            {{ $category->name }}
                        </h3>
                        
                        <!-- Description with fixed height -->
                        @if($category->description)
                        <p class="text-xs text-gray-500 mt-auto line-clamp-2 min-h-[2rem]">
                            {{ Str::limit($category->description, 45) }}
                        </p>
                        @else
                        <p class="text-xs text-gray-500 mt-auto min-h-[2rem]">&nbsp;</p>
                        @endif
                    </x-card>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center text-gray-500 py-8">
                <p>No categories available yet.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Featured Vendors Section -->
    <div class="py-16 bg-neutral">
        <div class="container mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Vendors</h2>
                <p class="text-lg text-gray-600">Top-rated and verified service providers</p>
            </div>

            @if($featuredVendors->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredVendors as $vendor)
                <x-card hoverable>
                    <!-- Vendor Image -->
                    @if($vendor->sample_work_images && count($vendor->sample_work_images) > 0)
                        <div class="h-48 bg-gray-100 overflow-hidden">
                            <img src="{{ asset('storage/' . $vendor->sample_work_images[0]) }}" 
                                 alt="{{ $vendor->business_name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-purple-100 to-indigo-100 flex items-center justify-center">
                            <span class="text-6xl text-primary font-bold">
                                {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif

                    <!-- Vendor Info -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ $vendor->business_name }}
                            </h3>
                            @if($vendor->is_verified)
                            <x-badge type="verified">
                                <svg class="w-3 h-3 mr-1 inline" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </x-badge>
                            @endif
                        </div>

                        <!-- Rating -->
                        <div class="flex items-center mb-3">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= round($vendor->rating_cached) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">
                                {{ number_format($vendor->rating_cached, 1) }}
                            </span>
                        </div>

                        <!-- Categories -->
                        @if($vendor->services->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($vendor->services->pluck('category')->unique('id')->take(3) as $category)
                            <x-badge type="primary">
                                {{ $category->name }}
                            </x-badge>
                            @endforeach
                        </div>
                        @endif

                        <!-- Description -->
                        @if($vendor->description)
                        <p class="text-sm text-gray-600 mb-4">
                            {{ Str::limit($vendor->description, 100) }}
                        </p>
                        @endif

                        <!-- Location -->
                        @if($vendor->address)
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ Str::limit($vendor->address, 30) }}
                        </div>
                        @endif

                        <!-- View Profile Button -->
                        <a href="{{ route('vendors.show', $vendor->slug) }}" class="block w-full">
                            <x-button variant="primary" class="w-full">
                                View Profile
                            </x-button>
                        </a>
                    </div>
                </x-card>
                @endforeach
            </div>
            @else
            <div class="text-center text-gray-500 py-12">
                <p class="text-lg mb-4">No featured vendors available yet.</p>
                <a href="{{ route('vendor.public.register') }}">
                    <x-button variant="primary" size="lg">
                        Be the First Vendor
                    </x-button>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-primary py-16">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold text-white mb-4">
                Ready to Grow Your Business?
            </h2>
            <p class="text-xl text-purple-100 mb-8">
                Join hundreds of vendors reaching thousands of clients
            </p>
            <a href="{{ route('vendor.public.register') }}">
                <x-button variant="accent" size="xl">
                    Register as Vendor Now
                </x-button>
            </a>
        </div>
    </div>

</x-layouts.base>

