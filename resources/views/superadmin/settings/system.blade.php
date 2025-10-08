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
                    <i class="fas fa-cog text-gray-600 mr-2"></i> System Configuration
                </h2>
                <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">
                    Core Settings
                </span>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.settings.system.update') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site Name <span class="text-red-500">*</span></label>
                    <input type="text" name="site_name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['site_name'] ?? 'KABZS EVENT' }}"
                           required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Email <span class="text-red-500">*</span></label>
                        <input type="email" name="site_email" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                               value="{{ $settings['site_email'] ?? '' }}"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Phone</label>
                        <input type="text" name="site_phone" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                               value="{{ $settings['site_phone'] ?? '' }}"
                               placeholder="+233 XX XXX XXXX">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Default Currency <span class="text-red-500">*</span></label>
                        <select name="default_currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                            <option value="GHS" {{ ($settings['default_currency'] ?? 'GHS') === 'GHS' ? 'selected' : '' }}>GHS - Ghana Cedi</option>
                            <option value="USD" {{ ($settings['default_currency'] ?? 'GHS') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ ($settings['default_currency'] ?? 'GHS') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ ($settings['default_currency'] ?? 'GHS') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency Symbol <span class="text-red-500">*</span></label>
                        <input type="text" name="currency_symbol" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                               value="{{ $settings['currency_symbol'] ?? 'GH₵' }}"
                               maxlength="5"
                               required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Timezone <span class="text-red-500">*</span></label>
                    <select name="timezone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary" required>
                        <option value="Africa/Accra" {{ ($settings['timezone'] ?? 'Africa/Accra') === 'Africa/Accra' ? 'selected' : '' }}>Africa/Accra (GMT)</option>
                        <option value="Africa/Lagos" {{ ($settings['timezone'] ?? 'Africa/Accra') === 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos (WAT)</option>
                        <option value="UTC" {{ ($settings['timezone'] ?? 'Africa/Accra') === 'UTC' ? 'selected' : '' }}>UTC</option>
                    </select>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Note:</strong> Changes to core system settings will affect the entire application.
                    </p>
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

