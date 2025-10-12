<x-client-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">Help & Support</h2>
        <p class="text-sm text-gray-600 mt-1">Get help or submit a support request</p>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Support Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Submit a Support Request</h3>
                
                <form action="{{ route('client.support.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <input 
                            type="text" 
                            id="subject" 
                            name="subject" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            placeholder="Brief description of your issue"
                        >
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select 
                            id="category" 
                            name="category" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                        >
                            <option value="">Select a category</option>
                            <option value="technical">Technical Issue</option>
                            <option value="billing">Billing & Payments</option>
                            <option value="general">General Inquiry</option>
                            <option value="complaint">Complaint</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select 
                            id="priority" 
                            name="priority" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                        >
                            <option value="low">Low - General question</option>
                            <option value="medium" selected>Medium - Issue affecting use</option>
                            <option value="high">High - Urgent issue</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="6" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            placeholder="Please provide as much detail as possible..."
                        ></textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition font-medium"
                    >
                        Submit Request
                    </button>
                </form>
            </div>
        </div>

        {{-- Quick Help --}}
        <div class="space-y-6">
            {{-- FAQ Card --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Help</h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            How do I bookmark vendors?
                        </h4>
                        <p class="text-sm text-gray-600 ml-7">Click the heart icon on any vendor profile to add them to your favorites.</p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            How to contact vendors?
                        </h4>
                        <p class="text-sm text-gray-600 ml-7">Use the message button on vendor profiles to start a conversation.</p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Can I edit my reviews?
                        </h4>
                        <p class="text-sm text-gray-600 ml-7">Yes, you can edit or delete your reviews from your dashboard.</p>
                    </div>
                </div>
            </div>

            {{-- Contact Info Card --}}
            <div class="bg-teal-50 border border-teal-200 rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Contact Us</h3>
                <div class="space-y-2 text-sm text-gray-700">
                    <p class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        support@kabzsevent.com
                    </p>
                    <p class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        +233 XX XXX XXXX
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>

