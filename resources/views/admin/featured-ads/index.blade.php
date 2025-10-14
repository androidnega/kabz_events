<x-admin-layout>
    <x-slot name="pageTitle">Featured Ads Management</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <x-card class="p-6">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-ad text-teal-600 mr-3"></i>
                        Featured Ads Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage vendor featured advertisements</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.featured-ads.export') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </a>
                    <a href="{{ route('admin.featured-ads.create') }}" class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                        <i class="fas fa-plus mr-2"></i>Create Ad
                    </a>
                </div>
            </div>

            @if(session('success'))
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endif

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-blue-800">Total Ads</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                    <div class="text-sm text-green-800">Active</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                    <div class="text-sm text-yellow-800">Pending</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['expired'] }}</div>
                    <div class="text-sm text-gray-800">Expired</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">GH₵{{ number_format($stats['revenue'], 2) }}</div>
                    <div class="text-sm text-purple-800">Revenue</div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form method="GET" action="{{ route('admin.featured-ads.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Vendor or service name..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Placement</label>
                        <select name="placement" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">All Placements</option>
                            <option value="homepage" {{ request('placement') == 'homepage' ? 'selected' : '' }}>Homepage</option>
                            <option value="category" {{ request('placement') == 'category' ? 'selected' : '' }}>Category</option>
                            <option value="search" {{ request('placement') == 'search' ? 'selected' : '' }}>Search</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700 text-sm">
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.featured-ads.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placement</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stats</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($featuredAds as $ad)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $ad->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $ad->vendor->business_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $ad->service->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($ad->placement) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $ad->start_date->format('M d') }} - {{ $ad->end_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">GH₵{{ number_format($ad->price, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if($ad->status === 'active') bg-green-100 text-green-800
                                        @elseif($ad->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($ad->status === 'expired') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($ad->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    👁️ {{ $ad->views }} | 🖱️ {{ $ad->clicks }}
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('admin.featured-ads.show', $ad->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    @if($ad->status === 'pending')
                                        <form action="{{ route('admin.featured-ads.approve', $ad->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800">Approve</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">No featured ads found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $featuredAds->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>

