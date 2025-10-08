<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="mb-6">
            <a href="{{ route('superadmin.settings.index') }}" class="text-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
        </div>

        <x-card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-credit-card text-green-600 mr-2"></i> Paystack Configuration
                </h2>
                <span class="px-3 py-1 text-sm rounded-full {{ ($settings['paystack_enabled'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ($settings['paystack_enabled'] ?? false) ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <ul class="list-disc list-inside text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.settings.paystack.update') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Public Key</label>
                    <input type="text" name="paystack_public_key" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           value="{{ $settings['paystack_public_key'] ?? '' }}"
                           placeholder="pk_test_xxxxxxxxxxxx">
                    <p class="mt-1 text-sm text-gray-500">Your Paystack public key (starts with pk_)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                    <input type="password" name="paystack_secret_key" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           value="{{ $settings['paystack_secret_key'] ?? '' }}"
                           placeholder="sk_test_xxxxxxxxxxxx">
                    <p class="mt-1 text-sm text-gray-500">Your Paystack secret key (starts with sk_)</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="paystack_enabled" id="paystack_enabled"
                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                           {{ ($settings['paystack_enabled'] ?? false) ? 'checked' : '' }}>
                    <label for="paystack_enabled" class="ml-2 text-sm font-medium text-gray-700">
                        Enable Paystack Payment Gateway
                    </label>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Getting your Paystack keys:</strong><br>
                                1. Visit <a href="https://paystack.com" target="_blank" class="underline">paystack.com</a><br>
                                2. Log in to your dashboard<br>
                                3. Go to Settings → API Keys & Webhooks<br>
                                4. Copy your Public and Secret keys
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-save mr-2"></i> Save Configuration
                    </button>
                    <a href="{{ route('superadmin.settings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>

