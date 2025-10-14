<x-admin-layout>
    <x-slot name="pageTitle">Vendor Management</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <x-card class="p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-store-alt text-teal-600 mr-3"></i>
                    Vendor Account Management
                </h2>
                <p class="text-sm text-gray-600 mt-1">Manage all vendor accounts and their settings</p>
            </div>

            @if(session('success'))
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endif

            {{-- Filters & Search --}}
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form method="GET" action="{{ route('admin.vendors.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Search --}}
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Business name, owner name, email..."
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        {{-- Verification Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Verification Status</label>
                            <select name="status" id="status" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">All Vendors</option>
                                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                            </select>
                        </div>

                        {{-- Account Status --}}
                        <div>
                            <label for="active" class="block text-sm font-medium text-gray-700 mb-1">Account Status</label>
                            <select name="active" id="active" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit" 
                                class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-md hover:bg-teal-700 transition">
                            <i class="fas fa-filter mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.vendors.index') }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                        <div class="text-sm text-gray-600">
                            Showing {{ $vendors->total() }} vendor(s)
                        </div>
                    </div>
                </form>
            </div>

            {{-- Vendors Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Business</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Services</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($vendors as $vendor)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-mono font-bold text-gray-900">#{{ $vendor->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 font-bold mr-3">
                                            {{ substr($vendor->business_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $vendor->business_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $vendor->phone ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $vendor->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $vendor->user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900">{{ $vendor->services->count() }}</span>
                                    <span class="text-xs text-gray-500">services</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        @if($vendor->is_verified)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Verified
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-clock mr-1"></i>Unverified
                                            </span>
                                        @endif
                                        
                                        @if($vendor->user->email_verified_at)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ number_format($vendor->rating_cached ?? 0, 1) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $vendor->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $vendor->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.vendors.show', $vendor->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-teal-600 text-xs font-medium rounded-md text-teal-600 hover:bg-teal-50 transition"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(!$vendor->is_verified)
                                            <form action="{{ route('admin.vendors.verify', $vendor->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-green-600 text-xs font-medium rounded-md text-green-600 hover:bg-green-50 transition"
                                                        title="Verify Vendor">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('vendors.show', $vendor->id) }}" 
                                           target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-600 hover:bg-gray-50 transition"
                                           title="View Public Profile">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-5xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">No vendors found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your filters</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($vendors->hasPages())
                <div class="mt-6">
                    {{ $vendors->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-admin-layout>

