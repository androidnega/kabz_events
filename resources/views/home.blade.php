<x-layouts.base>
    <x-slot name="title">
        {{ config('app.name', 'KABZS EVENT') }} - Find Perfect Event Vendors
    </x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-primary to-purple-700 text-white">
        <div class="container mx-auto py-6 md:py-12 lg:py-16 px-4">
            <div class="text-center">
                <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-bold mb-2 md:mb-4 px-2">
                    Find Perfect Event Vendors in Ghana
                </h1>
                <p class="text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl mb-4 md:mb-6 text-purple-100 px-2">
                    Connect with verified service providers
                </p>
                
                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto">
                    <form action="{{ route('search.index') }}" method="GET" class="relative">
                        <input 
                            type="text" 
                            name="q"
                            placeholder="Search vendors..."
                            class="w-full px-4 md:px-5 py-2.5 md:py-3 pr-24 md:pr-28 rounded-full text-gray-900 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-purple-300"
                        >
                        <button type="submit" class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-1.5 md:py-2 px-4 md:px-6 rounded-full transition-colors text-sm md:text-base">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="py-8 md:py-12 lg:py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-6 md:mb-8 lg:mb-10">
                <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 md:mb-3">Browse by Category</h2>
                <p class="text-sm md:text-base lg:text-lg text-gray-600">Find what you need for your event</p>
            </div>

            @if($categories->count() > 0)
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4 lg:gap-6 px-4 md:px-0">
                @foreach($categories as $category)
                <a href="{{ route('search.index', ['category' => $category->slug]) }}" class="block group">
                    <div class="bg-white rounded-lg border border-gray-200 hover:border-purple-300 transition-colors duration-200 p-3 md:p-4 text-center h-full">
                        <!-- Icon -->
                        <div class="mb-2 flex justify-center">
                            <div class="w-10 h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 rounded-full bg-gradient-to-br from-purple-100 to-indigo-100 flex items-center justify-center">
                                @if($category->icon)
                                    <i class="fas fa-{{ $category->icon }} text-base md:text-lg lg:text-xl text-primary"></i>
                                @else
                                    <i class="fas fa-box text-base md:text-lg lg:text-xl text-primary"></i>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Category name -->
                        <h3 class="font-semibold text-xs md:text-sm text-gray-900 group-hover:text-primary transition-colors line-clamp-2">
                            {{ $category->name }}
                        </h3>
                    </div>
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
    <div class="py-8 md:py-12 lg:py-16 bg-neutral">
        <div class="container mx-auto">
            <div class="text-center mb-6 md:mb-8 lg:mb-10">
                <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 md:mb-3">Featured Vendors</h2>
                <p class="text-sm md:text-base lg:text-lg text-gray-600">Top-rated service providers</p>
            </div>

            @if($featuredVendors->count() > 0)
            <div id="vendors-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach($featuredVendors as $vendor)
                    <x-vendor-card-infinite :vendor="$vendor" />
                @endforeach
            </div>
            
            <!-- Loading indicator -->
            <div id="loading-indicator" class="text-center py-8" style="display: none;">
                <div class="inline-flex items-center justify-center px-4 py-3 text-sm text-gray-600 bg-gray-50 rounded-lg">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading more vendors...</span>
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

</x-layouts.base>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 2;
    let isLoading = false;
    let hasMore = true;
    
    const vendorsContainer = document.getElementById('vendors-container');
    const loadingIndicator = document.getElementById('loading-indicator');
    
    if (!vendorsContainer || !loadingIndicator) return;
    
    // Function to load more vendors
    async function loadMoreVendors() {
        if (isLoading || !hasMore) return;
        
        isLoading = true;
        loadingIndicator.style.display = 'block';
        
        try {
            const response = await fetch(`/api/load-more-vendors?page=${currentPage}`);
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            if (data.html && data.html.trim() !== '') {
                vendorsContainer.insertAdjacentHTML('beforeend', data.html);
                currentPage = data.page;
                hasMore = data.hasMore;
            }
            
            if (!data.hasMore) {
                loadingIndicator.style.display = 'none';
                return;
            }
            
        } catch (error) {
            console.error('Error loading more vendors:', error);
            loadingIndicator.style.display = 'none';
            hasMore = false;
        } finally {
            isLoading = false;
        }
    }
    
    // Standard infinite scroll implementation
    let scrollTimeout;
    
    function handleScroll() {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        
        scrollTimeout = setTimeout(() => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            
            // Load more when user is 300px from bottom
            if (scrollTop + windowHeight >= documentHeight - 300) {
                loadMoreVendors();
            }
        }, 100);
    }
    
    // Use standard scroll event for better compatibility
    window.addEventListener('scroll', handleScroll, { passive: true });
    
    // Also use Intersection Observer as backup
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && hasMore && !isLoading) {
                    loadMoreVendors();
                }
            });
        }, {
            rootMargin: '50px',
            threshold: 0.1
        });
        
        observer.observe(loadingIndicator);
    }
});
</script>

