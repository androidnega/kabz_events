@php
$notifications = auth()->user()->unreadNotifications()->take(5)->get();
$unreadMessageCount = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
@endphp

<div class="relative" x-data="{ open: false }">
  <button @click="open = !open; loadNotifications()" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
    <i class="fas fa-bell text-xl"></i>
    @if($notifications->count() > 0 || $unreadMessageCount > 0)
      <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
        {{ $notifications->count() + $unreadMessageCount }}
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
    <div class="max-h-96 overflow-y-auto" id="notifications-container">
      @forelse($notifications as $notification)
        <a href="{{ auth()->user()->hasRole('vendor') ? route('vendor.messages') : route('client.conversations') }}" 
           onclick="markAsRead('{{ $notification->id }}')"
           class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              @if($notification->type === 'message_received')
                <i class="fas fa-comment-dots text-indigo-600 text-lg"></i>
              @else
                <i class="fas fa-bell text-yellow-600 text-lg"></i>
              @endif
            </div>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-gray-900">
                @if($notification->type === 'message_received')
                  {{ $notification->data['sender_name'] ?? 'Someone' }}
                @else
                  {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                @endif
              </p>
              <p class="text-sm text-gray-600 mt-1">
                @if($notification->type === 'message_received')
                  {{ $notification->data['message_preview'] ?? 'New message' }}
                @else
                  {{ Str::limit($notification->data['content'] ?? 'New notification', 60) }}
                @endif
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
    @if($notifications->count() > 0 || $unreadMessageCount > 0)
      <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
        <a href="{{ auth()->user()->hasRole('vendor') ? route('vendor.messages') : route('client.conversations') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
          View all messages →
        </a>
      </div>
    @endif
  </div>
</div>

<script>
function loadNotifications() {
  fetch('/notifications/unread')
    .then(response => response.json())
    .then(data => {
      // Update notification count
      const bellButton = document.querySelector('[x-data] button');
      const countSpan = bellButton.querySelector('span');
      
      if (data.total_unread > 0) {
        if (!countSpan) {
          const newSpan = document.createElement('span');
          newSpan.className = 'absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full';
          bellButton.appendChild(newSpan);
        }
        bellButton.querySelector('span').textContent = data.total_unread;
      } else if (countSpan) {
        countSpan.remove();
      }
      
      // Update notifications list
      updateNotificationsList(data.notifications);
    });
}

function updateNotificationsList(notifications) {
  const container = document.getElementById('notifications-container');
  
  if (notifications.length === 0) {
    container.innerHTML = `
      <div class="px-4 py-8 text-center">
        <i class="fas fa-bell-slash text-gray-300 text-4xl mb-2"></i>
        <p class="text-gray-500 text-sm">No new notifications</p>
      </div>
    `;
    return;
  }
  
  container.innerHTML = notifications.map(notification => {
    const icon = notification.type === 'message_received' ? 'fas fa-comment-dots text-indigo-600' : 'fas fa-bell text-yellow-600';
    const title = notification.type === 'message_received' ? (notification.data.sender_name || 'Someone') : ucfirst(notification.type.replace('_', ' '));
    const content = notification.type === 'message_received' ? (notification.data.message_preview || 'New message') : (notification.data.content || 'New notification');
    
    return `
      <a href="${getNotificationUrl(notification.type)}" 
         onclick="markAsRead('${notification.id}')"
         class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <i class="${icon} text-lg"></i>
          </div>
          <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-gray-900">${title}</p>
            <p class="text-sm text-gray-600 mt-1">${content}</p>
            <p class="text-xs text-gray-400 mt-1">${notification.time_ago}</p>
          </div>
        </div>
      </a>
    `;
  }).join('');
}

const notificationUrl = '{{ auth()->user()->hasRole("vendor") ? route("vendor.messages") : route("client.conversations") }}';

function getNotificationUrl(type) {
  return notificationUrl;
}

function ucfirst(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function markAsRead(notificationId) {
  fetch(`/notifications/${notificationId}/read`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  }).then(() => {
    loadNotifications();
  });
}

function markAllAsRead() {
  fetch('/notifications/read-all', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    }
  }).then(() => {
    loadNotifications();
  });
}

// Auto-refresh notifications every 30 seconds
setInterval(loadNotifications, 30000);
</script>

