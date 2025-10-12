<x-vendor-layout>
  <x-slot name="title">Messages</x-slot>

    <div data-has-conversations="{{ count($conversations) > 0 ? '1' : '0' }}"
         data-user-id="{{ Auth::id() }}"
         data-csrf-token="{{ csrf_token() }}">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Messages</h2>
                <p class="mt-1 text-sm text-gray-600">Communicate with your clients</p>
            </div>
            <div class="text-sm text-gray-500">
                <i class="fas fa-circle text-green-500 mr-1"></i> Online
            </div>
  </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden" style="height: calc(100vh - 280px);">
            <div class="flex h-full flex-col md:flex-row">
                <!-- Conversations List -->
                <div class="w-full md:w-1/3 border-r border-gray-200 overflow-y-auto">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Conversations</h2>
                    </div>
                    
                    <div id="conversations-list" class="divide-y divide-gray-200">
                        @forelse($conversations as $conversation)
                        <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer transition-colors {{ $loop->first ? 'bg-blue-50' : '' }}" 
                             data-client-id="{{ $conversation['client']->id }}"
                             data-client-name="{{ $conversation['client']->name }}">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($conversation['client']->name, 0, 2)) }}
                                    </div>
                                </div>
                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $conversation['client']->name }}
                                        </p>
                                        @if($conversation['unread_count'] > 0)
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                            {{ $conversation['unread_count'] }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <div class="w-2 h-2 rounded-full mr-2 {{ $conversation['client_status']['is_online'] ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                        <span class="text-xs text-gray-500">{{ $conversation['client_status']['last_seen'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 truncate mt-1">
                                        @if($conversation['latest_message']->media_type === 'image')
                                            📷 Photo
                                        @elseif($conversation['latest_message']->media_type === 'audio')
                                            🎵 Audio
                                        @else
                                            {{ Str::limit($conversation['latest_message']->message, 30) }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $conversation['latest_message']->timeAgo() }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <p class="mt-2">No conversations yet</p>
                            <p class="text-sm">Messages from clients will appear here</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 flex flex-col">
                    <!-- No conversation selected (desktop only) -->
                    <div id="no-conversation" class="flex-1 hidden md:flex items-center justify-center text-gray-500">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Select a conversation</h3>
                            <p class="mt-2">Choose a conversation from the list to start messaging</p>
                        </div>
                    </div>

                    <!-- Chat Container (hidden initially on mobile) -->
                    <div id="chat-container" class="flex-1 flex-col hidden">
                        <!-- Chat Header -->
                        <div class="p-4 border-b border-gray-200 bg-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <!-- Back button (mobile only) -->
                                    <button onclick="showConversationsList()" class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        <span id="chat-avatar"></span>
                                    </div>
                                    <div>
                                        <h3 id="chat-client-name" class="text-lg font-semibold text-gray-900"></h3>
                                        <p id="chat-client-status" class="text-sm text-gray-600"></p>
              </div>
                  </div>
                </div>
              </div>

                        <!-- Messages Area -->
                        <div id="messages-area" class="flex-1 overflow-y-auto p-4 bg-gray-50" style="max-height: calc(100vh - 400px);">
                            <!-- Messages will be loaded here -->
                        </div>

                        <!-- Typing Indicator -->
                        <div id="typing-indicator" class="hidden px-4 py-2 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center space-x-2">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                                <span class="text-sm text-gray-500">Client is typing...</span>
                            </div>
          </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t border-gray-200 bg-white">
                            <form id="message-form" enctype="multipart/form-data">
                                @csrf
                                <div class="flex items-end space-x-2">
                                    <input type="file" id="image-input" accept="image/*" class="hidden">
                                    <input type="file" id="audio-input" accept="audio/*" class="hidden">
                                    
                                    <button type="button" id="attach-image-btn" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    
                                    <button type="button" id="attach-audio-btn" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                        </svg>
                                    </button>
                                    
                                    <div class="flex-1">
                                        <textarea 
                                            id="message-input" 
                                            rows="1" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                                            placeholder="Type a message..."
                                        ></textarea>
                                        <div id="attachment-preview" class="mt-2 hidden"></div>
                                    </div>
                                    
                                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                        Send
              </button>
                                </div>
                            </form>
                        </div>
                    </div>
            </div>
          </div>
        </div>
      </div>

      <script>
    // Get data from HTML attributes
    const pageContainer = document.querySelector('[data-has-conversations]');
    const hasConversations = pageContainer.dataset.hasConversations === '1';
    const currentUserId = parseInt(pageContainer.dataset.userId);
    const csrfToken = pageContainer.dataset.csrfToken;
    
        let currentClientId = null;
        let messagesRefreshInterval = null;
        let typingTimeout = null;
        let isTyping = false;
    
    // Show conversations list (mobile back button)
    function showConversationsList() {
        const conversationsList = document.getElementById('conversations-list').parentElement;
        const chatContainer = document.getElementById('chat-container');
        
        conversationsList.classList.remove('hidden', 'md:block');
        conversationsList.classList.add('block');
        chatContainer.classList.add('hidden');
        chatContainer.classList.remove('flex');
        chatContainer.parentElement.classList.add('hidden');
        
        // Stop auto-refresh when going back
        if (messagesRefreshInterval) {
            clearInterval(messagesRefreshInterval);
            messagesRefreshInterval = null;
        }
    }
    
    // Handle conversation selection
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            currentClientId = this.dataset.clientId;
            const clientName = this.dataset.clientName;
            
            // Update active state
            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('bg-blue-50'));
            this.classList.add('bg-blue-50');
            
            // Show chat container
            document.getElementById('no-conversation').classList.add('hidden');
            document.getElementById('no-conversation').classList.remove('md:flex');
            document.getElementById('chat-container').classList.remove('hidden');
            document.getElementById('chat-container').classList.add('flex');
            
            // On mobile, hide conversation list and show chat
            if (window.innerWidth < 768) {
                const conversationsList = document.getElementById('conversations-list').parentElement;
                conversationsList.classList.add('hidden', 'md:block');
                document.getElementById('chat-container').parentElement.classList.remove('hidden');
                document.getElementById('chat-container').parentElement.classList.add('flex');
            }
            
            // Update chat header
            document.getElementById('chat-avatar').textContent = clientName.substring(0, 2).toUpperCase();
            document.getElementById('chat-client-name').textContent = clientName;
            
            // Load messages
            loadMessages();
            
            // Start auto-refresh
            if (messagesRefreshInterval) {
                clearInterval(messagesRefreshInterval);
            }
            messagesRefreshInterval = setInterval(loadMessages, 3000);
        });
    });
    
    // Load first conversation if exists (desktop only)
    document.addEventListener('DOMContentLoaded', function () {
        // Mark message notifications as read
        fetch('/dashboard/notifications/messages/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        if (hasConversations && window.innerWidth >= 768) {
            let firstConversation = document.querySelector('.conversation-item');
            if (firstConversation) {
                firstConversation.click();
            }
        }
    });

    function loadMessages() {
        if (!currentClientId) return;
        fetch(`/dashboard/messages/client/${currentClientId}`)
            .then(response => response.json())
            .then(data => {
                displayMessages(data.messages);
                document.getElementById('chat-client-status').textContent = data.client_status.last_seen;
                
                // Show/hide typing indicator
                const typingIndicator = document.getElementById('typing-indicator');
                if (data.is_typing) {
                    typingIndicator.classList.remove('hidden');
                } else {
                    typingIndicator.classList.add('hidden');
                }
            });
    }
    
    function displayMessages(messages) {
        const container = document.getElementById('messages-area');
        const scrollAtBottom = container.scrollHeight - container.scrollTop === container.clientHeight;
        
        container.innerHTML = messages.map(msg => {
            const isVendor = msg.sender_id === currentUserId;
            const alignClass = isVendor ? 'justify-end' : 'justify-start';
            const bgClass = isVendor ? 'bg-blue-600 text-white' : 'bg-white text-gray-900';
            
            let content = '';
            if (msg.media_type === 'image') {
                content = `<img src="${msg.media_url}" class="max-w-xs rounded-lg" alt="Image">`;
            } else if (msg.media_type === 'audio') {
                content = `<audio controls class="max-w-xs"><source src="${msg.media_url}"></audio>`;
              } else {
                content = `<p>${msg.message}</p>`;
            }
            
            return `
                <div class="flex ${alignClass} mb-4">
                    <div class="${bgClass} rounded-lg px-4 py-2 max-w-md shadow-sm">
                        ${content}
                        <p class="text-xs mt-1 ${isVendor ? 'text-blue-100' : 'text-gray-500'}">${msg.time_ago || 'Just now'}</p>
                    </div>
                </div>
            `;
        }).join('');
        
        if (scrollAtBottom || messages.length === 0) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    // Handle message sending
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('message-input');
        const message = messageInput.value.trim();
        const imageInput = document.getElementById('image-input');
        const audioInput = document.getElementById('audio-input');
        
        if (!message && !imageInput.files.length && !audioInput.files.length) return;
        
        const formData = new FormData();
        formData.append('_token', csrfToken);
        
        if (message) formData.append('message', message);
        if (imageInput.files.length) formData.append('image', imageInput.files[0]);
        if (audioInput.files.length) formData.append('audio', audioInput.files[0]);
        
        fetch(`/dashboard/messages/client/${currentClientId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                imageInput.value = '';
                audioInput.value = '';
                document.getElementById('attachment-preview').classList.add('hidden');
                loadMessages();
            }
        });
    });
    
    // Handle file attachments
    document.getElementById('attach-image-btn').addEventListener('click', function() {
        document.getElementById('image-input').click();
    });
    
    document.getElementById('attach-audio-btn').addEventListener('click', function() {
        document.getElementById('audio-input').click();
    });
    
    document.getElementById('image-input').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const preview = document.getElementById('attachment-preview');
            preview.innerHTML = `<span class="text-sm text-gray-600">📷 ${e.target.files[0].name}</span>`;
            preview.classList.remove('hidden');
        }
    });
    
        document.getElementById('audio-input').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const preview = document.getElementById('attachment-preview');
                preview.innerHTML = `<span class="text-sm text-gray-600">🎵 ${e.target.files[0].name}</span>`;
                preview.classList.remove('hidden');
            }
        });
        
        // Typing indicator
        document.getElementById('message-input').addEventListener('input', function() {
            if (!isTyping && currentClientId) {
                isTyping = true;
                // Send typing signal to server
                fetch(`/dashboard/messages/typing/${currentClientId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            }
            
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                isTyping = false;
                if (currentClientId) {
                    // Send stop typing signal
                    fetch(`/dashboard/messages/stop-typing/${currentClientId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                }
            }, 1000);
        });
      </script>
</x-app-layout>
