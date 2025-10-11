@php
    $user = Auth::user();
    $vendor = $user->vendor;
@endphp

<nav class="bg-white border-b border-gray-200 fixed w-full z-30 top-0">
    <div class="px-3 sm:px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Left: Logo & Toggle -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                <!-- Sidebar Toggle (Desktop only) -->
                <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-gray-600 hover:text-gray-900 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <x-kabzs-logo class="h-8 sm:h-10 w-auto" />
                </a>
            </div>

            <!-- Right: Quick Actions, Notifications, Profile -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Quick Action: Add Service (Desktop only) -->
                <a href="{{ route('vendor.services.create') }}" 
                   class="hidden lg:flex items-center px-3 sm:px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                    <i class="fas fa-plus mr-2"></i>
                    <span class="font-medium">Add Service</span>
                </a>

                <!-- Notifications Bell -->
                <x-notification-bell />

                <!-- Profile Dropdown (Desktop only) -->
                <div x-data="{ open: false }" class="relative hidden lg:block">
                    <button @click="open = !open" 
                            class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                        @if($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                                 alt="Profile" 
                                 class="w-8 h-8 rounded-full object-cover border-2 border-purple-200">
                        @elseif($vendor->profile_photo && !str_contains($vendor->profile_photo, 'picsum'))
                            <img src="{{ asset('storage/' . $vendor->profile_photo) }}" 
                                 alt="Profile" 
                                 class="w-8 h-8 rounded-full object-cover border-2 border-purple-200">
                        @else
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center border border-purple-200">
                                <span class="text-purple-700 font-semibold text-sm">
                                    {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <i class="fas fa-chevron-down text-gray-600 text-xs"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1"
                         style="display: none;">
                        
                        <!-- Business Name -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $vendor->business_name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->email }}</p>
                        </div>

                        <!-- Menu Items -->
                        <a href="{{ route('vendors.show', $vendor->slug) }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-eye w-5 text-gray-400"></i>
                            <span class="ml-3">View Public Profile</span>
                        </a>
                        
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user-cog w-5 text-gray-400"></i>
                            <span class="ml-3">Account Settings</span>
                        </a>
                        
                        <a href="{{ route('vendor.verification') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-certificate w-5 text-gray-400"></i>
                            <span class="ml-3">Verification</span>
                        </a>
                        
                        <a href="{{ route('vendor.subscriptions') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-crown w-5 text-gray-400"></i>
                            <span class="ml-3">Subscription</span>
                        </a>

                        <div class="border-t border-gray-100 mt-1"></div>
                        
                        <a href="{{ route('home') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-home w-5 text-gray-400"></i>
                            <span class="ml-3">Go to Homepage</span>
                        </a>

                        <div class="border-t border-gray-100 mt-1"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span class="ml-3">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

