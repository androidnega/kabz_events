<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-cog text-primary mr-2"></i> Configuration Center
            </h1>
            <p class="mt-2 text-gray-600">Manage all system integrations and settings from one place</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Paystack Configuration --}}
            <x-card class="p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-credit-card text-green-600 text-xl"></i>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full {{ ($paystack['paystack_enabled'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ($paystack['paystack_enabled'] ?? false) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Paystack</h3>
                <p class="text-sm text-gray-600 mb-4">Payment gateway for Ghana and Nigeria</p>
                <a href="{{ route('superadmin.settings.paystack') }}" class="inline-flex items-center text-primary hover:underline">
                    Configure <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </x-card>

            {{-- Cloudinary Configuration --}}
            <x-card class="p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-cloud text-blue-600 text-xl"></i>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full {{ ($storage['cloud_storage'] ?? 'local') === 'cloudinary' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ($storage['cloud_storage'] ?? 'local') === 'cloudinary' ? 'Active' : 'Local' }}
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Cloudinary</h3>
                <p class="text-sm text-gray-600 mb-4">Cloud storage for images and media</p>
                <a href="{{ route('superadmin.settings.cloudinary') }}" class="inline-flex items-center text-primary hover:underline">
                    Configure <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </x-card>

            {{-- Arkasel SMS Configuration --}}
            <x-card class="p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-sms text-orange-600 text-xl"></i>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full {{ ($sms['sms_enabled'] ?? false) ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ($sms['sms_enabled'] ?? false) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Arkasel SMS</h3>
                <p class="text-sm text-gray-600 mb-4">SMS notifications for Ghana</p>
                <a href="{{ route('superadmin.settings.sms') }}" class="inline-flex items-center text-primary hover:underline">
                    Configure <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </x-card>

            {{-- SMTP Configuration --}}
            <x-card class="p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-envelope text-indigo-600 text-xl"></i>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                        Email
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">SMTP Email</h3>
                <p class="text-sm text-gray-600 mb-4">Email server configuration</p>
                <a href="{{ route('superadmin.settings.smtp') }}" class="inline-flex items-center text-primary hover:underline">
                    Configure <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </x-card>

            {{-- System Configuration --}}
            <x-card class="p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-cog text-gray-600 text-xl"></i>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                        Required
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">System Settings</h3>
                <p class="text-sm text-gray-600 mb-4">Core application configuration</p>
                <a href="{{ route('superadmin.settings.system') }}" class="inline-flex items-center text-primary hover:underline">
                    Configure <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </x-card>
        </div>

        {{-- Quick Info --}}
        <x-card class="mt-8 p-6 bg-blue-50 border-l-4 border-blue-500">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Configuration Guide</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><i class="fas fa-check text-blue-600 mr-2"></i> All configurations are stored securely in the database</li>
                        <li><i class="fas fa-check text-blue-600 mr-2"></i> Changes take effect immediately after saving</li>
                        <li><i class="fas fa-check text-blue-600 mr-2"></i> Test each integration after configuration</li>
                        <li><i class="fas fa-check text-blue-600 mr-2"></i> API keys are encrypted and cached for performance</li>
                    </ul>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>

