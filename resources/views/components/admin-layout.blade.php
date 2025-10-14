<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <x-admin-sidebar />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 shadow-sm z-10">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 rounded-lg p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Empty spacer for layout -->
                    <div class="flex-1"></div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <x-notification-bell />

                        <!-- User Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 rounded-lg px-3 py-2">
                                    <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-user-circle text-xl text-teal-600"></i>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    <i class="fas fa-user-edit mr-2 text-gray-500"></i> Profile Settings
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('home')">
                                    <i class="fas fa-home mr-2 text-gray-500"></i> Public Site
                                </x-dropdown-link>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="fas fa-sign-out-alt mr-2 text-gray-500"></i> Log Out
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

