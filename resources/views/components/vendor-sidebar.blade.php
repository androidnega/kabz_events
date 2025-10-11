@props(['vendor', 'averageRating' => 0, 'totalReviews' => 0])

<div class="space-y-3">
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
          <h3 class="font-semibold text-sm text-gray-900">{{ $vendor->business_name }}</h3>
          @if($vendor->is_verified)
            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800 border border-blue-200">
              <i class="fas fa-check-circle mr-0.5"></i> Verified
            </span>
          @endif
        </div>
        <p class="text-xs text-gray-500">{{ $vendor->services->first()?->category->name ?? 'Vendor' }}</p>
      </div>
    </div>

    {{-- Business Details in same card --}}
    <div class="border-t border-gray-100 pt-3 space-y-2 text-xs">
      @if($vendor->address)
        <div class="flex items-start">
          <i class="fas fa-map-marker-alt text-gray-400 mr-2 mt-0.5 text-xs"></i>
          <div>
            <p class="text-[10px] text-gray-500">Location</p>
            <p class="text-gray-900">{{ $vendor->address }}</p>
          </div>
        </div>
      @endif
      <div class="flex items-start">
        <i class="fas fa-clock text-gray-400 mr-2 mt-0.5 text-xs"></i>
        <div>
          <p class="text-[10px] text-gray-500">Member Since</p>
          <p class="text-gray-900">{{ $vendor->created_at->format('F Y') }}</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Stats Card --}}
  <div class="bg-white p-3 rounded-2xl shadow">
    <div class="grid grid-cols-3 gap-3 text-center">
      <div>
        <p class="text-lg font-bold text-primary">{{ $vendor->services->count() }}</p>
        <p class="text-[10px] text-gray-500">Services</p>
      </div>
      <div>
        <p class="text-lg font-bold text-accent">{{ number_format($averageRating, 1) }}</p>
        <p class="text-[10px] text-gray-500">Rating</p>
      </div>
      <div>
        <p class="text-lg font-bold text-secondary">{{ $totalReviews }}</p>
        <p class="text-[10px] text-gray-500">Reviews</p>
      </div>
    </div>
  </div>

  {{-- Action Buttons Card - 2 per row --}}
  <div class="bg-white p-3 rounded-2xl shadow">
    <div class="grid grid-cols-2 gap-2" x-data="{ showLoginModal: false }">
      @auth
        {{-- Authenticated users see real buttons --}}
        {{-- Call Button --}}
        @if($vendor->phone)
          <a href="tel:{{ $vendor->phone }}">
            <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center">
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
            <button class="w-full bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center">
              <i class="fab fa-whatsapp mr-1 text-xs"></i> WhatsApp
            </button>
          </a>
        @endif

        {{-- Send Message Button --}}
        <button x-data="vendorChat({{ $vendor->id }}, {{ auth()->id() }})" @click="open = true" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center">
          <i class="fas fa-comment-dots mr-1 text-xs"></i> Message
        </button>

        {{-- Report Button --}}
        <button x-data="vendorReport({{ $vendor->id }})" @click="open = true" class="w-full bg-red-600 hover:bg-red-700 text-white px-2 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center">
          <i class="fas fa-flag mr-1 text-xs"></i> Report
        </button>
      @else
        {{-- Unauthenticated users see login prompts --}}
        @if($vendor->phone)
          <button @click="showLoginModal = true" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center">
            <i class="fas fa-phone mr-1 text-xs"></i> Call
          </button>
        @endif

        @if($vendor->whatsapp)
          <button @click="showLoginModal = true" class="w-full bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center">
            <i class="fab fa-whatsapp mr-1 text-xs"></i> WhatsApp
          </button>
        @endif

        <button @click="showLoginModal = true" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-2 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center">
          <i class="fas fa-comment-dots mr-1 text-xs"></i> Message
        </button>

        <button @click="showLoginModal = true" class="w-full bg-red-600 hover:bg-red-700 text-white px-2 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center">
          <i class="fas fa-flag mr-1 text-xs"></i> Report
        </button>
      @endauth

      {{-- Login Modal --}}
      <div x-show="showLoginModal" 
           x-cloak
           @click.away="showLoginModal = false"
           class="fixed inset-0 z-50 overflow-y-auto" 
           style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <!-- Background overlay -->
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showLoginModal = false"></div>

          <!-- Modal panel -->
          <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-6 pt-6 pb-4">
              <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                  <i class="fas fa-lock text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                  Login Required
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                  Please sign in to contact this vendor and access full details
                </p>
              </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 space-y-3">
              <a href="{{ route('login') }}" class="block w-full">
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-medium transition">
                  <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </button>
              </a>
              
              <a href="{{ route('register') }}" class="block w-full">
                <button class="w-full bg-white hover:bg-gray-50 text-indigo-600 border-2 border-indigo-600 px-4 py-3 rounded-lg font-medium transition">
                  <i class="fas fa-user-plus mr-2"></i> Create Account
                </button>
              </a>

              <div class="text-center pt-2">
                <p class="text-sm text-gray-600">
                  Are you a vendor? 
                  <a href="{{ route('vendor.public.register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                    Register here
                  </a>
                </p>
              </div>

              <button @click="showLoginModal = false" class="w-full text-gray-600 hover:text-gray-800 px-4 py-2 text-sm font-medium">
                Maybe Later
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Contact Details & Actions (for authenticated users) / Login prompt --}}
  @auth
    @if(auth()->user()->hasRole('client'))
      <div class="bg-white p-4 rounded-2xl shadow">
        <h4 class="font-semibold text-gray-900 mb-3">Contact Details</h4>
        <div class="text-sm space-y-2">
          <div class="flex items-start">
            <i class="fas fa-phone text-indigo-600 mr-2 mt-0.5"></i>
            <div>
              <p class="text-xs text-gray-500">Phone</p>
              <a href="tel:{{ $vendor->phone }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $vendor->phone }}</a>
            </div>
          </div>
          <div class="flex items-start">
            <i class="fas fa-envelope text-indigo-600 mr-2 mt-0.5"></i>
            <div>
              <p class="text-xs text-gray-500">Email</p>
              <a href="mailto:{{ $vendor->user?->email ?? '' }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $vendor->user?->email ?? '—' }}</a>
            </div>
          </div>
        </div>
      </div>
    @endif

    {{-- Send Message Section (all authenticated users) --}}
    <div class="bg-white p-4 rounded-2xl shadow" x-data="vendorChat({{ $vendor->id }}, {{ auth()->id() }})">
      <button x-show="!open" @click="open = true" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-lg font-medium transition flex items-center justify-center">
        <i class="fas fa-comment-dots mr-2"></i> Send Message
      </button>

      <div x-show="open" x-cloak class="space-y-3">
        <h4 class="font-semibold text-gray-900">Send a Message</h4>
        <textarea x-model="message" placeholder="Write a short message to the vendor..." rows="4"
                  class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>

        <div class="flex justify-between items-center">
          <small class="text-xs text-gray-500">
            <i class="fas fa-lock text-gray-400 mr-1"></i> Private message
          </small>
          <div class="space-x-2">
            <button @click="send" :disabled="sending" class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
              <i class="fas fa-paper-plane mr-1"></i> Send
            </button>
            <button @click="open = false" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Cancel</button>
          </div>
        </div>

        <template x-if="sent">
          <div class="text-sm text-green-700 bg-green-50 p-3 rounded-lg border border-green-200">
            <i class="fas fa-check-circle mr-1"></i> Message sent successfully!
          </div>
        </template>
      </div>
    </div>

    {{-- Report Vendor Section (all authenticated users) --}}
    <div class="bg-white p-4 rounded-2xl shadow" x-data="vendorReport({{ $vendor->id }})">
      <button x-show="!open" @click="open = true" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium transition flex items-center justify-center">
        <i class="fas fa-flag mr-2"></i> Report Vendor
      </button>

      <div x-show="open" x-cloak class="space-y-3">
        <h4 class="font-semibold text-gray-900">Report this Vendor</h4>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
          <select x-model="category" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <option value="">Select a reason</option>
            <option value="fraud">Fraud or Scam</option>
            <option value="quality">Poor Service Quality</option>
            <option value="payment">Payment Issues</option>
            <option value="unprofessional">Unprofessional Behavior</option>
            <option value="fake">Fake/Misleading Information</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Details</label>
          <textarea x-model="message" placeholder="Provide details about your concern..." rows="4"
                    class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
        </div>

        <div class="flex justify-between items-center">
          <small class="text-xs text-gray-500">
            <i class="fas fa-shield-alt text-gray-400 mr-1"></i> Reports are reviewed by our team
          </small>
          <div class="space-x-2">
            <button @click="submitReport" :disabled="submitting || !category || !message.trim()" 
                    class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
              <i class="fas fa-paper-plane mr-1"></i> Submit
            </button>
            <button @click="open = false; category = ''; message = ''" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Cancel</button>
          </div>
        </div>

        <template x-if="submitted">
          <div class="text-sm text-green-700 bg-green-50 p-3 rounded-lg border border-green-200">
            <i class="fas fa-check-circle mr-1"></i> Report submitted. Thank you for helping us maintain quality!
          </div>
        </template>
      </div>
    </div>
  @else
    {{-- Send Message Button for unauthenticated users --}}
    <div class="bg-white p-4 rounded-2xl shadow" x-data="{ showLoginModal: false }">
      <button @click="showLoginModal = true" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-lg font-medium transition flex items-center justify-center">
        <i class="fas fa-comment-dots mr-2"></i> Send Message
      </button>

      {{-- Login Modal for Send Message --}}
      <div x-show="showLoginModal" 
           x-cloak
           @click.away="showLoginModal = false"
           class="fixed inset-0 z-50 overflow-y-auto" 
           style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <!-- Background overlay -->
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showLoginModal = false"></div>

          <!-- Modal panel -->
          <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-6 pt-6 pb-4">
              <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                  <i class="fas fa-lock text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                  Login Required
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                  Please sign in to contact this vendor and access full details
                </p>
              </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 space-y-3">
              <a href="{{ route('login') }}" class="block w-full">
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-medium transition">
                  <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </button>
              </a>
              
              <a href="{{ route('register') }}" class="block w-full">
                <button class="w-full bg-white hover:bg-gray-50 text-indigo-600 border-2 border-indigo-600 px-4 py-3 rounded-lg font-medium transition">
                  <i class="fas fa-user-plus mr-2"></i> Create Account
                </button>
              </a>

              <div class="text-center pt-2">
                <p class="text-sm text-gray-600">
                  Are you a vendor? 
                  <a href="{{ route('vendor.public.register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                    Register here
                  </a>
                </p>
              </div>

              <button @click="showLoginModal = false" class="w-full text-gray-600 hover:text-gray-800 px-4 py-2 text-sm font-medium">
                Maybe Later
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Report Vendor Button for unauthenticated users --}}
    <div class="bg-white p-4 rounded-2xl shadow" x-data="{ showLoginModal: false }">
      <button @click="showLoginModal = true" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium transition flex items-center justify-center">
        <i class="fas fa-flag mr-2"></i> Report Vendor
      </button>

      {{-- Login Modal for Report --}}
      <div x-show="showLoginModal" 
           x-cloak
           @click.away="showLoginModal = false"
           class="fixed inset-0 z-50 overflow-y-auto" 
           style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <!-- Background overlay -->
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showLoginModal = false"></div>

          <!-- Modal panel -->
          <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-6 pt-6 pb-4">
              <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                  <i class="fas fa-lock text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                  Login Required
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                  Please sign in to report this vendor
                </p>
              </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 space-y-3">
              <a href="{{ route('login') }}" class="block w-full">
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-medium transition">
                  <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </button>
              </a>
              
              <a href="{{ route('register') }}" class="block w-full">
                <button class="w-full bg-white hover:bg-gray-50 text-indigo-600 border-2 border-indigo-600 px-4 py-3 rounded-lg font-medium transition">
                  <i class="fas fa-user-plus mr-2"></i> Create Account
                </button>
              </a>

              <div class="text-center pt-2">
                <p class="text-sm text-gray-600">
                  Are you a vendor? 
                  <a href="{{ route('vendor.public.register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                    Register here
                  </a>
                </p>
              </div>

              <button @click="showLoginModal = false" class="w-full text-gray-600 hover:text-gray-800 px-4 py-2 text-sm font-medium">
                Maybe Later
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endauth

  {{-- Business Details Card --}}
  <div class="bg-white p-4 rounded-2xl shadow">
    <h4 class="font-semibold text-gray-900 mb-3">Business Details</h4>
    <div class="space-y-3 text-sm">
      @if($vendor->address)
        <div class="flex items-start">
          <i class="fas fa-map-marker-alt text-gray-400 mr-2 mt-0.5"></i>
          <div>
            <p class="text-xs text-gray-500">Location</p>
            <p class="text-gray-900">{{ $vendor->address }}</p>
          </div>
        </div>
      @endif

      <div class="flex items-start">
        <i class="fas fa-clock text-gray-400 mr-2 mt-0.5"></i>
        <div>
          <p class="text-xs text-gray-500">Member Since</p>
          <p class="text-gray-900">{{ $vendor->created_at->format('F Y') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function vendorChat(vendorId, authId) {
  return {
    open: false,
    message: '',
    sent: false,
    sending: false,
    async send() {
      if (!this.message.trim()) {
        alert('Please enter a message.');
        return;
      }
      this.sending = true;
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      try {
        const res = await fetch("{{ route('messages.store') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({
            vendor_id: vendorId,
            message: this.message
          })
        });

        const json = await res.json();
        if (json.ok) {
          this.sent = true;
          this.message = '';
          setTimeout(() => {
            this.sent = false;
            this.open = false;
          }, 3000);
        } else {
          alert(json.error || 'Could not send message.');
        }
      } catch (e) {
        console.error(e);
        alert('Could not send message. Try again later.');
      } finally {
        this.sending = false;
      }
    }
  }
}

function vendorReport(vendorId) {
  return {
    open: false,
    category: '',
    message: '',
    submitted: false,
    submitting: false,
    async submitReport() {
      if (!this.category || !this.message.trim()) {
        alert('Please select a category and provide details.');
        return;
      }
      this.submitting = true;
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      try {
        const res = await fetch("{{ route('reports.store') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({
            vendor_id: vendorId,
            category: this.category,
            message: this.message
          })
        });

        const json = await res.json();
        if (json.ok) {
          this.submitted = true;
          this.category = '';
          this.message = '';
          setTimeout(() => {
            this.submitted = false;
            this.open = false;
          }, 3000);
        } else {
          alert(json.message || 'Could not submit report.');
        }
      } catch (e) {
        console.error(e);
        alert('Could not submit report. Try again later.');
      } finally {
        this.submitting = false;
      }
    }
  }
}
</script>

