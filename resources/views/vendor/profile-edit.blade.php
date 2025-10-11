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

    <form action="{{ route('vendor.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

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
                    </label>
                    <input type="text" 
                           id="whatsapp" 
                           name="whatsapp" 
                           value="{{ old('whatsapp', $vendor->whatsapp) }}"
                           class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg">
                    @error('whatsapp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

