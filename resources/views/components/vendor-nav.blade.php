@php
    $vendor = Auth::user()->vendor;
    if (!$vendor) {
        // Redirect or show error if no vendor profile
        abort(403, 'Vendor profile not found. Please contact support.');
    }
@endphp

<!-- Sidebar Navigation (Desktop Only) -->
<aside class="vendor-sidebar hidden md:block bg-white border-r border-gray-200 overflow-y-auto" :class="sidebarOpen ? 'sidebar-open' : 'sidebar-collapsed'">
    <div class="h-full flex flex-col" x-data="{ tooltip: '' }">
        
        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}"
               :class="!sidebarOpen ? 'justify-center' : ''"
               :title="!sidebarOpen ? 'Dashboard' : ''">
                <i class="fas fa-home text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Dashboard</span>
            </a>

            <!-- Services -->
            <a href="{{ route('vendor.services.index') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.services.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}"
               :class="!sidebarOpen ? 'justify-center' : ''"
               :title="!sidebarOpen ? 'Services' : ''">
                <i class="fas fa-th-large text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Services</span>
            </a>

            <!-- Sample Work -->
            <a href="{{ route('vendor.sample-work') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.sample-work*') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}"
               :class="!sidebarOpen ? 'justify-center' : ''"
               :title="!sidebarOpen ? 'Sample Work' : ''">
                <i class="fas fa-images text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Sample Work</span>
            </a>

            <!-- Payments (Coming Soon) -->
            <div class="flex items-center px-3 py-3 rounded-lg text-gray-400 cursor-not-allowed opacity-60"
                 :class="!sidebarOpen ? 'justify-center' : ''"
                 :title="!sidebarOpen ? 'Payments (Coming Soon)' : ''">
                <i class="fas fa-credit-card text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Payments</span>
                <span x-show="sidebarOpen" x-transition class="ml-auto text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span>
            </div>

            <!-- Analytics (Coming Soon) -->
            <div class="flex items-center px-3 py-3 rounded-lg text-gray-400 cursor-not-allowed opacity-60"
                 :class="!sidebarOpen ? 'justify-center' : ''"
                 :title="!sidebarOpen ? 'Analytics (Coming Soon)' : ''">
                <i class="fas fa-chart-line text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Analytics</span>
                <span x-show="sidebarOpen" x-transition class="ml-auto text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span>
            </div>

            <!-- Messages -->
            <a href="{{ route('vendor.messages') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.messages*') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}"
               :class="!sidebarOpen ? 'justify-center' : ''"
               :title="!sidebarOpen ? 'Messages' : ''">
                <i class="fas fa-envelope text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Messages</span>
            </a>

            <!-- Divider -->
            <div x-show="sidebarOpen" x-transition class="border-t border-gray-200 my-4"></div>
            <div x-show="!sidebarOpen" class="border-t border-gray-200 my-4"></div>

            <!-- Settings Section -->
            <div x-show="sidebarOpen" x-transition class="px-3 py-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</p>
            </div>

            <!-- Profile -->
            <a href="{{ route('vendor.profile') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.profile') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}"
               :class="!sidebarOpen ? 'justify-center' : ''"
               :title="!sidebarOpen ? 'Profile' : ''">
                <i class="fas fa-user-circle text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Profile</span>
            </a>

            <!-- Verification -->
            <a href="{{ route('vendor.verification') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.verification') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}"
               :class="!sidebarOpen ? 'justify-center' : ''"
               :title="!sidebarOpen ? 'Verification' : ''">
                <i class="fas fa-certificate text-lg w-6 relative">
                    @if(!$vendor->is_verified)
                        <span x-show="!sidebarOpen" class="absolute -top-1 -right-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                        </span>
                    @endif
                </i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Verification</span>
                @if(!$vendor->is_verified)
                    <span x-show="sidebarOpen" x-transition class="ml-auto">
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                        </span>
                    </span>
                @endif
            </a>

            <!-- Subscription -->
            <a href="{{ route('vendor.subscriptions') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.subscriptions') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}"
               :class="!sidebarOpen ? 'justify-center' : ''"
               :title="!sidebarOpen ? 'Subscription' : ''">
                <i class="fas fa-crown text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">Subscription</span>
            </a>

            <!-- VIP Plans -->
            <a href="{{ route('vendor.vip-subscriptions.index') }}" 
               class="flex items-center px-3 py-3 rounded-lg transition {{ request()->routeIs('vendor.vip-subscriptions.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}"
               :class="!sidebarOpen ? 'justify-center' : ''"
               :title="!sidebarOpen ? 'VIP Plans' : ''">
                <i class="fas fa-gem text-lg w-6"></i>
                <span x-show="sidebarOpen" x-transition class="ml-3 font-medium whitespace-nowrap">VIP Plans</span>
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

