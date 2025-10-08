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
                    <i class="fas fa-sms text-orange-600 mr-2"></i> Arkasel SMS Configuration
                </h2>
                <span class="px-3 py-1 text-sm rounded-full {{ ($settings['sms_enabled'] ?? false) ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ($settings['sms_enabled'] ?? false) ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.settings.sms.update') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <input type="text" name="sms_api_key" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['sms_api_key'] ?? '' }}"
                           placeholder="Your Arkasel API Key">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Secret</label>
                    <input type="password" name="sms_api_secret" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['sms_api_secret'] ?? '' }}"
                           placeholder="Your API Secret">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sender ID</label>
                    <input type="text" name="sms_sender_id" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['sms_sender_id'] ?? '' }}"
                           placeholder="KABZS EVENT"
                           maxlength="11">
                    <p class="mt-1 text-sm text-gray-500">Maximum 11 characters (approved by Arkasel)</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="sms_enabled" id="sms_enabled"
                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                           {{ ($settings['sms_enabled'] ?? false) ? 'checked' : '' }}>
                    <label for="sms_enabled" class="ml-2 text-sm font-medium text-gray-700">
                        Enable SMS Notifications
                    </label>
                </div>

                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
                    <p class="text-sm text-orange-700">
                        <strong>Ghana SMS Service:</strong><br>
                        Arkasel is a Ghanaian SMS gateway. Get your API credentials from their dashboard.
                    </p>
                </div>

                <div class="flex items-center space-x-4">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-save mr-2"></i> Save Configuration
                    </button>
                    <a href="{{ route('superadmin.sms.test') }}" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                        <i class="fas fa-paper-plane mr-2"></i> Test SMS
                    </a>
                    <a href="{{ route('superadmin.settings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>

