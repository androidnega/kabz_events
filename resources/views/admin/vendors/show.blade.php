<x-admin-layout>
    <x-slot name="pageTitle">Vendor Details</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('admin.vendors.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Vendor Management
            </a>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Vendor Info --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Vendor Details Card --}}
                <x-card class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">{{ $vendor->business_name }}</h2>
                            <p class="text-sm text-gray-500 mt-1">Vendor ID: #{{ $vendor->id }}</p>
                        </div>
                        
                        {{-- Verification Badge --}}
                        <div>
                            @if($vendor->is_verified)
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            @else
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-clock mr-1"></i>Unverified
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Business Info --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase mb-1">Business Phone</p>
                            <p class="font-semibold text-gray-900">{{ $vendor->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase mb-1">Rating</p>
                            <p class="font-semibold text-gray-900">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                {{ number_format($vendor->rating_cached ?? 0, 1) }}
                            </p>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($vendor->description)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3">Business Description</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-800 leading-relaxed">{{ $vendor->description }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Services --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3">Services ({{ $services->count() }})</h3>
                        @if($services->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($services as $service)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-900">{{ $service->name }}</span>
                                            @if($service->is_active)
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Active</span>
                                            @else
                                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Inactive</span>
                                            @endif
                                        </div>
                                        @if($service->price)
                                            <p class="text-sm text-gray-600 mt-1">GHS {{ number_format($service->price, 2) }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No services added yet</p>
                        @endif
                    </div>
                </x-card>

                {{-- Admin Actions Card --}}
                <x-card class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-tools mr-2"></i>
                        Admin Actions
                    </h3>

                    <div class="grid grid-cols-2 gap-3">
                        @if(!$vendor->is_verified)
                            <form action="{{ route('admin.vendors.verify', $vendor->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-check-circle mr-2"></i>Verify Vendor
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.vendors.unverify', $vendor->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                                    <i class="fas fa-times-circle mr-2"></i>Remove Verification
                                </button>
                            </form>
                        @endif

                        @if($vendor->user->email_verified_at)
                            <form action="{{ route('admin.vendors.deactivate', $vendor->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                    <i class="fas fa-ban mr-2"></i>Deactivate Account
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.vendors.activate', $vendor->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-check mr-2"></i>Activate Account
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.vendors.resetPassword', $vendor->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-key mr-2"></i>Reset Password
                            </button>
                        </form>

                        <a href="{{ route('vendors.show', $vendor->id) }}" target="_blank" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-center">
                            <i class="fas fa-external-link-alt mr-2"></i>View Public Profile
                        </a>
                    </div>
                </x-card>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                
                {{-- Owner Info --}}
                <x-card class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                        <i class="fas fa-user mr-2"></i>
                        Account Owner
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 font-bold text-lg mr-3">
                                {{ substr($vendor->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $vendor->user->name }}</p>
                                <p class="text-xs text-gray-500">User ID: {{ $vendor->user->id }}</p>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-200 space-y-2">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                {{ $vendor->user->email }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-phone mr-2 text-gray-400"></i>
                                {{ $vendor->user->phone ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                Joined {{ $vendor->created_at->format('M j, Y') }}
                            </p>
                        </div>
                    </div>
                </x-card>

                {{-- Stats --}}
                <x-card class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Statistics
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Services</span>
                            <span class="font-semibold text-gray-900">{{ $services->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Reviews</span>
                            <span class="font-semibold text-gray-900">{{ $reviews->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Average Rating</span>
                            <span class="font-semibold text-gray-900">{{ number_format($vendor->rating_cached ?? 0, 1) }}</span>
                        </div>
                    </div>
                </x-card>

                {{-- Account Status --}}
                <x-card class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Account Status
                    </h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Email Verified</span>
                            @if($vendor->user->email_verified_at)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Yes</span>
                            @else
                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">No</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Business Verified</span>
                            @if($vendor->is_verified)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Yes</span>
                            @else
                                <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">No</span>
                            @endif
                        </div>
                        @if($vendor->verified_at)
                            <div class="pt-2 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    Verified on {{ $vendor->verified_at->format('M j, Y') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-admin-layout>

