<x-vendor-layout>
    <x-slot name="title">Messages</x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
         data-has-conversations="{{ count($conversations) > 0 ? '1' : '0' }}"
         data-user-id="{{ Auth::id() }}"
         data-csrf-token="{{ csrf_token() }}"
         style="height: calc(100vh - 220px);">
        
        <div class="flex h-full">
            <!-- Conversations List (Left Panel) -->
            <div id="conversations-panel" class="w-full md:w-80 border-r border-gray-200 flex flex-col bg-white">
                <!-- Conversations Header -->
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Conversations</h3>
                </div>
                
                <!-- Conversations List -->
                <div class="flex-1 overflow-y-auto">
                    @forelse($conversations as $conversation)
                    <div class="conversation-item p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer {{ $loop->first ? 'bg-gray-50' : '' }}" 
                         data-client-id="{{ $conversation['client']->id }}"
                         data-client-name="{{ $conversation['client']->name }}">
                        <div class="flex justify-between items-start mb-1">
                            <p class="text-sm font-medium text-gray-900">{{ $conversation['client']->name }}</p>
                            @if($conversation['unread_count'] > 0)
                            <span class="text-xs font-bold text-purple-600">{{ $conversation['unread_count'] }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 truncate">
                            @if($conversation['latest_message']->media_type === 'image')
                                📷 Photo
                            @elseif($conversation['latest_message']->media_type === 'audio')
                                🎵 Audio
                            @else
                                {{ Str::limit($conversation['latest_message']->message, 35) }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">{{ $conversation['latest_message']->timeAgo() }}</p>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <p class="text-sm">No conversations yet</p>
                        <p class="text-xs mt-1">Messages from clients will appear here</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Area (Right Panel) -->
            <div class="flex-1 flex flex-col bg-gray-50 min-w-0">
                <!-- No Conversation Selected -->
                <div id="no-conversation" class="hidden md:flex flex-1 items-center justify-center">
                    <div class="text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-sm">Select a conversation to start messaging</p>
                    </div>
                </div>

                <!-- Chat Container -->
                <div id="chat-container" class="hidden flex-col h-full overflow-hidden">
                    <!-- Chat Header -->
                    <div class="px-4 py-3 border-b border-gray-200 bg-white flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <button onclick="showConversationsList()" class="md:hidden -ml-2 p-2 text-gray-600 hover:text-gray-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <div>
                                <p id="chat-client-name" class="text-sm font-semibold text-gray-900"></p>
                                <p id="chat-client-status" class="text-xs text-gray-600"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Messages (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-3" style="min-height: 0;">
                        <div id="messages-area" class="space-y-2">
                            <!-- Messages loaded here -->
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div id="typing-indicator" class="hidden px-4 py-2 bg-gray-50">
                        <div class="flex items-center space-x-2">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                            <span class="text-xs text-gray-600">Typing...</span>
                        </div>
                    </div>

                    <!-- Message Input (Fixed) -->
                    <div class="flex items-center gap-1.5 px-2 py-2 border-t border-gray-200 bg-white flex-shrink-0">
                        <form id="message-form" class="flex items-center gap-1.5 w-full" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="image-input" accept="image/*" class="hidden">
                            <input type="file" id="audio-input" accept="audio/*" class="hidden">
                            
                            <button type="button" id="attach-image-btn" class="flex-shrink-0 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </button>
                            
                            <button type="button" id="attach-audio-btn" class="flex-shrink-0 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                </svg>
                            </button>
                            
                            <input 
                                type="text"
                                id="message-input" 
                                class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500" 
                                placeholder="Type message..."
                                autocomplete="off"
                                style="font-size: 16px;"
                            />
                            
                            <button type="submit" class="flex-shrink-0 px-3 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700" style="font-size: 14px;">
                                Send
                            </button>
                        </form>
                    </div>
                    <div id="attachment-preview" class="hidden px-3 py-1 text-xs text-gray-600 bg-gray-50 border-t border-gray-200"></div>
                </div>
            </div>
        </div>
    </div>

<script>
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
        document.getElementById('conversations-panel').classList.remove('hidden');
        document.getElementById('chat-container').classList.add('hidden');
        
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
            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('bg-gray-50'));
            this.classList.add('bg-gray-50');
            
            // Hide no-conversation message
            document.getElementById('no-conversation').classList.add('hidden');
            
            // Show chat
            const chatContainer = document.getElementById('chat-container');
            chatContainer.classList.remove('hidden');
            chatContainer.classList.add('flex');
            
            // On mobile, hide conversations panel
            if (window.innerWidth < 768) {
                document.getElementById('conversations-panel').classList.add('hidden');
            }
            
            // Update header
            document.getElementById('chat-client-name').textContent = clientName;
            
            // Load messages
            loadMessages();
            
            // Start auto-refresh
            if (messagesRefreshInterval) clearInterval(messagesRefreshInterval);
            messagesRefreshInterval = setInterval(loadMessages, 3000);
        });
    });
    
    // Auto-load first conversation (desktop only)
    document.addEventListener('DOMContentLoaded', function() {
        // Clear message notifications
        fetch('/dashboard/notifications/messages/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        if (hasConversations && window.innerWidth >= 768) {
            document.querySelector('.conversation-item')?.click();
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
                const typingEl = document.getElementById('typing-indicator');
                if (data.is_typing) {
                    typingEl.classList.remove('hidden');
                } else {
                    typingEl.classList.add('hidden');
                }
            });
    }
    
    function displayMessages(messages) {
        const container = document.getElementById('messages-area');
        const shouldScroll = (container.scrollHeight - container.scrollTop - container.clientHeight) < 100;
        
        container.innerHTML = messages.map(msg => {
            const isVendor = msg.sender_id === currentUserId;
            
            let content = '';
            if (msg.media_type === 'image') {
                content = `<img src="${msg.media_url}" class="max-w-xs rounded-lg" alt="Image">`;
            } else if (msg.media_type === 'audio') {
                content = `<audio controls class="max-w-xs"><source src="${msg.media_url}"></audio>`;
            } else {
                content = `${msg.message || ''}`;
            }
            
            if (isVendor) {
                return `
                    <div class="flex justify-end">
                        <div class="bg-purple-600 text-white rounded-lg px-4 py-2 max-w-sm">
                            <div class="text-sm">${content}</div>
                            <div class="text-xs opacity-75 mt-1 text-right">${msg.time_ago || 'Just now'}</div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div class="flex justify-start">
                        <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 max-w-sm">
                            <div class="text-sm text-gray-900">${content}</div>
                            <div class="text-xs text-gray-500 mt-1">${msg.time_ago || 'Just now'}</div>
                        </div>
                    </div>
                `;
            }
        }).join('');
        
        if (shouldScroll) {
            container.parentElement.scrollTop = container.parentElement.scrollHeight;
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
                document.getElementById('attachment-preview').innerHTML = '';
                document.getElementById('attachment-preview').classList.add('hidden');
                loadMessages();
            }
        });
    });
    
    // File attachments
    document.getElementById('attach-image-btn').addEventListener('click', () => {
        document.getElementById('image-input').click();
    });
    
    document.getElementById('attach-audio-btn').addEventListener('click', () => {
        document.getElementById('audio-input').click();
    });
    
    document.getElementById('image-input').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const preview = document.getElementById('attachment-preview');
            preview.innerHTML = `📷 ${e.target.files[0].name}`;
            preview.classList.remove('hidden');
        }
    });
    
    document.getElementById('audio-input').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const preview = document.getElementById('attachment-preview');
            preview.innerHTML = `🎵 ${e.target.files[0].name}`;
            preview.classList.remove('hidden');
        }
    });
    
    // Typing indicator
    document.getElementById('message-input').addEventListener('input', function() {
        if (!isTyping && currentClientId) {
            isTyping = true;
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
