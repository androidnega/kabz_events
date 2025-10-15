<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-tools text-red-600 mr-2"></i> Site Mode Configuration
                    </h1>
                    <p class="mt-2 text-gray-600">Manage maintenance, coming soon, and update modes</p>
                </div>
                <a href="{{ route('superadmin.settings.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Settings
                </a>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Configuration Form --}}
        <x-card class="p-6">
            <form method="POST" action="{{ route('superadmin.settings.maintenance.update') }}">
                @csrf

                {{-- Site Mode Toggle --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="site_mode_enabled" value="1" 
                               {{ ($settings['site_mode_enabled'] ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="ml-3 text-lg font-semibold text-gray-900">
                            Enable Site Mode
                        </span>
                    </label>
                    <p class="mt-2 text-sm text-gray-600 ml-8">
                        When enabled, site will display the selected mode page to all users except super admins
                    </p>
                </div>

                {{-- Mode Selection --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Mode</label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Live Mode --}}
                        <label class="relative flex cursor-pointer border-2 rounded-lg p-4 hover:border-primary transition {{ ($settings['site_mode'] ?? 'live') === 'live' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                            <input type="radio" name="site_mode" value="live" 
                                   {{ ($settings['site_mode'] ?? 'live') === 'live' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Live Mode</h4>
                                    <p class="text-sm text-gray-600">Site is fully operational</p>
                                </div>
                            </div>
                        </label>

                        {{-- Maintenance Mode --}}
                        <label class="relative flex cursor-pointer border-2 rounded-lg p-4 hover:border-primary transition {{ ($settings['site_mode'] ?? 'live') === 'maintenance' ? 'border-purple-500 bg-purple-50' : 'border-gray-300' }}">
                            <input type="radio" name="site_mode" value="maintenance" 
                                   {{ ($settings['site_mode'] ?? 'live') === 'maintenance' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-wrench text-purple-600 text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Maintenance Mode</h4>
                                    <p class="text-sm text-gray-600">Site under maintenance</p>
                                </div>
                            </div>
                        </label>

                        {{-- Coming Soon Mode --}}
                        <label class="relative flex cursor-pointer border-2 rounded-lg p-4 hover:border-primary transition {{ ($settings['site_mode'] ?? 'live') === 'coming_soon' ? 'border-pink-500 bg-pink-50' : 'border-gray-300' }}">
                            <input type="radio" name="site_mode" value="coming_soon" 
                                   {{ ($settings['site_mode'] ?? 'live') === 'coming_soon' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-rocket text-pink-600 text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Coming Soon</h4>
                                    <p class="text-sm text-gray-600">Site launching soon</p>
                                </div>
                            </div>
                        </label>

                        {{-- Update Mode --}}
                        <label class="relative flex cursor-pointer border-2 rounded-lg p-4 hover:border-primary transition {{ ($settings['site_mode'] ?? 'live') === 'update' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                            <input type="radio" name="site_mode" value="update" 
                                   {{ ($settings['site_mode'] ?? 'live') === 'update' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-arrow-circle-up text-blue-600 text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Update Mode</h4>
                                    <p class="text-sm text-gray-600">System updating</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Custom Messages --}}
                <div class="space-y-6 mb-6">
                    {{-- Maintenance Message --}}
                    <div>
                        <label for="maintenance_message" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-wrench text-purple-600 mr-2"></i> Maintenance Message
                        </label>
                        <textarea name="maintenance_message" id="maintenance_message" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                        >{{ $settings['maintenance_message'] ?? 'We are currently performing scheduled maintenance. We\'ll be back shortly!' }}</textarea>
                    </div>

                    {{-- Coming Soon Message --}}
                    <div>
                        <label for="coming_soon_message" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-rocket text-pink-600 mr-2"></i> Coming Soon Message
                        </label>
                        <textarea name="coming_soon_message" id="coming_soon_message" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                        >{{ $settings['coming_soon_message'] ?? 'Something amazing is coming soon! Stay tuned.' }}</textarea>
                    </div>

                    {{-- Update Message --}}
                    <div>
                        <label for="update_message" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-arrow-circle-up text-blue-600 mr-2"></i> Update Message
                        </label>
                        <textarea name="update_message" id="update_message" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                        >{{ $settings['update_message'] ?? 'We are currently updating our system with exciting new features. Please check back soon!' }}</textarea>
                    </div>
                </div>

                {{-- Expected End Time --}}
                <div class="mb-6">
                    <label for="maintenance_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock text-gray-600 mr-2"></i> Expected End Time (Optional)
                    </label>
                    <input type="datetime-local" name="maintenance_end_time" id="maintenance_end_time" 
                           value="{{ $settings['maintenance_end_time'] ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                    <p class="mt-1 text-sm text-gray-500">Display countdown or expected completion time to users</p>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition font-medium">
                        <i class="fas fa-save mr-2"></i> Save Configuration
                    </button>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('site.maintenance') }}" target="_blank" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                            <i class="fas fa-eye mr-2"></i> Preview Maintenance
                        </a>
                        <a href="{{ route('site.coming-soon') }}" target="_blank" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                            <i class="fas fa-eye mr-2"></i> Preview Coming Soon
                        </a>
                        <a href="{{ route('site.update') }}" target="_blank" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                            <i class="fas fa-eye mr-2"></i> Preview Update
                        </a>
                    </div>
                </div>
            </form>
        </x-card>

        {{-- Warning Notice --}}
        <x-card class="mt-6 p-6 bg-yellow-50 border-l-4 border-yellow-500">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">Important Notice</h3>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li><i class="fas fa-shield-alt text-yellow-600 mr-2"></i> Super admins can always access the site</li>
                        <li><i class="fas fa-users-slash text-yellow-600 mr-2"></i> All other users will see the selected mode page</li>
                        <li><i class="fas fa-eye text-yellow-600 mr-2"></i> Use preview links to test before enabling</li>
                        <li><i class="fas fa-clock text-yellow-600 mr-2"></i> Remember to disable mode when maintenance is complete</li>
                    </ul>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>

