@php
$currentRoute = request()->route()->getName();
$user = Auth::user();
@endphp

<!-- Sidebar -->
<aside 
    x-show="sidebarOpen" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="w-64 bg-white shadow-lg border-r border-gray-200 flex-shrink-0"
>
    <div class="h-full flex flex-col">
        <!-- Logo Section -->
        <div class="px-6 py-5 border-b border-gray-200">
            <a href="{{ route('home') }}" class="flex items-center">
                <x-kabzs-logo class="h-8 w-auto" />
            </a>
            <p class="mt-2 text-xs text-gray-500">Client Dashboard</p>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <!-- Dashboard Overview -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors
                      {{ $currentRoute === 'dashboard' ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Favorites -->
            <a href="{{ route('client.favorites.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors
                      {{ str_contains($currentRoute, 'favorites') ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Favorites
            </a>

            <!-- Messages -->
            <a href="{{ route('client.conversations') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors
                      {{ str_contains($currentRoute, 'conversation') || str_contains($currentRoute, 'messages') ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                Messages
                @php
                    $unreadCount = \App\Models\Message::where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </a>

            <!-- Payments/Invoices -->
            <a href="{{ route('client.payments.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors
                      {{ str_contains($currentRoute, 'payments') ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Payments
            </a>

            <!-- Support -->
            <a href="{{ route('client.support.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors
                      {{ str_contains($currentRoute, 'support') ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Support
            </a>

            <!-- Settings -->
            <a href="{{ route('client.settings.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors
                      {{ str_contains($currentRoute, 'settings') ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Settings
            </a>

            <hr class="my-4 border-gray-200">

            <!-- Quick Links -->
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Quick Links</p>
            </div>

            <a href="{{ route('vendors.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Find Vendors
            </a>

            <a href="{{ route('home') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Public Site
            </a>
        </nav>

        <!-- User Info at Bottom -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-teal-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $user->display_id ?? 'Client' }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>

