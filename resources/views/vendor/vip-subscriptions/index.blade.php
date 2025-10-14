<x-vendor-layout>
    <x-slot name="title">VIP Subscriptions</x-slot>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">VIP Subscriptions</h2>
        <p class="text-sm text-gray-600">Unlock VIP status and enjoy premium visibility, top search rankings, and exclusive benefits</p>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success" class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="danger" class="mb-6">
            {{ session('error') }}
        </x-alert>
    @endif

    @if(session('info'))
        <x-alert type="info" class="mb-6">
            {{ session('info') }}
        </x-alert>
    @endif

    {{-- Active Subscription Banner --}}
    @if($activeSubscription)
        <div class="mb-8 bg-gradient-to-r from-purple-600 to-pink-500 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">👑 Your Active VIP Subscription</h3>
                    <p class="text-lg">
                        <span class="font-semibold">{{ $activeSubscription->vipPlan->name }}</span>
                        • Expires on {{ $activeSubscription->end_date->format('d M Y') }}
                        ({{ $activeSubscription->end_date->diffForHumans() }})
                    </p>
                    <p class="text-sm mt-2">
                        Free Featured Ads: <strong>{{ $activeSubscription->getRemainingFreeAds() }}</strong> remaining
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-4xl mb-2">💎</div>
                    <form action="{{ route('vendor.vip-subscriptions.cancel', $activeSubscription->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel your VIP subscription?')">
                        @csrf
                        <button type="submit" class="text-sm text-white hover:text-gray-200 underline">
                            Cancel Subscription
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Available VIP Plans --}}
    <div class="mb-8">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Available VIP Plans</h3>
        
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-lg shadow-lg border-2 
                    @if($activeSubscription && $activeSubscription->vip_plan_id == $plan->id) 
                        border-purple-500 
                    @else 
                        border-gray-200 hover:border-purple-400 
                    @endif
                    transition-all duration-300">
                    
                    {{-- Plan Header --}}
                    <div class="bg-gradient-to-r from-purple-600 to-pink-500 text-white p-6 rounded-t-lg">
                        <h4 class="text-2xl font-bold mb-2">{{ $plan->name }}</h4>
                        <div class="text-4xl font-bold mb-1">GH₵{{ number_format($plan->price, 0) }}</div>
                        <p class="text-sm opacity-90">{{ $plan->getDurationLabel() }}</p>
                    </div>
                    
                    {{-- Plan Features --}}
                    <div class="p-6">
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start gap-2">
                                <span class="text-green-600 mt-1">✓</span>
                                <span class="text-sm text-gray-700">{{ $plan->free_ads }} free featured ads per cycle</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-600 mt-1">✓</span>
                                <span class="text-sm text-gray-700">Top listing priority in search</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-600 mt-1">✓</span>
                                <span class="text-sm text-gray-700">{{ $plan->image_limit }} image uploads in portfolio</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-600 mt-1">✓</span>
                                <span class="text-sm text-gray-700">VIP badge next to business name</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-600 mt-1">✓</span>
                                <span class="text-sm text-gray-700">Priority customer support</span>
                            </li>
                        </ul>

                        @if($plan->description)
                            <p class="text-xs text-gray-600 mb-4">{{ $plan->description }}</p>
                        @endif

                        @if($activeSubscription && $activeSubscription->vip_plan_id == $plan->id)
                            <button disabled class="w-full bg-gray-400 text-white px-4 py-3 rounded-lg font-semibold cursor-not-allowed">
                                Current Plan
                            </button>
                        @elseif($activeSubscription)
                            <button disabled class="w-full bg-gray-300 text-gray-600 px-4 py-3 rounded-lg font-semibold cursor-not-allowed">
                                Already Subscribed
                            </button>
                        @else
                            <a href="{{ route('vendor.vip-subscriptions.subscribe', $plan->id) }}" 
                               class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition">
                                Subscribe Now
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Subscription History --}}
    @if($subscriptions->isNotEmpty())
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Subscription History</h3>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">End Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($subscriptions as $sub)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $sub->vipPlan->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->start_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->end_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($sub->status === 'active') bg-green-100 text-green-800
                                        @elseif($sub->status === 'expired') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">GH₵{{ number_format($sub->vipPlan->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscriptions->links() }}
            </div>
        </div>
    @endif
</x-vendor-layout>

