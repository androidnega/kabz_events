@php
$notifications = auth()->user()->unreadNotifications()->take(5)->get();
$unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative text-gray-600 hover:text-primary focus:outline-none transition p-2 rounded-full hover:bg-gray-100">
        <i class="fas fa-bell text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden"
         style="display: none;">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-800">
                <i class="fas fa-bell mr-2"></i>Notifications
            </h3>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.markAllAsRead') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-primary hover:underline">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notif)
                <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-info-circle text-primary text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $notif->data['from'] ?? 'System' }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ Str::limit($notif->data['content'] ?? $notif->data['message'] ?? 'New notification', 80) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-clock mr-1"></i>{{ $notif->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-primary transition" title="Mark as read">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 text-sm">No new notifications</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->count() > 0)
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-center">
                <a href="#" class="text-sm text-primary hover:underline font-medium">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>

