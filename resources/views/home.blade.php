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
            <div id="vendors-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredVendors as $vendor)
                    <x-vendor-card-infinite :vendor="$vendor" />
                @endforeach
            </div>
            
            <!-- Loading indicator -->
            <div id="loading-indicator" class="text-center py-8 hidden">
                <div class="inline-flex items-center px-4 py-2 text-sm text-gray-600">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading more vendors...
                </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 2; // Start from page 2 since page 1 is already loaded
    let isLoading = false;
    let hasMore = true;
    
    const vendorsContainer = document.getElementById('vendors-container');
    const loadingIndicator = document.getElementById('loading-indicator');
    
    if (!vendorsContainer || !loadingIndicator) return;
    
    // Function to load more vendors
    async function loadMoreVendors() {
        if (isLoading || !hasMore) return;
        
        isLoading = true;
        loadingIndicator.classList.remove('hidden');
        
        try {
            const response = await fetch(`/api/load-more-vendors?page=${currentPage}`);
            const data = await response.json();
            
            if (data.html) {
                vendorsContainer.insertAdjacentHTML('beforeend', data.html);
                currentPage = data.page;
                hasMore = data.hasMore;
            }
            
            if (!data.hasMore) {
                // No more vendors to load
                loadingIndicator.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error loading more vendors:', error);
            loadingIndicator.classList.add('hidden');
        } finally {
            isLoading = false;
        }
    }
    
    // Intersection Observer for infinite scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && hasMore && !isLoading) {
                loadMoreVendors();
            }
        });
    }, {
        rootMargin: '100px' // Start loading when 100px away from the loading indicator
    });
    
    observer.observe(loadingIndicator);
    
    // Fallback: Load more on scroll near bottom
    window.addEventListener('scroll', () => {
        if (isLoading || !hasMore) return;
        
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.offsetHeight;
        
        // Load more when user is 200px from bottom
        if (scrollTop + windowHeight >= documentHeight - 200) {
            loadMoreVendors();
        }
    });
});
</script>

