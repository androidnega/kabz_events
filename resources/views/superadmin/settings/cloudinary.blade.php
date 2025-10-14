<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="mb-6">
            <a href="{{ route('superadmin.settings.index') }}" class="text-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Configuration
            </a>
        </div>

        <x-card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-cloud text-blue-600 mr-2"></i> Cloudinary Configuration
                </h2>
                <span class="px-3 py-1 text-sm rounded-full {{ ($settings['cloud_storage'] ?? 'local') === 'cloudinary' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ($settings['cloud_storage'] ?? 'local') === 'cloudinary' ? 'Active' : 'Local Storage' }}
                </span>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.settings.cloudinary.update') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Storage Provider</label>
                    <select name="cloud_storage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="local" {{ ($settings['cloud_storage'] ?? 'local') === 'local' ? 'selected' : '' }}>Local Storage</option>
                        <option value="cloudinary" {{ ($settings['cloud_storage'] ?? 'local') === 'cloudinary' ? 'selected' : '' }}>Cloudinary</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cloud Name</label>
                    <input type="text" name="cloudinary_cloud_name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['cloudinary_cloud_name'] ?? '' }}"
                           placeholder="your-cloud-name">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <input type="text" name="cloudinary_api_key" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['cloudinary_api_key'] ?? '' }}"
                           placeholder="123456789012345">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Secret</label>
                    <input type="password" name="cloudinary_api_secret" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                           value="{{ $settings['cloudinary_api_secret'] ?? '' }}"
                           placeholder="Your API Secret">
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-sm text-blue-700">
                        <strong>Getting your Cloudinary credentials:</strong><br>
                        1. Visit <a href="https://cloudinary.com" target="_blank" class="underline">cloudinary.com</a><br>
                        2. Log in to your dashboard<br>
                        3. Find your Cloud Name, API Key, and API Secret on the dashboard homepage
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

