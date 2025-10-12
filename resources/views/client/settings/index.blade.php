<x-client-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">
            Account Settings
        </h2>
        <p class="text-sm text-gray-600 mt-1">Manage your account preferences and settings</p>
    </x-slot>

    <div class="space-y-6">
        {{-- Notification Settings --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Notification Preferences
            </h3>

            <form action="{{ route('client.settings.notifications') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-3">
                    <label class="flex items-start">
                        <input type="checkbox" name="email_notifications" class="mt-1 h-4 w-4 text-teal-600 rounded focus:ring-teal-500" checked>
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Email Notifications</span>
                            <p class="text-xs text-gray-500">Receive email updates about your activities</p>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="message_notifications" class="mt-1 h-4 w-4 text-teal-600 rounded focus:ring-teal-500" checked>
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Message Notifications</span>
                            <p class="text-xs text-gray-500">Get notified when vendors message you</p>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="review_notifications" class="mt-1 h-4 w-4 text-teal-600 rounded focus:ring-teal-500" checked>
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Review Status Updates</span>
                            <p class="text-xs text-gray-500">Notifications when your reviews are approved</p>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="marketing_notifications" class="mt-1 h-4 w-4 text-teal-600 rounded focus:ring-teal-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Marketing & Promotions</span>
                            <p class="text-xs text-gray-500">Receive news, offers, and vendor recommendations</p>
                        </div>
                    </label>
                </div>

                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition font-medium">
                    Save Notification Settings
                </button>
            </form>
        </div>

        {{-- Privacy Settings --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Privacy Settings
            </h3>

            <form action="{{ route('client.settings.privacy') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="profile_visibility" class="block text-sm font-medium text-gray-700 mb-2">Profile Visibility</label>
                    <select 
                        id="profile_visibility" 
                        name="profile_visibility" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                    >
                        <option value="public">Public - Anyone can see my reviews</option>
                        <option value="private">Private - Hide my profile from public</option>
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="flex items-start">
                        <input type="checkbox" name="show_email" class="mt-1 h-4 w-4 text-teal-600 rounded focus:ring-teal-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Show Email on Profile</span>
                            <p class="text-xs text-gray-500">Display your email address on your public profile</p>
                        </div>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="allow_vendor_contact" class="mt-1 h-4 w-4 text-teal-600 rounded focus:ring-teal-500" checked>
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-900">Allow Vendor Contact</span>
                            <p class="text-xs text-gray-500">Let vendors send you messages</p>
                        </div>
                    </label>
                </div>

                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition font-medium">
                    Save Privacy Settings
                </button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                Change Password
            </h3>

            <form action="{{ route('client.settings.password') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                    >
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                    >
                    <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters</p>
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="new_password_confirmation" 
                        name="new_password_confirmation" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                    >
                </div>

                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition font-medium">
                    Update Password
                </button>
            </form>
        </div>

        {{-- Account Actions --}}
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-400">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Danger Zone
            </h3>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-medium text-red-900 mb-2">Delete Account</h4>
                <p class="text-sm text-red-700 mb-4">
                    Once you delete your account, there is no going back. This will permanently delete your account, reviews, and all associated data.
                </p>
                <button 
                    type="button"
                    onclick="confirm('Are you sure you want to delete your account? This action cannot be undone.') && document.getElementById('delete-account-form').submit()"
                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition font-medium"
                >
                    Delete My Account
                </button>
            </div>

            <form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>

        {{-- Account Info --}}
        <div class="bg-teal-50 border border-teal-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Account Information</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-medium text-gray-900">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium text-gray-900">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">User ID:</span>
                    <span class="font-medium text-gray-900">{{ $user->display_id ?? $user->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Member Since:</span>
                    <span class="font-medium text-gray-900">{{ $user->created_at->format('F Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>

