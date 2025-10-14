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
    <div class="py-16 bg-neutral">
        <div class="container mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Vendors</h2>
                <p class="text-lg text-gray-600">Top-rated and verified service providers</p>
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

