<x-vendor-layout>
    <x-slot name="title">Edit Profile</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Edit Business Profile</h2>
        <p class="text-gray-600 mt-1">Update your business information</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Profile Photo Upload -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Photo</h3>
            
            <div class="flex items-center space-x-6">
                <!-- Current Photo -->
                <div>
                    @if($vendor->profile_photo && !str_contains($vendor->profile_photo, 'picsum'))
                        <img src="{{ asset('storage/' . $vendor->profile_photo) }}" 
                             alt="Profile" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-purple-100">
                    @else
                        <div class="w-24 h-24 rounded-full bg-purple-100 flex items-center justify-center border-4 border-purple-200">
                            <span class="text-3xl font-bold text-purple-600">
                                {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Upload Input -->
                <div class="flex-1">
                    <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload New Photo
                    </label>
                    <input type="file" 
                           id="profile_photo" 
                           name="profile_photo" 
                           accept="image/jpeg,image/jpg,image/png"
                           class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="mt-1 text-xs text-gray-500">JPG, JPEG or PNG. Max 2MB</p>
                    @error('profile_photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Business Information Card -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Business Name -->
                <div class="lg:col-span-2">
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Business Name *
                    </label>
                    <input type="text" 
                           id="business_name" 
                           name="business_name" 
                           value="{{ old('business_name', $vendor->business_name) }}"
                           required
                           class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg">
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Name Change Warning -->
                    @if($vendor->is_verified)
                        <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-sm text-red-700 font-medium flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Warning: You are verified. Changing your business name will require re-verification.
                            </p>
                        </div>
                    @else
                        <div class="mt-3 bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <p class="text-sm text-amber-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                You can change your business name <strong>{{ $vendor->remainingBusinessNameChanges() }} more time(s)</strong> this year.
                                @if($vendor->business_name_changes_count > 0)
                                    ({{ $vendor->business_name_changes_count }}/3 used)
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg">{{ old('description', $vendor->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number *
                    </label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $vendor->phone) }}"
                           required
                           class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp -->
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                        WhatsApp Number
                        @if(!$vendor->canShowWhatsApp())
                            <span class="text-xs text-amber-600">(Hidden until verified or subscribed)</span>
                        @endif
                    </label>
                    <input type="text" 
                           id="whatsapp" 
                           name="whatsapp" 
                           value="{{ old('whatsapp', $vendor->whatsapp) }}"
                           class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg">
                    @error('whatsapp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(!$vendor->canShowWhatsApp())
                        <div class="mt-2 bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <p class="text-xs text-amber-700">
                                <i class="fas fa-lock mr-1"></i>
                                Your WhatsApp will only be visible to customers after you get <strong>verified</strong> or purchase a <strong>subscription</strong>.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Website -->
                <div class="lg:col-span-2">
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                        Website
                    </label>
                    <input type="url" 
                           id="website" 
                           name="website" 
                           value="{{ old('website', $vendor->website) }}"
                           placeholder="https://example.com"
                           class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="lg:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address
                    </label>
                    <textarea id="address" 
                              name="address" 
                              rows="2"
                              class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg">{{ old('address', $vendor->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('vendor.profile') }}" 
               class="text-gray-600 hover:text-gray-800 font-medium">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                Save Changes
            </button>
        </div>
    </form>
</x-vendor-layout>

