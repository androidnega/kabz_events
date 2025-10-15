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

            {{-- Plans Grid - Compact Design with Tier Colors --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($plans as $plan)
                    @php
                        // Assign tier-specific solid colors (no gradients)
                        $tierColors = [
                            'VIP Bronze' => ['bg' => 'bg-amber-600', 'border' => 'border-amber-500'],
                            'VIP Silver' => ['bg' => 'bg-slate-600', 'border' => 'border-slate-500'],
                            'VIP Gold' => ['bg' => 'bg-yellow-600', 'border' => 'border-yellow-500'],
                            'VIP Platinum' => ['bg' => 'bg-purple-600', 'border' => 'border-purple-500'],
                        ];
                        
                        $colors = $tierColors[$plan->name] ?? ['bg' => 'bg-gray-600', 'border' => 'border-gray-500'];
                    @endphp
                    
                    <div class="bg-white border @if($plan->status) {{ $colors['border'] }} @else border-gray-300 @endif rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                        {{-- Header with solid color --}}
                        <div class="@if($plan->status) {{ $colors['bg'] }} @else bg-gray-400 @endif text-white p-4">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-lg font-bold">{{ str_replace('VIP ', '', $plan->name) }}</h3>
                                @if($plan->status)
                                    <span class="text-xs bg-white bg-opacity-20 px-2 py-0.5 rounded">Active</span>
                                @else
                                    <span class="text-xs bg-white bg-opacity-20 px-2 py-0.5 rounded">Inactive</span>
                                @endif
                            </div>
                            <div class="text-3xl font-bold">GH₵{{ number_format($plan->price, 0) }}</div>
                            <p class="text-xs opacity-90">{{ $plan->getDurationLabel() }}</p>
                        </div>

                        {{-- Content --}}
                        <div class="p-4">
                            <ul class="space-y-1.5 mb-3 text-xs">
                                <li class="flex items-center">
                                    <i class="fas fa-bullhorn w-4 text-amber-600"></i>
                                    <span class="ml-2"><strong>{{ $plan->free_ads }}</strong> Free Ads</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-images w-4 text-blue-600"></i>
                                    <span class="ml-2"><strong>{{ $plan->image_limit == -1 ? '∞' : $plan->image_limit }}</strong> Images</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-star w-4 text-yellow-600"></i>
                                    <span class="ml-2">Priority <strong>{{ $plan->priority_level }}</strong></span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-users w-4 text-green-600"></i>
                                    <span class="ml-2"><strong>{{ $plan->subscriptions_count }}</strong> Users</span>
                                </li>
                            </ul>

                            {{-- Action Buttons --}}
                            <div class="flex gap-2">
                                <a href="{{ route('admin.vip-plans.show', $plan->id) }}" 
                                   class="flex-1 text-center px-2 py-1.5 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.vip-plans.edit', $plan->id) }}" 
                                   class="flex-1 text-center px-2 py-1.5 bg-gray-500 text-white rounded text-xs hover:bg-gray-600 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.vip-plans.toggle-status', $plan->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-2 py-1.5 @if($plan->status) bg-red-500 hover:bg-red-600 @else bg-green-500 hover:bg-green-600 @endif text-white rounded text-xs transition">
                                        <i class="fas fa-{{ $plan->status ? 'times' : 'check' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-admin-layout>

        </x-card>
    </div>
</x-admin-layout>

