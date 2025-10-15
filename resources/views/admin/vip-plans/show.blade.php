<x-admin-layout>
    <x-slot name="pageTitle">VIP Plan Details</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <x-card class="p-6">
            <div class="mb-6">
                <a href="{{ route('admin.vip-plans.index') }}" class="text-purple-600 hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i> Back to VIP Plans
                </a>
            </div>

            <div class="mb-6 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-crown text-purple-600 mr-3"></i>
                        {{ $plan->name }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">VIP Plan Details & Subscriptions</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.vip-plans.edit', $plan->id) }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <span class="px-4 py-2 rounded {{ $plan->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $plan->status ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            @if(session('success'))
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endif

            {{-- Plan Details --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg p-6">
                    <div class="text-sm opacity-90 mb-1">Price</div>
                    <div class="text-3xl font-bold">GH₵{{ number_format($plan->price, 2) }}</div>
                    <div class="text-xs opacity-75 mt-1">{{ $plan->getDurationLabel() }}</div>
                </div>

                <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                    <div class="text-sm text-blue-800 mb-1">Duration</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $plan->duration_days }}</div>
                    <div class="text-xs text-blue-700 mt-1">Days</div>
                </div>

                <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                    <div class="text-sm text-green-800 mb-1">Image Limit</div>
                    <div class="text-3xl font-bold text-green-600">{{ $plan->image_limit }}</div>
                    <div class="text-xs text-green-700 mt-1">Max uploads</div>
                </div>

                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                    <div class="text-sm text-yellow-800 mb-1">Free Ads</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $plan->free_ads }}</div>
                    <div class="text-xs text-yellow-700 mt-1">Featured ads</div>
                </div>
            </div>

            {{-- Features Grid --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Plan Features</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-star text-purple-600 mt-1"></i>
                        <div>
                            <div class="font-medium text-gray-800">Priority Level</div>
                            <div class="text-sm text-gray-600">Level {{ $plan->priority_level }} (Higher = Better ranking)</div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-users text-purple-600 mt-1"></i>
                        <div>
                            <div class="font-medium text-gray-800">Total Subscriptions</div>
                            <div class="text-sm text-gray-600">{{ $plan->subscriptions_count }} vendor(s)</div>
                        </div>
                    </div>
                </div>

                @if($plan->description)
                <div class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <div class="font-medium text-purple-800 mb-2">Description</div>
                    <p class="text-sm text-purple-700">{{ $plan->description }}</p>
                </div>
                @endif
            </div>

            {{-- Active Subscriptions --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Active Subscriptions</h3>
                
                @if($subscriptions->isEmpty())
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">No subscriptions for this plan yet</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subscriptions as $subscription)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('vendors.show', $subscription->vendor->slug) }}" class="text-purple-600 hover:underline">
                                                {{ $subscription->vendor->business_name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                   ($subscription->status === 'expired' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subscription->start_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subscription->end_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.vip-subscriptions.show', $subscription->id) }}" class="text-blue-600 hover:underline">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $subscriptions->links() }}
                    </div>
                @endif
            </div>
        </x-card>
    </div>
</x-admin-layout>

