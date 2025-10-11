<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Vendor Dashboard' }} - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <x-vendor-topbar />
        
        <div class="flex">
            <!-- Sidebar (Desktop) -->
            <x-vendor-nav />
            
            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden w-full pb-20 lg:pb-6" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
                <div class="p-3 sm:p-4 md:p-6 max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Mobile Bottom Navigation -->
        <x-vendor-mobile-nav />
    </div>
    
    @stack('scripts')
</body>
</html>

