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
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                    Home
                </a>
                
                <a href="{{ route('search.index') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                    Search
                </a>
                
                <a href="{{ route('vendors.index') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                    Find Vendors
                </a>
                
                <a href="{{ route('vendors.index') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                    Browse Services
                </a>
                
                @auth
                    @role('super_admin')
                    <a href="{{ route('superadmin.dashboard') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                        Super Admin
                    </a>
                    @endrole
                    
                    @role('admin')
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                        Admin Dashboard
                    </a>
                    @endrole
                    
                    @role('vendor')
                    <a href="{{ route('vendor.dashboard') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                        Vendor Dashboard
                    </a>
                    @endrole
                    
                    @role('client')
                    <a href="{{ route('client.dashboard') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                        My Dashboard
                    </a>
                    @endrole
                    
                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('vendor.public.register') }}" class="text-primary hover:text-purple-700 px-3 py-2 text-sm font-semibold">
                        Sign Up as Vendor
                    </a>
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-primary text-white hover:bg-purple-700 px-4 py-2 rounded-md text-sm font-medium btn-lift">
                        Sign Up
                    </a>
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
    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                Home
            </a>
            <a href="{{ route('search.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                Search
            </a>
            <a href="{{ route('vendors.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                Find Vendors
            </a>
            <a href="{{ route('vendors.index') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                Browse Services
            </a>
            
            @auth
                @role('super_admin')
                <a href="{{ route('superadmin.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                    Super Admin
                </a>
                @endrole
                @role('admin')
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                    Admin Dashboard
                </a>
                @endrole
                @role('vendor')
                <a href="{{ route('vendor.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                    Vendor Dashboard
                </a>
                @endrole
                @role('client')
                <a href="{{ route('client.dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                    My Dashboard
                </a>
                @endrole
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('vendor.public.register') }}" class="block px-3 py-2 text-base font-medium text-primary hover:bg-gray-50 rounded-md">
                    Sign Up as Vendor
                </a>
                <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50 rounded-md">
                    Login
                </a>
                <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium bg-primary text-white hover:bg-purple-700 rounded-md text-center">
                    Sign Up
                </a>
            @endauth
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

