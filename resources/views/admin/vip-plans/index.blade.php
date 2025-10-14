<x-admin-layout>
    <x-slot name="pageTitle">VIP Plans Management</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <x-card class="p-6">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-crown text-purple-600 mr-3"></i>
                        VIP Plans Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Create and manage VIP subscription plans</p>
                </div>
                <a href="{{ route('admin.vip-plans.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    <i class="fas fa-plus mr-2"></i>Create Plan
                </a>
            </div>

            @if(session('success'))
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endif

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_plans'] }}</div>
                    <div class="text-sm text-blue-800">Total Plans</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['active_plans'] }}</div>
                    <div class="text-sm text-green-800">Active Plans</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['total_subscriptions'] }}</div>
                    <div class="text-sm text-purple-800">Total Subscriptions</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['active_subscriptions'] }}</div>
                    <div class="text-sm text-yellow-800">Active Subscriptions</div>
                </div>
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-indigo-600">GH₵{{ number_format($stats['revenue'], 2) }}</div>
                    <div class="text-sm text-indigo-800">Total Revenue</div>
                </div>
            </div>

            {{-- Plans Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <div class="bg-white border-2 @if($plan->status) border-purple-500 @else border-gray-300 @endif rounded-lg shadow-md overflow-hidden">
                        <div class="@if($plan->status) bg-gradient-to-r from-purple-600 to-pink-500 @else bg-gray-400 @endif text-white p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-2xl font-bold">{{ $plan->name }}</h3>
                                <form action="{{ route('admin.vip-plans.toggle-status', $plan->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-white text-sm hover:underline">
                                        {{ $plan->status ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                            <div class="text-4xl font-bold">GH₵{{ number_format($plan->price, 0) }}</div>
                            <p class="text-sm opacity-90">{{ $plan->getDurationLabel() }} ({{ $plan->duration_days }} days)</p>
                        </div>

                        <div class="p-6">
                            <ul class="space-y-2 mb-4 text-sm">
                                <li>📦 <strong>Free Ads:</strong> {{ $plan->free_ads }}</li>
                                <li>🖼️ <strong>Image Limit:</strong> {{ $plan->image_limit }}</li>
                                <li>⭐ <strong>Priority:</strong> Level {{ $plan->priority_level }}</li>
                                <li>📊 <strong>Subscriptions:</strong> {{ $plan->subscriptions_count }}</li>
                            </ul>

                            @if($plan->description)
                                <p class="text-xs text-gray-600 mb-4">{{ $plan->description }}</p>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('admin.vip-plans.show', $plan->id) }}" class="flex-1 text-center px-3 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                                    View
                                </a>
                                <a href="{{ route('admin.vip-plans.edit', $plan->id) }}" class="flex-1 text-center px-3 py-2 bg-gray-500 text-white rounded text-sm hover:bg-gray-600">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-admin-layout>

