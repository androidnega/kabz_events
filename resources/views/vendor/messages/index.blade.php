<x-vendor-layout>
    <x-slot name="title">Messages</x-slot>

    <div class="flex flex-col bg-white border border-gray-200 rounded-lg overflow-hidden" 
         data-has-conversations="{{ count($conversations) > 0 ? '1' : '0' }}"
         data-user-id="{{ Auth::id() }}"
         data-csrf-token="{{ csrf_token() }}"
         style="height: 600px; max-height: calc(100vh - 200px);">
        
        <div class="flex flex-1 min-h-0">
                <!-- Conversations List -->
                <div class="w-full md:w-1/3 border-r border-gray-200 flex flex-col">
                    <div class="p-4 border-b border-gray-200 flex-shrink-0">
                        <h2 class="text-lg font-semibold text-gray-900">Conversations</h2>
                    </div>

                    <div id="conversations-list" class="flex-1 overflow-y-auto divide-y divide-gray-200">
                        @forelse($conversations as $conversation)
                        <div class="conversation-item p-3 hover:bg-gray-50 cursor-pointer border-l-2 {{ $loop->first ? 'border-gray-900 bg-gray-50' : 'border-transparent' }}" 
                             data-client-id="{{ $conversation['client']->id }}"
                             data-client-name="{{ $conversation['client']->name }}">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $conversation['client']->name }}
                                </p>
                                @if($conversation['unread_count'] > 0)
                                <span class="text-xs font-bold text-gray-900">
                                    {{ $conversation['unread_count'] }}
                                </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 truncate">
                                @if($conversation['latest_message']->media_type === 'image')
                                    📷 Photo
                                @elseif($conversation['latest_message']->media_type === 'audio')
                                    🎵 Audio
                                @else
                                    {{ Str::limit($conversation['latest_message']->message, 40) }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $conversation['latest_message']->timeAgo() }}</p>
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
                <div class="flex-1 flex flex-col min-h-0">
                    <!-- No conversation selected (desktop only) -->
                    <div id="no-conversation" class="flex-1 hidden md:flex items-center justify-center text-gray-500 bg-gray-50">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Select a conversation</h3>
                            <p class="mt-2">Choose a conversation from the list to start messaging</p>
                        </div>
                    </div>

                    <!-- Chat Container (hidden initially on mobile) -->
                    <div id="chat-container" class="flex flex-col hidden h-full">
                        <!-- Chat Header (Fixed) -->
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 flex-shrink-0 bg-white">
                            <div class="flex items-center space-x-3">
                                <button onclick="showConversationsList()" class="md:hidden p-1 text-gray-600 hover:text-gray-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <div>
                                    <p id="chat-client-name" class="text-sm font-medium text-gray-900"></p>
                                    <p id="chat-client-status" class="text-xs text-gray-600"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Area (Scrollable Only) -->
                        <div class="flex-1 overflow-hidden bg-gray-50">
                            <div id="messages-area" class="h-full overflow-y-auto p-4 space-y-3">
                                <!-- Messages will be loaded here -->
                            </div>
                        </div>

                        <!-- Typing Indicator (Fixed) -->
                        <div id="typing-indicator" class="hidden px-4 py-2 bg-gray-50 border-t border-gray-200 flex-shrink-0">
                            <div class="flex items-center space-x-2">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                                <span class="text-sm text-gray-500">Client is typing...</span>
                            </div>
                        </div>

                        <!-- Message Input (Fixed at Bottom) -->
                        <div class="flex items-center px-4 py-3 border-t border-gray-200 bg-white flex-shrink-0">
                            <form id="message-form" class="flex items-center w-full space-x-3" enctype="multipart/form-data">
                                @csrf
                                <input type="file" id="image-input" accept="image/*" class="hidden">
                                <input type="file" id="audio-input" accept="audio/*" class="hidden">
                                
                                <button type="button" id="attach-image-btn" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" title="Attach image">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                
                                <button type="button" id="attach-audio-btn" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" title="Attach audio">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                    </svg>
                                </button>
                                
                                <div class="flex-1 relative">
                                    <textarea 
                                        id="message-input" 
                                        rows="1" 
                                        class="w-full resize-none px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                                        placeholder="Type a message… (Enter to send)"
                                        style="min-height: 44px; max-height: 120px;"
                                    ></textarea>
                                    <div id="attachment-preview" class="hidden absolute -top-8 left-0 text-xs text-gray-600 bg-white px-2 py-1 rounded border"></div>
                                </div>
                                
                                <button type="submit" class="px-6 py-3 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                                    Send
                                </button>
                            </form>
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
        
        // Show conversation list, hide chat
        conversationsList.classList.remove('hidden');
        chatContainer.classList.add('hidden');
        chatContainer.classList.remove('flex');
        
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
            const noConv = document.getElementById('no-conversation');
            const chatContainer = document.getElementById('chat-container');
            
            // Hide "no conversation" message
            noConv.classList.add('hidden');
            
            // Show chat container with flex layout
            chatContainer.classList.remove('hidden');
            chatContainer.classList.add('flex');
            chatContainer.style.display = 'flex'; // Force display
            
            // On mobile, hide conversation list
            if (window.innerWidth < 768) {
                const conversationsList = document.getElementById('conversations-list').parentElement;
                conversationsList.classList.add('hidden');
            }
            
            // Update chat header
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
        const scrollAtBottom = (container.scrollHeight - container.scrollTop - container.clientHeight) < 50;
        
        container.innerHTML = messages.map(msg => {
            const isVendor = msg.sender_id === currentUserId;
            const alignClass = isVendor ? 'justify-end' : 'justify-start';
            
            let content = '';
            if (msg.media_type === 'image') {
                content = `<img src="${msg.media_url}" class="max-w-xs rounded-lg border border-gray-200" alt="Image">`;
            } else if (msg.media_type === 'audio') {
                content = `<audio controls class="max-w-xs"><source src="${msg.media_url}"></audio>`;
            } else {
                content = `<div class="text-sm">${msg.message || ''}</div>`;
            }
            
            const bgColor = isVendor ? 'bg-purple-600 text-white' : 'bg-white text-gray-900';
            const timeColor = isVendor ? 'text-purple-100' : 'text-gray-500';
            
            return `
                <div class="flex ${alignClass}">
                    <div class="max-w-[70%] break-words">
                        <div class="px-4 py-3 rounded-lg shadow-sm border ${bgColor}">
                            ${content}
                            <div class="text-xs ${timeColor} mt-2 opacity-75">${msg.time_ago || 'Just now'}</div>
                        </div>
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
        
        // Keyboard: Enter = send, Shift+Enter = newline
        document.getElementById('message-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('message-form').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
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
</x-vendor-layout>
