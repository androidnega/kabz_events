@php
$notifications = auth()->user()->unreadNotifications()->take(5)->get();
@endphp

<div class="relative" x-data="{ open: false }">
  <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
    <i class="fas fa-bell text-xl"></i>
    @if($notifications->count())
      <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
        {{ $notifications->count() }}
      </span>
    @endif
  </button>

  <div x-show="open" 
       @click.away="open = false"
       x-cloak
       class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 overflow-hidden">
    
    {{-- Header --}}
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
      @if($notifications->count())
        <button onclick="markAllAsRead()" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
          Mark all read
        </button>
      @endif
    </div>

    {{-- Notifications List --}}
    <div class="max-h-96 overflow-y-auto">
      @forelse($notifications as $notification)
        <a href="{{ route('messages.index') }}" 
           onclick="markAsRead('{{ $notification->id }}')"
           class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <i class="fas fa-comment-dots text-indigo-600 text-lg"></i>
            </div>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-gray-900">
                {{ $notification->data['from'] ?? 'Someone' }}
              </p>
              <p class="text-sm text-gray-600 mt-1">
                {{ Str::limit($notification->data['content'] ?? 'New message', 60) }}
              </p>
              <p class="text-xs text-gray-400 mt-1">
                {{ $notification->created_at->diffForHumans() }}
              </p>
            </div>
          </div>
        </a>
      @empty
        <div class="px-4 py-8 text-center">
          <i class="fas fa-bell-slash text-gray-300 text-4xl mb-2"></i>
          <p class="text-gray-500 text-sm">No new notifications</p>
        </div>
      @endforelse
    </div>

    {{-- Footer --}}
    @if($notifications->count())
      <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
        <a href="{{ route('messages.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
          View all messages →
        </a>
      </div>
    @endif
  </div>
</div>

<script>
function markAsRead(notificationId) {
  fetch(`/notifications/${notificationId}/read`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  });
}

function markAllAsRead() {
  fetch('/notifications/mark-all-read', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  }).then(() => {
    window.location.reload();
  });
}
</script>

