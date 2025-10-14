<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
        <!-- Top Navigation Bar (Fixed) -->
        <x-vendor-topbar />
        
        <!-- Sidebar (Fixed, Desktop only) -->
        <x-vendor-nav />
        
        <!-- Main Content Area -->
        <main class="min-h-screen pt-16 transition-all duration-300 pb-20 lg:pb-6" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
            <div class="p-4 sm:p-6">
                {{ $slot }}
            </div>
        </main>

        <!-- Mobile Bottom Navigation -->
        <x-vendor-mobile-nav />
    </div>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>

