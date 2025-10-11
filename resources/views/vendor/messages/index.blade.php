<x-vendor-layout>
  <x-slot name="title">Messages</x-slot>

  <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
      <i class="fas fa-comments text-purple-600 mr-3"></i> Messages
    </h2>
    <p class="text-gray-600 mt-1">Communicate with your clients</p>
  </div>

  <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">

    @if($vendor && $conversations->count() > 0)
      <div class="grid md:grid-cols-4 gap-6" x-data="vendorMessages({{ $vendor->id }})">
        {{-- Conversations List --}}
        <div class="md:col-span-1 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <h3 class="font-semibold mb-3 text-gray-800">Conversations</h3>
          @foreach($conversations as $conv)
            <a href="#" @click.prevent="openConversation({{ $conv['counterparty']->id }})" 
               class="block py-3 border-b border-gray-100 hover:bg-gray-50 rounded transition">
              <div class="flex items-center gap-3">
                <img src="{{ $conv['counterparty']->profile_photo_url ?? asset('images/user-placeholder.png') }}" 
                     alt="{{ $conv['counterparty']->name }}" 
                     class="w-10 h-10 rounded-full object-cover border border-gray-200">
                <div class="flex-1 min-w-0">
                  <div class="font-medium text-gray-900 truncate">{{ $conv['counterparty']->name }}</div>
                  <div class="text-xs text-gray-500 truncate">{{ \Str::limit($conv['last_message']->message ?? '', 40) }}</div>
                </div>
                @if($conv['unread'])
                  <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">{{ $conv['unread'] }}</span>
                @endif
              </div>
            </a>
          @endforeach
        </div>

        {{-- Messages Display --}}
        <div class="md:col-span-3 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="h-96 overflow-y-auto mb-4 border border-gray-200 rounded-lg p-4 bg-gray-50" id="messagesContainer">
            <template x-if="messages.length === 0">
              <div class="text-center text-gray-500 py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p>Select a conversation to view messages</p>
              </div>
            </template>
            <template x-for="m in messages" :key="m.id">
              <div class="mb-4">
                <div :class="{'text-right': m.sender_id == {{ auth()->id() }}}">
                  <div class="inline-block max-w-xs md:max-w-md lg:max-w-lg p-3 rounded-lg"
                       :class="m.sender_id == {{ auth()->id() }} ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-900'">
                    <p x-text="m.message" class="whitespace-pre-wrap"></p>
                  </div>
                  <div class="text-xs text-gray-400 mt-1" x-text="m.sender.name + ' • ' + new Date(m.created_at).toLocaleString()"></div>
                </div>
              </div>
            </template>
          </div>

          <div class="mt-4">
            <textarea x-model="newMessage" rows="3" 
                      class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                      placeholder="Write your reply..."></textarea>
            <div class="flex justify-end mt-2">
              <button @click="send()" :disabled="!currentCounterpartyId || !newMessage.trim()"
                      class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 text-white px-6 py-2 rounded-lg font-medium transition flex items-center">
                <i class="fas fa-paper-plane mr-2"></i> Send
              </button>
            </div>
          </div>
        </div>
      </div>

      <script>
        function vendorMessages(vendorId) {
          return {
            messages: [],
            newMessage: '',
            currentCounterpartyId: null,
            vendorId: vendorId,
            async openConversation(counterpartyId) {
              this.currentCounterpartyId = counterpartyId;
              await this.loadMessages(counterpartyId);
            },
            async loadMessages(counterpartyId) {
              const url = "{{ route('messages.conversation') }}?vendor_id=" + this.vendorId + "&user_id=" + counterpartyId;
              const res = await fetch(url, {
                headers: {
                  'X-Requested-With':'XMLHttpRequest', 
                  'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                }
              });
              const json = await res.json();
              this.messages = json.messages;
              setTimeout(() => {
                const container = document.getElementById('messagesContainer');
                if (container) container.scrollTop = container.scrollHeight;
              }, 100);
            },
            async send() {
              if (!this.newMessage.trim() || !this.currentCounterpartyId) return;
              const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              const res = await fetch("{{ route('messages.store') }}", {
                method: "POST",
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token},
                body: JSON.stringify({ vendor_id: this.vendorId, message: this.newMessage })
              });
              const json = await res.json();
              if (json.ok) {
                this.messages.push(json.message);
                this.newMessage = '';
                setTimeout(() => {
                  const container = document.getElementById('messagesContainer');
                  if (container) container.scrollTop = container.scrollHeight;
                }, 100);
              } else {
                alert('Could not send message');
              }
            }
          }
        }
      </script>
    @else
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
        <p class="text-gray-500 text-lg">No conversations yet.</p>
        <p class="text-gray-400 mt-2">When clients send you messages, they will appear here.</p>
      </div>
    @endif
  </div>
</x-vendor-layout>

