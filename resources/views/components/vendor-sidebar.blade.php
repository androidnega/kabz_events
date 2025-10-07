@props(['vendor'])

<div class="bg-white p-4 rounded-2xl shadow space-y-4">
  <div class="flex items-center gap-3">
    <img src="{{ $vendor->profile_image ?? ($vendor->logo ?? asset('images/default_vendor.png')) }}"
         alt="{{ $vendor->business_name }}"
         class="w-16 h-16 rounded-full object-cover border" />
    <div>
      <h3 class="font-semibold text-lg">{{ $vendor->business_name }}</h3>
      <p class="text-sm text-gray-500">{{ $vendor->services->first()?->category->name ?? 'Vendor' }}</p>
      @if($vendor->is_verified)
        <span class="inline-block mt-1 text-green-700 text-sm font-medium">
          <i class="fas fa-check-circle"></i> Verified
        </span>
      @endif
      <div class="text-xs text-gray-400 mt-1">
        <i class="fas fa-map-marker-alt"></i> {{ $vendor->town->name ?? $vendor->address }}
      </div>
    </div>
  </div>

  {{-- Contact block (only for authenticated users who are clients) --}}
  @auth
    @if(auth()->user()->hasRole('client'))
      <div class="text-sm space-y-2 p-3 bg-gray-50 rounded-lg">
        <div>
          <strong class="text-gray-700"><i class="fas fa-phone text-indigo-600 mr-1"></i> Phone:</strong>
          <a href="tel:{{ $vendor->phone }}" class="text-indigo-600 hover:text-indigo-800">{{ $vendor->phone }}</a>
        </div>
        <div>
          <strong class="text-gray-700"><i class="fas fa-envelope text-indigo-600 mr-1"></i> Email:</strong>
          <a href="mailto:{{ $vendor->user->email ?? '' }}" class="text-indigo-600 hover:text-indigo-800">{{ $vendor->user->email ?? '—' }}</a>
        </div>
      </div>
    @endif
  @else
    <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
      <p class="text-sm text-gray-700">
        <i class="fas fa-lock text-yellow-600 mr-1"></i>
        Please <a href="{{ route('login') }}" class="text-indigo-600 underline font-medium">log in</a> to view contact details and message the vendor.
      </p>
    </div>
  @endauth

  {{-- Send message button + inline chat area (Alpine.js) --}}
  @auth
    @if(auth()->user()->hasRole('client'))
      <div x-data="vendorChat({{ $vendor->id }}, {{ auth()->id() }})" class="space-y-2">
        <button x-show="!open" @click="open = true" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center justify-center">
          <i class="fas fa-comment-dots mr-2"></i> Send Message
        </button>

        <div x-show="open" x-cloak class="space-y-2">
          <textarea x-model="message" placeholder="Write a short message to the vendor..." rows="4"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>

          <div class="flex justify-between items-center">
            <small class="text-xs text-gray-500">
              <i class="fas fa-lock text-gray-400 mr-1"></i> Your message will be sent privately.
            </small>
            <div class="space-x-2">
              <button @click="send" :disabled="sending" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-paper-plane mr-1"></i> Send
              </button>
              <button @click="open = false" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Cancel</button>
            </div>
          </div>

          <template x-if="sent">
            <div class="text-sm text-green-700 bg-green-50 p-3 rounded-lg border border-green-200">
              <i class="fas fa-check-circle mr-1"></i> Message sent. Vendor will respond on their dashboard.
            </div>
          </template>
        </div>
      </div>
    @endif
  @endauth
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
</script>

