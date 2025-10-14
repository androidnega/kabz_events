<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Services\SettingsService::get('site_name', 'KABZS EVENT') }} Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar Backdrop (Mobile) --}}
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

        {{-- Sidebar --}}
        <aside x-show="sidebarOpen || window.innerWidth >= 1024"
               x-transition:enter="transition ease-in-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in-out duration-300 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               @resize.window="if (window.innerWidth >= 1024) { sidebarOpen = false }"
               class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-lg lg:shadow-md flex flex-col">
            
            {{-- Logo/Brand --}}
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <i class="fas fa-calendar-check text-2xl text-primary"></i>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">{{ \App\Services\SettingsService::get('site_name', 'KABZS EVENT') }}</h2>
                        <p class="text-xs text-gray-600 -mt-1">
                            @role('super_admin')Super Admin@endrole
                            @role('admin')Admin Panel@endrole
                            @role('vendor')Vendor Dashboard@endrole
                            @role('client')My Dashboard@endrole
                        </p>
                    </div>
                </a>
                
                {{-- Close button for mobile --}}
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            {{-- Navigation Links --}}
            <nav class="flex-1 overflow-y-auto p-4">
                @include('components.dashboard.sidebar-links')
            </nav>

            {{-- User Info & Logout --}}
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-primary bg-opacity-20 flex items-center justify-center">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('home') }}" class="flex-1 text-center px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition" title="View Public Site">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700 transition">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Top Bar --}}
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-700 hover:text-primary focus:outline-none transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <!-- Empty spacer for layout -->
                    <div class="flex-1"></div>
                </div>

                <div class="flex items-center space-x-4">
                    {{-- Notification Bell --}}
                    <x-dashboard.notification-bell />
                    
                    {{-- User Avatar/Name (Desktop) --}}
                    <div class="hidden md:flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-primary bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-user text-primary text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-800">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </header>

            {{-- Content Area --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                            <p class="text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-0.5"></i>
                            <div>
                                <p class="font-medium text-red-800 mb-2">Please fix the following errors:</p>
                                <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Page Content --}}
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
