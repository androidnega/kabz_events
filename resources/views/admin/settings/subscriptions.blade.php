<x-admin-layout>
    <x-slot name="pageTitle">Subscription Settings</x-slot>

    <div class="p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Subscription Auto-Approval Settings</h2>
            <p class="text-gray-600">Configure automatic approval for vendor subscriptions and featured ads</p>
        </div>

        @if(session('success'))
            <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
        @endif

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <form action="{{ route('admin.settings.subscriptions.update') }}" method="POST">
                @csrf

                {{-- Subscription Auto-Approval --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor Subscriptions</h3>
                    
                    <div class="mb-4">
                        <label class="flex items-center space-x-3">
                            <input 
                                type="checkbox" 
                                name="subscription_auto_approval_enabled" 
                                value="1"
                                {{ AdminSetting::get('subscription_auto_approval_enabled', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            >
                            <span class="text-sm font-medium text-gray-700">
                                Enable automatic approval of subscription payments
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-7">
                            When enabled, subscriptions will be automatically approved if not reviewed within the specified time.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Auto-approval wait time (hours)
                        </label>
                        <input 
                            type="number" 
                            name="subscription_auto_approval_hours"
                            value="{{ AdminSetting::get('subscription_auto_approval_hours', 24) }}"
                            min="1"
                            max="168"
                            class="w-64 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            Subscriptions will be auto-approved after this many hours if not manually reviewed (1-168 hours).
                        </p>
                    </div>
                </div>

                {{-- Featured Ad Auto-Approval --}}
                <div class="mb-8 border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Featured Ads</h3>
                    
                    <div class="mb-4">
                        <label class="flex items-center space-x-3">
                            <input 
                                type="checkbox" 
                                name="featured_ad_auto_approval_enabled" 
                                value="1"
                                {{ AdminSetting::get('featured_ad_auto_approval_enabled', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            >
                            <span class="text-sm font-medium text-gray-700">
                                Enable automatic approval of featured ad payments
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-7">
                            When enabled, featured ads will be automatically approved if not reviewed within the specified time.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Auto-approval wait time (hours)
                        </label>
                        <input 
                            type="number" 
                            name="featured_ad_auto_approval_hours"
                            value="{{ AdminSetting::get('featured_ad_auto_approval_hours', 24) }}"
                            min="1"
                            max="168"
                            class="w-64 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            Featured ads will be auto-approved after this many hours if not manually reviewed (1-168 hours).
                        </p>
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ How Auto-Approval Works</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Vendors pay for subscriptions/ads via Paystack</li>
                        <li>• Payment is verified and marked as "Under Review"</li>
                        <li>• Admins receive notification to review</li>
                        <li>• If not reviewed within set hours, system auto-approves</li>
                        <li>• Vendor gets activated immediately upon approval</li>
                    </ul>
                </div>

                {{-- Save Button --}}
                <div class="flex items-center space-x-4">
                    <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-save mr-2"></i> Save Settings
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

