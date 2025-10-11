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
        @if($vendor->whatsapp)
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

        {{-- Send Message Button --}}
        <button @click="chatOpen = true" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
          <i class="fas fa-comment-dots mr-1 text-xs"></i> Message
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

        @if($vendor->whatsapp)
          <button @click="showLoginModal = true" class="w-full bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
            <i class="fab fa-whatsapp mr-1 text-xs"></i> WhatsApp
          </button>
        @endif

        <button @click="showLoginModal = true" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
          <i class="fas fa-comment-dots mr-1 text-xs"></i> Message
        </button>

        <button @click="showLoginModal = true" class="w-full bg-red-600 hover:bg-red-700 text-white px-2 py-2 rounded-lg text-[13px] font-medium transition flex items-center justify-center">
          <i class="fas fa-flag mr-1 text-xs"></i> Report
        </button>
      @endauth

      {{-- Login Modal --}}
      <div x-show="showLoginModal" 
           x-cloak
           @click.away="showLoginModal = false"
           class="fixed inset-0 z-50 flex items-center justify-center p-4" 
           style="display: none;">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showLoginModal = false"></div>

        <!-- Modal panel - Centered -->
        <div class="relative bg-white rounded-2xl shadow-2xl transform transition-all w-full max-w-md mx-auto">
          <div class="bg-white px-6 py-8 sm:px-8">
            <div class="text-center">
              <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                <i class="fas fa-lock text-indigo-600 text-2xl"></i>
              </div>
              <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                Login Required
              </h3>
              <p class="text-sm sm:text-base text-gray-600">
                Please sign in to contact this vendor and access full details
              </p>
            </div>
          </div>
          
          <div class="bg-gray-50 px-6 py-6 sm:px-8 space-y-3 rounded-b-2xl">
            <a href="{{ route('login') }}" class="block w-full">
              <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-medium transition shadow-sm">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
              </button>
            </a>
            
            <a href="{{ route('register') }}" class="block w-full">
              <button class="w-full bg-white hover:bg-gray-50 text-indigo-600 border-2 border-indigo-600 px-4 py-3 rounded-lg font-medium transition">
                <i class="fas fa-user-plus mr-2"></i> Create Account
              </button>
            </a>

            <div class="text-center pt-2 pb-1">
              <p class="text-sm text-gray-600">
                Are you a vendor? 
                <a href="{{ route('vendor.public.register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                  Register here
                </a>
              </p>
            </div>

            <button @click="showLoginModal = false" class="w-full text-gray-600 hover:text-gray-800 px-4 py-2 text-sm font-medium hover:bg-gray-100 rounded-lg transition">
              Maybe Later
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Message Modal (for authenticated users) --}}
  @auth
    <div x-show="chatOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
      <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="chatOpen = false"></div>
      <div class="relative bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl mx-auto">
        <h4 class="font-semibold text-gray-900 mb-4 text-base sm:text-lg">Send a Message</h4>
        <textarea x-model="chatMessage" placeholder="Write a short message to the vendor..." rows="4"
                  class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 mb-4"></textarea>
        <div class="flex justify-end space-x-2">
          <button @click="chatOpen = false" class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2 hover:bg-gray-100 rounded-lg transition">Cancel</button>
          <button @click="sendMessage" :disabled="chatSending" class="bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white px-5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            <i class="fas fa-paper-plane mr-1"></i> Send
          </button>
        </div>
        <template x-if="chatSent">
          <div class="text-sm text-green-700 bg-green-50 p-3 rounded-lg border border-green-200 mt-3">
            <i class="fas fa-check-circle mr-1"></i> Message sent successfully!
          </div>
        </template>
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
    
    async sendMessage() {
      if (!this.chatMessage.trim()) {
        alert('Please enter a message.');
        return;
      }
      this.chatSending = true;
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      try {
        const res = await fetch(messagesRoute, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({
            vendor_id: vendorId,
            message: this.chatMessage
          })
        });
        
        const json = await res.json();
        if (json.ok) {
          this.chatSent = true;
          this.chatMessage = '';
          setTimeout(() => {
            this.chatSent = false;
            this.chatOpen = false;
          }, 3000);
        } else {
          alert(json.error || 'Could not send message.');
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
