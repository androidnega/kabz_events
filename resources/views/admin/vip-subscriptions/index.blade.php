<x-admin-layout>
    <x-slot name="pageTitle">VIP Subscriptions Management</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <x-card class="p-6">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-gem text-purple-600 mr-3"></i>
                        VIP Subscriptions Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage vendor VIP subscriptions</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.vip-subscriptions.export') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </a>
                    <a href="{{ route('admin.vip-subscriptions.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                        <i class="fas fa-plus mr-2"></i>Create Subscription
                    </a>
                </div>
            </div>

            @if(session('success'))
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endif

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-blue-800">Total Subscriptions</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                    <div class="text-sm text-green-800">Active</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-gray-600">{{ $stats['expired'] }}</div>
                    <div class="text-sm text-gray-800">Expired</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</div>
                    <div class="text-sm text-red-800">Cancelled</div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form method="GET" action="{{ route('admin.vip-subscriptions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Vendor name..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                        <select name="plan_id" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">All Plans</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.vip-subscriptions.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ads Used</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($subscriptions as $sub)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $sub->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $sub->vendor->business_name }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-purple-600">{{ $sub->vipPlan->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->start_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->end_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->ads_used }} / {{ $sub->vipPlan->free_ads }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if($sub->status === 'active') bg-green-100 text-green-800
                                        @elseif($sub->status === 'expired') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('admin.vip-subscriptions.show', $sub->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    @if($sub->status === 'active')
                                        <form action="{{ route('admin.vip-subscriptions.cancel', $sub->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No VIP subscriptions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $subscriptions->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>

