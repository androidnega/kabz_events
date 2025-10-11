<x-vendor-layout>
    <x-slot name="title">Vendor Profile</x-slot>
        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Vendor Profile</h2>
            <p class="text-gray-600 mt-1">Manage your business information and settings</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Profile Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Business Information -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Business Information</h3>
                        <a href="{{ route('vendor.profile.edit') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                            Edit
                        </a>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Business Name</label>
                            <p class="text-base text-gray-900 mt-1">{{ $vendor->business_name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Description</label>
                            <p class="text-base text-gray-700 mt-1">{{ $vendor->description ?? 'No description provided' }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Phone</label>
                                <p class="text-base text-gray-900 mt-1">{{ $vendor->phone }}</p>
                            </div>

                            @if($vendor->whatsapp)
                            <div>
                                <label class="text-sm font-medium text-gray-500">WhatsApp</label>
                                <p class="text-base text-gray-900 mt-1">{{ $vendor->whatsapp }}</p>
                            </div>
                            @endif
                        </div>

                        @if($vendor->website)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Website</label>
                            <p class="text-base text-gray-900 mt-1">
                                <a href="{{ $vendor->website }}" target="_blank" class="text-purple-600 hover:underline">
                                    {{ $vendor->website }}
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Location Information -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Location</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Address</label>
                            <p class="text-base text-gray-900 mt-1">{{ $vendor->address ?? 'Not specified' }}</p>
                        </div>

                        @if($vendor->region)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Region</label>
                                <p class="text-base text-gray-900 mt-1">{{ $vendor->region->name }}</p>
                            </div>

                            @if($vendor->district)
                            <div>
                                <label class="text-sm font-medium text-gray-500">District</label>
                                <p class="text-base text-gray-900 mt-1">{{ $vendor->district->name }}</p>
                            </div>
                            @endif

                            @if($vendor->town)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Town</label>
                                <p class="text-base text-gray-900 mt-1">{{ $vendor->town->name }}</p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Social Media Links -->
                @php
                    $socialLinks = is_string($vendor->social_links) ? json_decode($vendor->social_links, true) : $vendor->social_links;
                @endphp

                @if($socialLinks && (isset($socialLinks['facebook']) || isset($socialLinks['instagram'])))
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Social Media</h3>

                    <div class="space-y-3">
                        @if(isset($socialLinks['facebook']) && $socialLinks['facebook'])
                        <div class="flex items-center">
                            <i class="fab fa-facebook text-blue-600 text-xl w-8"></i>
                            <a href="{{ $socialLinks['facebook'] }}" target="_blank" class="text-purple-600 hover:underline ml-3">
                                Facebook Page
                            </a>
                        </div>
                        @endif

                        @if(isset($socialLinks['instagram']) && $socialLinks['instagram'])
                        <div class="flex items-center">
                            <i class="fab fa-instagram text-pink-600 text-xl w-8"></i>
                            <a href="{{ $socialLinks['instagram'] }}" target="_blank" class="text-purple-600 hover:underline ml-3">
                                Instagram Profile
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Profile Photo -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Photo</h3>
                    
                    <div class="flex flex-col items-center">
                        @if($vendor->profile_photo && !str_contains($vendor->profile_photo, 'picsum'))
                            <img src="{{ asset('storage/' . $vendor->profile_photo) }}" 
                                 alt="{{ $vendor->business_name }}" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-purple-100 mb-4">
                        @else
                            <div class="w-32 h-32 rounded-full bg-purple-100 flex items-center justify-center border-4 border-purple-200 mb-4">
                                <span class="text-4xl font-bold text-purple-600">
                                    {{ strtoupper(substr($vendor->business_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <p class="text-sm text-gray-600 text-center">{{ $vendor->business_name }}</p>
                        <a href="{{ route('vendor.profile.edit') }}" class="mt-2 text-xs text-purple-600 hover:text-purple-700 font-medium">
                            Upload Photo
                        </a>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Status</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Verification</span>
                            @if($vendor->is_verified)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Rating</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <i class="fas fa-star mr-1"></i>
                                {{ number_format($vendor->rating_cached, 1) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Services</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $vendor->services->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    
                    <div class="space-y-2">
                        <a href="{{ route('vendors.show', $vendor->slug) }}" 
                           class="flex items-center w-full p-3 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-eye w-5"></i>
                            <span class="ml-3">View Public Profile</span>
                        </a>

                        <a href="{{ route('vendor.services.index') }}" 
                           class="flex items-center w-full p-3 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-th-large w-5"></i>
                            <span class="ml-3">Manage Services</span>
                        </a>

                        @if(!$vendor->is_verified)
                        <a href="{{ route('vendor.verification') }}" 
                           class="flex items-center w-full p-3 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition">
                            <i class="fas fa-certificate w-5"></i>
                            <span class="ml-3">Get Verified</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</x-vendor-layout>

