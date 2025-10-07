<x-admin-layout>
    <x-slot name="pageTitle">User Details</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Page Header --}}
        <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user text-indigo-600 mr-3"></i>
                        User Profile
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">View detailed user information</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- User Info Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 mx-auto rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                            <span class="text-4xl font-bold text-indigo-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        
                        {{-- Role Badge --}}
                        @if($user->roles->isNotEmpty())
                            @php
                                $role = $user->roles->first();
                                $roleColors = [
                                    'super_admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'admin' => 'bg-teal-100 text-teal-800 border-teal-200',
                                    'vendor' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'client' => 'bg-blue-100 text-blue-800 border-blue-200',
                                ];
                                $colorClass = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <div class="mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $colorClass }}">
                                    @if($role->name === 'super_admin')
                                        <i class="fas fa-shield-alt mr-1"></i>
                                    @elseif($role->name === 'admin')
                                        <i class="fas fa-user-shield mr-1"></i>
                                    @elseif($role->name === 'vendor')
                                        <i class="fas fa-store mr-1"></i>
                                    @else
                                        <i class="fas fa-user mr-1"></i>
                                    @endif
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4 border-t border-gray-200 pt-4">
                        {{-- Email Verification Status --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Email Status:</span>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-clock mr-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        {{-- Account Created --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Member Since:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>

                        {{-- Last Updated --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Last Updated:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity & Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Vendor Information (if vendor) --}}
                @if($user->hasRole('vendor') && $user->vendor)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-store text-amber-600 mr-2"></i>
                            Vendor Information
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Business Name</p>
                                <p class="font-medium text-gray-900">{{ $user->vendor->business_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Verification Status</p>
                                @if($user->vendor->is_verified)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Unverified
                                    </span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Phone</p>
                                <p class="font-medium text-gray-900">{{ $user->vendor->phone ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Rating</p>
                                <p class="font-medium text-gray-900">
                                    <i class="fas fa-star text-yellow-500"></i>
                                    {{ number_format($user->vendor->rating_cached, 1) }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('vendors.show', $user->vendor->slug) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View Vendor Profile <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Recent Reviews --}}
                @if($user->reviews->isNotEmpty())
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-star text-yellow-600 mr-2"></i>
                            Recent Reviews ({{ $user->reviews->count() }})
                        </h3>
                        <div class="space-y-4">
                            @foreach($user->reviews->take(5) as $review)
                                <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ Str::limit($review->comment, 100) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Account Actions --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-cog text-gray-600 mr-2"></i>
                        Account Actions
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                            <i class="fas fa-edit mr-2"></i> Edit User
                        </a>
                        
                        @if($user->id !== auth()->id() && !$user->hasRole('super_admin'))
                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="block w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition">
                                    <i class="fas fa-trash mr-2"></i> Delete User
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center text-sm text-gray-600">
                                <i class="fas fa-shield-alt mr-1"></i>
                                This account cannot be deleted
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

