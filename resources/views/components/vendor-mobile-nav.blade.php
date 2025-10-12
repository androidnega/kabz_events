@php
    $vendor = Auth::user()->vendor;
@endphp

<!-- Mobile Bottom Navigation -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-30 safe-bottom">
    <div class="grid grid-cols-5 h-16">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center justify-center {{ request()->routeIs('dashboard') ? 'text-purple-600' : 'text-gray-600' }}">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-xs font-medium">Home</span>
        </a>

        <!-- Services -->
        <a href="{{ route('vendor.services.index') }}" 
           class="flex flex-col items-center justify-center {{ request()->routeIs('vendor.services.*') ? 'text-purple-600' : 'text-gray-600' }}">
            <i class="fas fa-th-large text-xl mb-1"></i>
            <span class="text-xs font-medium">Services</span>
        </a>

        <!-- Add Service (Center Action) -->
        <a href="{{ route('vendor.services.create') }}" 
           class="flex flex-col items-center justify-center -mt-6">
            <div class="w-14 h-14 rounded-full bg-purple-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-plus text-white text-2xl"></i>
            </div>
        </a>

        <!-- Messages -->
        <a href="{{ route('vendor.messages') }}" 
           class="flex flex-col items-center justify-center {{ request()->routeIs('vendor.messages*') ? 'text-purple-600' : 'text-gray-600' }}">
            <i class="fas fa-envelope text-xl mb-1"></i>
            <span class="text-xs font-medium">Messages</span>
        </a>

        <!-- Menu -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                class="flex flex-col items-center justify-center text-gray-600">
            <i class="fas fa-bars text-xl mb-1"></i>
            <span class="text-xs font-medium">Menu</span>
        </button>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div x-show="mobileMenuOpen" 
     @click="mobileMenuOpen = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="lg:hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-40"
     style="display: none;">
</div>

<!-- Mobile Menu Drawer -->
<div x-show="mobileMenuOpen"
     @click.away="mobileMenuOpen = false"
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="lg:hidden fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-50 max-h-[80vh] overflow-y-auto pb-20"
     style="display: none;">
    
    <div class="p-6">
        <!-- Menu Header -->
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Menu</h3>
            <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-purple-50 rounded-lg p-3 border border-purple-100">
                <p class="text-2xl font-bold text-purple-600">{{ $vendor->services->count() }}</p>
                <p class="text-xs text-gray-600">Services</p>
            </div>
            @if($vendor->is_verified)
                <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                    <p class="text-sm font-bold text-green-600 flex items-center">
                        <i class="fas fa-check-circle mr-1"></i> Verified
                    </p>
                    <p class="text-xs text-gray-600">Account Status</p>
                </div>
            @else
                <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                    <p class="text-sm font-bold text-amber-600">Not Verified</p>
                    <a href="{{ route('vendor.verification') }}" class="text-xs text-purple-600">Get Verified →</a>
                </div>
            @endif
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-2">
            <a href="{{ route('vendor.profile') }}" 
               class="flex items-center p-3 rounded-lg hover:bg-gray-50 {{ request()->routeIs('vendor.profile') ? 'bg-purple-50 text-purple-700' : 'text-gray-700' }}">
                <i class="fas fa-user-circle w-6"></i>
                <span class="ml-3 font-medium">Profile</span>
            </a>

            <a href="{{ route('vendor.verification') }}" 
               class="flex items-center p-3 rounded-lg hover:bg-gray-50 {{ request()->routeIs('vendor.verification') ? 'bg-purple-50 text-purple-700' : 'text-gray-700' }}">
                <i class="fas fa-certificate w-6"></i>
                <span class="ml-3 font-medium">Verification</span>
                @if(!$vendor->is_verified)
                    <span class="ml-auto">
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                    </span>
                @endif
            </a>

            <a href="{{ route('vendor.subscriptions') }}" 
               class="flex items-center p-3 rounded-lg hover:bg-gray-50 {{ request()->routeIs('vendor.subscriptions') ? 'bg-purple-50 text-purple-700' : 'text-gray-700' }}">
                <i class="fas fa-crown w-6"></i>
                <span class="ml-3 font-medium">Subscription</span>
            </a>

            <div class="border-t border-gray-200 my-4"></div>

            <a href="{{ route('vendors.show', $vendor->slug) }}" 
               class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                <i class="fas fa-eye w-6"></i>
                <span class="ml-3 font-medium">View Public Profile</span>
            </a>

            <a href="{{ route('profile.edit') }}" 
               class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                <i class="fas fa-user-cog w-6"></i>
                <span class="ml-3 font-medium">Account Settings</span>
            </a>

            <a href="{{ route('home') }}" 
               class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                <i class="fas fa-home w-6"></i>
                <span class="ml-3 font-medium">Go to Homepage</span>
            </a>

            <div class="border-t border-gray-200 my-4"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="flex items-center w-full p-3 rounded-lg hover:bg-red-50 text-red-600">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span class="ml-3 font-medium">Logout</span>
                </button>
            </form>
        </nav>
    </div>
</div>

<style>
    .safe-bottom {
        padding-bottom: env(safe-area-inset-bottom);
    }
</style>

