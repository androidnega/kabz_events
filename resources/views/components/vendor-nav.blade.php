@php
    $vendor = Auth::user()->vendor;
@endphp

<!-- Sidebar Navigation -->
<aside class="hidden lg:fixed lg:block top-16 left-0 h-[calc(100vh-4rem)] bg-white border-r border-gray-200 transition-all duration-300 z-20"
       :class="sidebarOpen ? 'w-64' : 'w-20'">
    <div class="h-full flex flex-col">
        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-home text-lg w-6"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Dashboard</span>
            </a>

            <!-- Services -->
            <a href="{{ route('vendor.services.index') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.services.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-th-large text-lg w-6"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Services</span>
            </a>

            <!-- Bookings (Coming Soon) -->
            <div class="flex items-center px-3 py-3 rounded-lg text-gray-400 cursor-not-allowed opacity-60">
                <i class="fas fa-calendar-check text-lg w-6"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Bookings</span>
                <span x-show="sidebarOpen" class="ml-auto text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span>
            </div>

            <!-- Payments (Coming Soon) -->
            <div class="flex items-center px-3 py-3 rounded-lg text-gray-400 cursor-not-allowed opacity-60">
                <i class="fas fa-credit-card text-lg w-6"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Payments</span>
                <span x-show="sidebarOpen" class="ml-auto text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span>
            </div>

            <!-- Analytics (Coming Soon) -->
            <div class="flex items-center px-3 py-3 rounded-lg text-gray-400 cursor-not-allowed opacity-60">
                <i class="fas fa-chart-line text-lg w-6"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Analytics</span>
                <span x-show="sidebarOpen" class="ml-auto text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span>
            </div>

            <!-- Messages -->
            <a href="{{ route('messages.index') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('messages.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-envelope text-lg w-6"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Messages</span>
            </a>

            <!-- Divider -->
            <div x-show="sidebarOpen" class="border-t border-gray-200 my-4"></div>

            <!-- Settings Section -->
            <div x-show="sidebarOpen" class="px-3 py-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</p>
            </div>

            <!-- Verification -->
            <a href="{{ route('vendor.verification') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.verification') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-certificate text-lg w-6"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Verification</span>
                @if(!$vendor->is_verified)
                    <span x-show="sidebarOpen" class="ml-auto">
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                        </span>
                    </span>
                @endif
            </a>

            <!-- Subscription -->
            <a href="{{ route('vendor.subscriptions') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.subscriptions') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-crown text-lg w-6"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Subscription</span>
            </a>
        </nav>

        <!-- Verification Status Card (collapsed sidebar shows icon only) -->
        <div class="border-t border-gray-200 p-4">
            <div x-show="sidebarOpen" class="bg-purple-50 rounded-lg p-3 border border-purple-100">
                @if($vendor->is_verified)
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">Verified</p>
                            <p class="text-xs text-gray-600">Your account is verified</p>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <i class="fas fa-star text-amber-500 text-2xl mb-2"></i>
                        <p class="text-xs font-semibold text-gray-900 mb-1">Get Verified!</p>
                        <a href="{{ route('vendor.verification') }}" 
                           class="text-xs text-purple-600 hover:text-purple-700 font-medium">
                            Apply Now →
                        </a>
                    </div>
                @endif
            </div>

            <!-- Collapsed view - just icon -->
            <div x-show="!sidebarOpen" class="flex justify-center">
                @if($vendor->is_verified)
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                @else
                    <i class="fas fa-star text-amber-500 text-2xl"></i>
                @endif
            </div>
        </div>
    </div>
</aside>

