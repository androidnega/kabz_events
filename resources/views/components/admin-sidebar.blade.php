<!-- Sidebar Backdrop (Mobile) -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 lg:hidden"
     style="display: none;">
</div>

<!-- Sidebar -->
<aside x-show="sidebarOpen || window.innerWidth >= 1024"
       x-transition:enter="transition ease-in-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in-out duration-300 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       @resize.window="if (window.innerWidth >= 1024) { sidebarOpen = false }"
       class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-lg lg:shadow-none flex flex-col">
    
    <!-- Logo/Brand -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <i class="fas fa-shield-alt text-2xl text-teal-600"></i>
            <div>
                <h2 class="text-lg font-bold text-gray-800">KABZS</h2>
                <p class="text-xs text-gray-600 -mt-1">Admin Panel</p>
            </div>
        </a>
        <!-- Close button for mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        <div class="space-y-1">
            <!-- Dashboard - Unified Route (Phase K3) -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition
                      {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-tachometer-alt text-lg w-5"></i>
                <span>Dashboard</span>
            </a>

            <!-- Verifications -->
            <a href="{{ route('admin.verifications.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition
                      {{ request()->routeIs('admin.verifications.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-user-check text-lg w-5"></i>
                <span>Verifications</span>
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="ml-auto bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <!-- Vendors -->
            <a href="{{ route('vendors.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition
                      {{ request()->routeIs('vendors.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-store-alt text-lg w-5"></i>
                <span>All Vendors</span>
            </a>

            <!-- Reports -->
            <a href="{{ route('admin.reports.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition
                      {{ request()->routeIs('admin.reports.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-flag text-lg w-5"></i>
                <span>Reports</span>
            </a>

            <!-- Messages (Admin view - coming soon) -->
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-400 cursor-not-allowed">
                <i class="fas fa-envelope text-lg w-5"></i>
                <span>Messages</span>
            </div>

            <!-- Users -->
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition
                      {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-users-cog text-lg w-5"></i>
                <span>Users</span>
            </a>
        </div>

        <!-- System Section -->
        <div class="mt-6">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">System</h3>
            <div class="space-y-1">
                <!-- Analytics -->
                <a href="{{ route('dashboard') }}#analytics" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-chart-line text-lg w-5"></i>
                    <span>Analytics</span>
                </a>

                <!-- Settings -->
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition
                          {{ request()->routeIs('profile.edit') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-cog text-lg w-5"></i>
                    <span>Settings</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- User Info (Bottom) -->
    <div class="border-t border-gray-200 p-4 bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center">
                <i class="fas fa-user text-teal-600"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-600" title="View Public Site">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>
    </div>
</aside>

