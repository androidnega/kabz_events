<!-- KABZS EVENT - Main Navigation Component -->
<nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="container mx-auto">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                    <span class="text-2xl font-bold text-primary">KABZS EVENT</span>
                </a>
            </div>
            
            <!-- Navigation Links (Desktop) -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition">
                    Home
                </a>
                
                <a href="{{ route('vendors.index') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition">
                    <i class="fas fa-search mr-1"></i> Find Vendors
                </a>
                
                <a href="{{ route('search.index') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition">
                    <i class="fas fa-th-large mr-1"></i> Browse Categories
                </a>
                
                @auth
                    <!-- Unified Dashboard Link - Phase K3 -->
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    
                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition">
                            <i class="fas fa-user-circle text-xl"></i>
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                    
                    <!-- Sign Up Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center bg-primary text-white hover:bg-purple-700 px-4 py-2 rounded-md text-sm font-medium btn-lift transition">
                            <i class="fas fa-user-plus mr-1"></i>
                            <span>Sign Up</span>
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
                            <a href="{{ route('register') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">
                                <i class="fas fa-user mr-2 text-indigo-600"></i>
                                <span class="font-medium">Sign Up as Client</span>
                                <p class="text-xs text-gray-500 ml-6">Find vendors for your event</p>
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="{{ route('vendor.public.register') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-purple-50">
                                <i class="fas fa-store mr-2 text-purple-600"></i>
                                <span class="font-medium">Sign Up as Vendor</span>
                                <p class="text-xs text-gray-500 ml-6">Offer your services</p>
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-primary">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" x-cloak class="md:hidden border-t border-gray-200 bg-white">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                <i class="fas fa-home mr-2 w-5"></i> Home
            </a>
            <a href="{{ route('vendors.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                <i class="fas fa-search mr-2 w-5"></i> Find Vendors
            </a>
            <a href="{{ route('search.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                <i class="fas fa-th-large mr-2 w-5"></i> Browse Categories
            </a>
            
            @auth
                <div class="border-t border-gray-200 my-2"></div>
                
                <!-- Unified Dashboard Link - Phase K3 Mobile -->
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                    <i class="fas fa-tachometer-alt mr-2 w-5"></i> 
                    @if(auth()->user()->hasRole('super-admin'))
                        Super Admin Dashboard
                    @elseif(auth()->user()->hasRole('admin'))
                        Admin Dashboard
                    @elseif(auth()->user()->hasRole('vendor'))
                        My Business Dashboard
                    @else
                        My Dashboard
                    @endif
                </a>
                
                <div class="border-t border-gray-200 my-2"></div>
                
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                    <i class="fas fa-user mr-2 w-5"></i> Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                        <i class="fas fa-sign-out-alt mr-2 w-5"></i> Logout
                    </button>
                </form>
            @endauth
            
            @guest
                <div class="border-t border-gray-200 my-2"></div>
                
                <!-- Auth Section -->
                <div class="px-3 py-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Account</p>
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                        <i class="fas fa-sign-in-alt mr-2 w-5"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium text-indigo-600 hover:bg-indigo-50 rounded-md">
                        <i class="fas fa-user mr-2 w-5"></i> Sign Up as Client
                    </a>
                    <a href="{{ route('vendor.public.register') }}" class="block px-3 py-2 text-base font-medium text-purple-600 hover:bg-purple-50 rounded-md">
                        <i class="fas fa-store mr-2 w-5"></i> Sign Up as Vendor
                    </a>
                </div>
            @endguest
        </div>
    </div>
</nav>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('navbar', () => ({
            mobileMenuOpen: false
        }))
    })
</script>
@endpush

