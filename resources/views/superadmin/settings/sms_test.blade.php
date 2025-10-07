<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <x-card class="p-6">
            <h2 class="text-2xl font-bold text-green-700 mb-6">📱 SMS Test (Arkassel Ghana) 🇬🇭</h2>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.sms.test.send') }}" class="space-y-6">
                @csrf

                {{-- Phone Number --}}
                <div>
                    <x-input-label for="phone" value="Phone Number *" class="mb-2" />
                    <x-text-input 
                        type="text" 
                        name="phone" 
                        id="phone"
                        placeholder="+233 24 XXX XXXX or 024 XXX XXXX"
                        class="w-full"
                        required
                    />
                    <p class="mt-1 text-sm text-gray-500">
                        Formats: +233XXXXXXXXX or 0XXXXXXXXX (MTN, Vodafone, AirtelTigo)
                    </p>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Message --}}
                <div>
                    <x-input-label for="message" value="Message *" class="mb-2" />
                    <textarea 
                        name="message" 
                        id="message"
                        rows="4"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        placeholder="Enter test message (max 160 characters)"
                        required
                    ></textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        Max 160 characters for standard SMS
                    </p>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Box --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ Arkassel Configuration</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Make sure Arkassel API keys are set in System Settings</li>
                        <li>• Sender ID: {{ \App\Services\SettingsService::get('sms_sender_id', 'KABZS') }}</li>
                        <li>• SMS Status: {{ \App\Services\SettingsService::get('sms_enabled') ? '✅ Enabled' : '❌ Disabled' }}</li>
                        <li>• Supports MTN, Vodafone, and AirtelTigo networks in Ghana</li>
                    </ul>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('superadmin.dashboard') }}" class="text-gray-600 hover:text-gray-800 underline">
                        Back to Dashboard
                    </a>
                    <x-button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold">
                        Send Test SMS 📱
                    </x-button>
                </div>
            </form>

            {{-- Recent Test Log (Optional) --}}
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Test Guidelines</h3>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li>✓ Use a real Ghana number for testing</li>
                    <li>✓ Check your Arkassel account balance</li>
                    <li>✓ Messages are sent in real-time (test mode)</li>
                    <li>✓ Each SMS costs credits from your Arkassel account</li>
                </ul>
            </div>
        </x-card>
    </div>
</x-app-layout>

