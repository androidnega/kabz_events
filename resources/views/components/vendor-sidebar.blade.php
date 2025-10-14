@props(['vendor', 'averageRating' => 0, 'totalReviews' => 0, 'averageResponseTime' => null])

<div class="space-y-3" x-data="vendorSidebar({{ $vendor->id }}, '{{ route('messages.store') }}', '{{ route('reports.store') }}')">
  {{-- Vendor Profile Card with Business Details --}}
  <div class="bg-white p-3 rounded-2xl shadow">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-100 to-teal-100 flex items-center justify-center border border-gray-200">
        <span class="text-xl font-bold text-purple-600">
          {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
        </span>
      </div>
      <div class="flex-1">
        <div class="flex items-center gap-1 mb-1">
          <h3 class="font-semibold text-[15px] text-gray-900">{{ $vendor->business_name }}</h3>
          @if($vendor->is_verified)
            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800 border border-blue-200">
              <i class="fas fa-check-circle mr-0.5"></i> Verified
            </span>
          @endif
        </div>
        <p class="text-[13px] text-gray-500">{{ $vendor->services->first()?->category->name ?? 'Vendor' }}</p>
      </div>
    </div>

    {{-- Stats Section in same card --}}
    <div class="border-t border-gray-100 pt-3 pb-3">
      <div class="grid grid-cols-3 gap-3 text-center">
        <div>
          <p class="text-xl font-bold text-primary">{{ $vendor->services->count() }}</p>
          <p class="text-xs text-gray-500">Services</p>
        </div>
        <div>
          <p class="text-xl font-bold text-accent">{{ number_format($averageRating, 1) }}</p>
          <p class="text-xs text-gray-500">Rating</p>
        </div>
        <div>
          <p class="text-xl font-bold text-secondary">{{ $totalReviews }}</p>
          <p class="text-xs text-gray-500">Reviews</p>
        </div>
      </div>
    </div>

    {{-- Business Details in same card --}}
    <div class="border-t border-gray-100 pt-3 space-y-2 text-[13px]">
      @if($vendor->address)
        <div class="flex items-start">
          <i class="fas fa-map-marker-alt text-gray-400 mr-2 mt-0.5 text-xs"></i>
          <div>
            <p class="text-xs text-gray-500">Location</p>
            <p class="text-gray-900">{{ $vendor->address }}</p>
          </div>
        </div>
      @endif
      <div class="flex items-start">
        <i class="fas fa-clock text-gray-400 mr-2 mt-0.5 text-xs"></i>
        <div>
          <p class="text-xs text-gray-500">Member Since</p>
          <p class="text-gray-900">{{ $vendor->created_at->format('F Y') }}</p>
        </div>
      </div>
      @if($averageResponseTime)
        <div class="flex items-start">
          <i class="fas fa-reply text-gray-400 mr-2 mt-0.5 text-xs"></i>
          <div>
            <p class="text-xs text-gray-500">Response Time</p>
            <p class="text-gray-900">Usually replies in {{ $averageResponseTime }}</p>
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- Action Buttons Card - 2 per row --}}
  <div class="bg-white p-3 rounded-2xl shadow">
    <div class="grid grid-cols-2 gap-2">
      @auth
        {{-- Authenticated users see real buttons --}}
        {{-- Call Button --}}
        @if($vendor->phone)
          <a href="tel:{{ $vendor->phone }}">
            <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
              <i class="fas fa-phone mr-1 text-xs"></i> Call
            </button>
          </a>
        @endif

        {{-- WhatsApp Button --}}
        @if($vendor->whatsapp && $vendor->canShowWhatsApp())
          @php
            $whatsappNumber = preg_replace('/[^0-9+]/', '', $vendor->whatsapp);
            if (str_starts_with($whatsappNumber, '0')) {
              $whatsappNumber = '233' . substr($whatsappNumber, 1);
            }
            $whatsappNumber = ltrim($whatsappNumber, '+');
          @endphp
          <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank">
            <button class="w-full bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
              <i class="fab fa-whatsapp mr-1 text-xs"></i> WhatsApp
            </button>
          </a>
        @endif

        {{-- Start Chat Button --}}
        <button @click="toggleChat" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
          <i class="fas fa-comment-dots mr-1 text-xs"></i> 
          <span x-text="chatOpen ? 'Close Chat' : 'Start Chat'"></span>
        </button>

        {{-- Report Button --}}
        <button @click="reportOpen = true" class="w-full bg-red-600 hover:bg-red-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
          <i class="fas fa-flag mr-1 text-xs"></i> Report
        </button>
      @else
        {{-- Unauthenticated users see login prompts --}}
        @if($vendor->phone)
          <button @click="showLoginModal = true" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
            <i class="fas fa-phone mr-1 text-xs"></i> Call
          </button>
        @endif

        @if($vendor->whatsapp && $vendor->canShowWhatsApp())
          <button @click="showLoginModal = true" class="w-full bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
            <i class="fab fa-whatsapp mr-1 text-xs"></i> WhatsApp
          </button>
        @endif

        <button @click="showLoginModal = true" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
          <i class="fas fa-comment-dots mr-1 text-xs"></i> Start Chat
        </button>

        <button @click="showLoginModal = true" class="w-full bg-red-600 hover:bg-red-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
          <i class="fas fa-flag mr-1 text-xs"></i> Report
        </button>
      @endauth

      {{-- Login Modal --}}
      <div x-show="showLoginModal" 
           x-cloak
           @click.away="showLoginModal = false"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" 
           style="display: none;">
        
        <!-- Modal panel - Centered and Compact -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xs mx-auto overflow-hidden" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop>
          <!-- Header Section -->
          <div class="bg-white px-5 py-6">
            <div class="text-center">
              <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-3">
                <i class="fas fa-lock text-indigo-600 text-lg"></i>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-1.5">
                Login Required
              </h3>
              <p class="text-xs text-gray-600 leading-relaxed px-2">
                Please sign in to contact this vendor and access full details
              </p>
            </div>
          </div>
          
          <!-- Actions Section -->
          <div class="bg-gray-50 px-5 py-4 space-y-2.5 rounded-b-2xl">
            <a href="{{ route('login') }}" class="block w-full">
              <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg font-medium transition shadow-sm text-sm">
                <i class="fas fa-sign-in-alt mr-1.5"></i> Sign In
              </button>
            </a>
            
            <a href="{{ route('register') }}" class="block w-full">
              <button class="w-full bg-white hover:bg-gray-50 text-indigo-600 border-2 border-indigo-600 px-4 py-2.5 rounded-lg font-medium transition text-sm">
                <i class="fas fa-user-plus mr-1.5"></i> Create Account
              </button>
            </a>

            <!-- Divider -->
            <div class="pt-2 pb-1">
              <div class="border-t border-gray-200"></div>
            </div>

            <!-- Vendor Registration Link -->
            <div class="text-center py-1">
              <p class="text-xs text-gray-600">
                Are you a vendor? 
                <a href="{{ route('vendor.public.register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold underline">
                  Register here
                </a>
              </p>
            </div>

            <!-- Close Button -->
            <button @click="showLoginModal = false" class="w-full text-gray-500 hover:text-gray-700 px-3 py-2 text-xs font-medium hover:bg-gray-100 rounded-lg transition">
              <i class="fas fa-times mr-1"></i> Maybe Later
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Inline Chat Form (for authenticated users) --}}
  @auth
    <div x-show="chatOpen" x-cloak class="mt-3 bg-white p-3 rounded-2xl shadow" style="display: none;">
      <!-- Chat Header -->
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-2">
          <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
            {{ strtoupper(substr($vendor->business_name, 0, 2)) }}
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-900">{{ $vendor->business_name }}</p>
            <div class="flex items-center text-xs">
              <div x-show="vendorStatus.is_online" class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
              <div x-show="!vendorStatus.is_online" class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
              <span x-text="vendorStatus.last_seen" class="text-gray-500"></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Messages Area -->
      <div class="h-48 overflow-y-auto p-2 bg-gray-50 rounded-lg mb-3" x-ref="messagesContainer">
        <template x-if="messages.length === 0 && !loadingMessages">
          <div class="text-center text-gray-500 py-6">
            <i class="fas fa-comments text-gray-400 text-2xl mb-2"></i>
            <p class="text-sm">No messages yet</p>
            <p class="text-xs">Start the conversation!</p>
          </div>
        </template>

        <div class="space-y-2">
          <template x-for="message in messages" :key="message.id">
            <div :class="message.sender_id === {{ auth()->id() }} ? 'flex justify-end' : 'flex justify-start'">
              <div :class="message.sender_id === {{ auth()->id() }} ? 'bg-purple-600 text-white' : 'bg-white text-gray-900 border'" class="max-w-[80%] rounded-lg px-3 py-2 text-sm">
                <template x-if="message.media_type === 'image'">
                  <img :src="message.media_url" class="max-w-full rounded mb-1" alt="Image">
                </template>
                <template x-if="message.media_type === 'audio'">
                  <audio controls class="max-w-full mb-1">
                    <source :src="message.media_url">
                  </audio>
                </template>
                <template x-if="message.message">
                  <p x-text="message.message"></p>
                </template>
                <p :class="message.sender_id === {{ auth()->id() }} ? 'text-purple-100' : 'text-gray-500'" class="text-xs mt-1" x-text="message.time_ago || 'Just now'"></p>
              </div>
            </div>
          </template>
        </div>

        <template x-if="loadingMessages">
          <div class="text-center py-2">
            <i class="fas fa-spinner fa-spin text-purple-600"></i>
          </div>
        </template>
      </div>

      <!-- Message Input -->
      <div x-show="attachmentPreview" x-cloak class="mb-2 p-2 bg-gray-100 rounded text-xs" x-html="attachmentPreview"></div>
      
      <div class="flex items-end space-x-1">
        <input type="file" x-ref="imageInput" @change="handleImageSelect" accept="image/*" class="hidden">
        <input type="file" x-ref="audioInput" @change="handleAudioSelect" accept="audio/*" class="hidden">
        
        <button @click="$refs.imageInput.click()" type="button" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded">
          <i class="fas fa-image text-sm"></i>
        </button>
        
        <button @click="$refs.audioInput.click()" type="button" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded">
          <i class="fas fa-microphone text-sm"></i>
        </button>
        
        <textarea 
          x-model="chatMessage" 
          @keydown.enter.prevent="sendMessage"
          rows="2" 
          placeholder="Type a message..."
          class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-purple-500 focus:border-transparent resize-none text-sm"
        ></textarea>
        
        <button @click="sendMessage" :disabled="chatSending || (!chatMessage.trim() && !selectedFile)" class="p-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:bg-gray-400 transition">
          <i class="fas fa-paper-plane text-sm"></i>
        </button>
      </div>
    </div>

    {{-- Report Modal (for authenticated users) --}}
    <div x-show="reportOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
      <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="reportOpen = false"></div>
      <div class="relative bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl mx-auto">
        <h4 class="font-semibold text-gray-900 mb-4 text-base sm:text-lg">Report this Vendor</h4>
        <select x-model="reportCategory" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-red-500 mb-3">
          <option value="">Select a reason</option>
          <option value="fraud">Fraud or Scam</option>
          <option value="quality">Poor Service Quality</option>
          <option value="payment">Payment Issues</option>
          <option value="unprofessional">Unprofessional Behavior</option>
          <option value="fake">Fake/Misleading Information</option>
          <option value="other">Other</option>
        </select>
        <textarea x-model="reportMessage" placeholder="Provide details about your concern..." rows="4"
                  class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-red-500 mb-4"></textarea>
        <div class="flex justify-end space-x-2">
          <button @click="reportOpen = false; reportCategory = ''; reportMessage = ''" class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 hover:bg-gray-100 rounded-lg transition">Cancel</button>
          <button @click="submitReport" :disabled="reportSubmitting || !reportCategory || !reportMessage.trim()" 
                  class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            <i class="fas fa-paper-plane mr-1"></i> Submit
          </button>
        </div>
        <template x-if="reportSubmitted">
          <div class="text-sm text-green-700 bg-green-50 p-3 rounded-lg border border-green-200 mt-3">
            <i class="fas fa-check-circle mr-1"></i> Report submitted!
          </div>
        </template>
      </div>
    </div>
  @endauth

  {{-- Safety Tips Card --}}
  <div class="bg-white p-3 rounded-2xl shadow">
    <h4 class="font-semibold text-gray-900 mb-3 text-[13px]">Safety Tips</h4>
    <ul class="text-xs text-gray-700 space-y-2">
      <li class="flex items-start">
        <svg class="w-3 h-3 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Avoid sending any prepayments
      </li>
      <li class="flex items-start">
        <svg class="w-3 h-3 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Meet with the seller at a safe public place
      </li>
      <li class="flex items-start">
        <svg class="w-3 h-3 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Inspect what you're going to buy to make sure it's what you need
      </li>
      <li class="flex items-start">
        <svg class="w-3 h-3 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Check all the docs and only pay if you're satisfied
      </li>
    </ul>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('vendorSidebar', (vendorId, messagesRoute, reportsRoute) => ({
    showLoginModal: false,
    chatOpen: false,
    reportOpen: false,
    chatMessage: '',
    chatSent: false,
    chatSending: false,
    reportCategory: '',
    reportMessage: '',
    reportSubmitted: false,
    reportSubmitting: false,
    messages: [],
    loadingMessages: false,
    messagesInterval: null,
    selectedFile: null,
    selectedFileType: null,
    attachmentPreview: '',
    vendorStatus: {
      is_online: false,
      last_seen: 'Offline'
    },
    
    init() {
      // Watch for chat open
      this.$watch('chatOpen', (value) => {
        if (value) {
          this.loadMessages();
          this.startMessagesRefresh();
        } else {
          this.stopMessagesRefresh();
        }
      });
    },
    
    toggleChat() {
      this.chatOpen = !this.chatOpen;
    },
    
    
    async loadMessages() {
      this.loadingMessages = true;
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      try {
        const res = await fetch(`/dashboard/messages/vendor/${vendorId}`, {
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          }
        });
        
        const data = await res.json();
        this.messages = data.messages || [];
        this.vendorStatus = data.vendor_status || { is_online: false, last_seen: 'Offline' };
        
        // Scroll to bottom
        this.$nextTick(() => {
          const container = this.$refs.messagesContainer;
          if (container) {
            container.scrollTop = container.scrollHeight;
          }
        });
      } catch (e) {
        console.error('Failed to load messages', e);
      } finally {
        this.loadingMessages = false;
      }
    },
    
    startMessagesRefresh() {
      this.messagesInterval = setInterval(() => {
        this.loadMessages();
      }, 3000); // Refresh every 3 seconds
    },
    
    stopMessagesRefresh() {
      if (this.messagesInterval) {
        clearInterval(this.messagesInterval);
        this.messagesInterval = null;
      }
    },
    
    handleImageSelect(event) {
      const file = event.target.files[0];
      if (file) {
        this.selectedFile = file;
        this.selectedFileType = 'image';
        this.attachmentPreview = `<span class="text-sm text-gray-600">📷 ${file.name}</span>`;
      }
    },
    
    handleAudioSelect(event) {
      const file = event.target.files[0];
      if (file) {
        this.selectedFile = file;
        this.selectedFileType = 'audio';
        this.attachmentPreview = `<span class="text-sm text-gray-600">🎵 ${file.name}</span>`;
      }
    },
    
    async sendMessage() {
      if (!this.chatMessage.trim() && !this.selectedFile) {
        return;
      }
      
      this.chatSending = true;
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      try {
        const formData = new FormData();
        formData.append('_token', token);
        
        if (this.chatMessage.trim()) {
          formData.append('message', this.chatMessage);
        }
        
        if (this.selectedFile) {
          if (this.selectedFileType === 'image') {
            formData.append('image', this.selectedFile);
          } else if (this.selectedFileType === 'audio') {
            formData.append('audio', this.selectedFile);
          }
        }
        
        const res = await fetch(`/dashboard/messages/vendor/${vendorId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token
          },
          body: formData
        });
        
        const json = await res.json();
        if (json.success) {
          this.chatMessage = '';
          this.selectedFile = null;
          this.selectedFileType = null;
          this.attachmentPreview = '';
          this.$refs.imageInput.value = '';
          this.$refs.audioInput.value = '';
          this.loadMessages();
        } else {
          alert(json.message || 'Could not send message.');
        }
      } catch (e) {
        console.error(e);
        alert('Could not send message. Try again later.');
      } finally {
        this.chatSending = false;
      }
    },
    
    async submitReport() {
      if (!this.reportCategory || !this.reportMessage.trim()) {
        alert('Please select a category and provide details.');
        return;
      }
      this.reportSubmitting = true;
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      try {
        const res = await fetch(reportsRoute, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({
            vendor_id: vendorId,
            category: this.reportCategory,
            message: this.reportMessage
          })
        });
        
        const json = await res.json();
        if (json.ok) {
          this.reportSubmitted = true;
          this.reportCategory = '';
          this.reportMessage = '';
          setTimeout(() => {
            this.reportSubmitted = false;
            this.reportOpen = false;
          }, 3000);
        } else {
          alert(json.message || 'Could not submit report.');
        }
      } catch (e) {
        console.error(e);
        alert('Could not submit report. Try again later.');
      } finally {
        this.reportSubmitting = false;
      }
    }
  }));
});
</script>
@endpush
