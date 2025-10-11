<x-app-layout>
    <div class="max-w-6xl mx-auto mt-8 px-4">
        {{-- Page Header --}}
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Choose a Subscription Plan</h2>
            <p class="text-lg text-gray-600">Select the plan that best fits your business needs</p>
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

        {{-- Current Subscription Banner --}}
        @if($active)
            <div class="mb-8 bg-gradient-to-r from-purple-600 to-teal-500 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">Your Current Plan</h3>
                        <p class="text-lg">
                            <span class="font-semibold">{{ $active->plan }} Plan</span>
                            @if($active->ends_at)
                                • Expires on {{ $active->ends_at->format('d M Y') }}
                                ({{ $active->ends_at->diffForHumans() }})
                            @else
                                • Lifetime Access
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">
                            @if($active->plan === 'Free')
                                🎉
                            @elseif($active->plan === 'Premium')
                                ⭐
                            @else
                                👑
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Subscription Plans Grid --}}
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            @foreach($plans as $plan)
                <x-card class="relative flex flex-col text-center border-2 transition-all duration-300
                    @if(isset($active) && $active->plan === $plan['plan']) 
                        border-purple-500 shadow-lg 
                    @else 
                        border-gray-200 hover:border-yellow-500 hover:shadow-lg 
                    @endif">
                    
                    {{-- Current Plan Badge --}}
                    @if(isset($active) && $active->plan === $plan['plan'])
                        <div class="absolute top-0 right-0 bg-purple-600 text-white px-3 py-1 rounded-bl-lg rounded-tr-lg text-xs font-semibold">
                            Current Plan
                        </div>
                    @endif

                    {{-- Plan Icon --}}
                    <div class="text-5xl mb-4 mt-4">
                        @if($plan['plan'] === 'Free')
                            🎁
                        @elseif($plan['plan'] === 'Premium')
                            💎
                        @else
                            👑
                        @endif
                    </div>

                    {{-- Plan Name --}}
                    <h3 class="text-2xl font-bold mb-2
                        @if($plan['plan'] === 'Gold') 
                            text-yellow-600 
                        @elseif($plan['plan'] === 'Premium') 
                            text-purple-700 
                        @else 
                            text-gray-700 
                        @endif">
                        {{ $plan['plan'] }} Plan
                    </h3>

                    {{-- Price --}}
                    <div class="mb-4">
                        <p class="text-4xl font-bold text-gray-900">
                            GH₵ {{ number_format($plan['price_amount'], 2) }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">{{ $plan['duration'] }}</p>
                    </div>

                    {{-- Features List --}}
                    <div class="flex-grow mb-6">
                        <ul class="text-left space-y-3 px-6">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Subscribe Button --}}
                    <form action="{{ route('vendor.subscriptions.subscribe', $plan['plan']) }}" method="POST" class="mt-auto">
                        @csrf
                        @if(isset($active) && $active->plan === $plan['plan'])
                            <button type="button" disabled
                                    class="w-full bg-gray-300 text-gray-600 font-semibold py-3 px-6 rounded-lg cursor-not-allowed">
                                Current Plan
                            </button>
                        @else
                            <button type="submit"
                                    class="w-full font-semibold py-3 px-6 rounded-lg transition-colors duration-200
                                    @if($plan['plan'] === 'Gold')
                                        bg-yellow-500 hover:bg-yellow-600 text-white shadow-md hover:shadow-lg
                                    @elseif($plan['plan'] === 'Premium')
                                        bg-purple-600 hover:bg-purple-700 text-white shadow-md hover:shadow-lg
                                    @else
                                        bg-gray-600 hover:bg-gray-700 text-white
                                    @endif">
                                @if($plan['price_amount'] > 0)
                                    Subscribe Now
                                @else
                                    Get Started Free
                                @endif
                            </button>
                        @endif
                    </form>
                </x-card>
            @endforeach
        </div>

        {{-- Info Section --}}
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            {{-- Payment Info --}}
            <x-card class="p-6 bg-blue-50 border border-blue-200">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">💳 Payment Methods (Coming Soon)</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mobile Money (MTN, Vodafone, AirtelTigo)
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Visa / Mastercard
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Secure payment via Paystack
                    </li>
                    <li class="text-xs text-blue-700 mt-2 italic">
                        * Currently in test mode - no real charges will be made
                    </li>
                </ul>
            </x-card>

            {{-- Support Info --}}
            <x-card class="p-6 bg-purple-50 border border-purple-200">
                <h3 class="text-lg font-semibold text-purple-900 mb-3">💬 Need Help?</h3>
                <ul class="text-sm text-purple-800 space-y-2">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>All subscriptions auto-renew before expiry</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Upgrade or downgrade anytime</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Cancel anytime - no hidden fees</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-2 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Contact support for custom enterprise plans</span>
                    </li>
                </ul>
            </x-card>
        </div>

        {{-- FAQ Section --}}
        <x-card class="p-6 bg-gray-50 mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">❓ Frequently Asked Questions</h3>
            <div class="space-y-4 text-sm">
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">Can I change my plan later?</h4>
                    <p class="text-gray-600">Yes! You can upgrade or downgrade your plan at any time. Upgrades take effect immediately, and downgrades apply at the end of your current billing period.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">What happens when my subscription expires?</h4>
                    <p class="text-gray-600">Your account will automatically revert to the Free plan. All your data remains safe, and you can reactivate anytime.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">Are the payments secure?</h4>
                    <p class="text-gray-600">Absolutely! We use Paystack, Ghana's most trusted payment gateway. All transactions are encrypted and secure.</p>
                </div>
            </div>
        </x-card>

        {{-- Back Button --}}
        <div class="text-center mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
</x-app-layout>

