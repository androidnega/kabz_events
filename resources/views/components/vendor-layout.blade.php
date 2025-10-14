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
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Sidebar styling on desktop */
        @media (min-width: 768px) {
            .vendor-sidebar {
                display: block !important;
                position: fixed !important;
                top: 64px !important;
                left: 0 !important;
                height: calc(100vh - 64px) !important;
                z-index: 20 !important;
                transition: width 0.3s ease !important;
            }
            
            .vendor-sidebar.sidebar-open {
                width: 256px !important;
            }
            
            .vendor-sidebar.sidebar-collapsed {
                width: 80px !important;
            }
            
            .vendor-main-content {
                transition: margin-left 0.3s ease !important;
            }
            
            .vendor-main-content.content-expanded {
                margin-left: 256px !important;
            }
            
            .vendor-main-content.content-collapsed {
                margin-left: 80px !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
    <!-- Top Navigation Bar (Fixed) -->
    <x-vendor-topbar />
    
    <!-- Sidebar (Fixed, Desktop only) -->
    <div :class="sidebarOpen ? 'sidebar-open' : 'sidebar-collapsed'">
        <x-vendor-nav />
    </div>
    
    <!-- Main Content Area -->
    <main class="vendor-main-content min-h-screen pt-16 pb-20 md:pb-6" :class="sidebarOpen ? 'content-expanded' : 'content-collapsed'">
        <div class="p-4 sm:p-6">
            {{ $slot }}
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <x-vendor-mobile-nav />
    
    @stack('scripts')
</body>
</html>

