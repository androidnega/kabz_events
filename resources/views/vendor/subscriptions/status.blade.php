<x-vendor-layout>
    <x-slot name="title">Subscription Status</x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ route('vendor.subscriptions') }}" class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Back to Subscriptions
            </a>
        </div>

        {{-- Subscription Status --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Subscription Status</h2>
            <p class="text-gray-600">Track your subscription payment and approval status</p>
        </div>

        {{-- Status Cards --}}
        <div class="grid md:grid-cols-3 gap-4 mb-6">
            {{-- Payment Status --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">Payment Status</h3>
                    @if($subscription->isPaid())
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                    @endif
                </div>
                <div class="text-2xl font-bold 
                    @if($subscription->isPaid()) text-green-600
                    @elseif($subscription->payment_status === 'failed') text-red-600
                    @else text-yellow-600 @endif">
                    {{ ucfirst($subscription->payment_status) }}
                </div>
                @if($subscription->paid_at)
                    <p class="text-xs text-gray-500 mt-1">Paid on {{ $subscription->paid_at->format('M d, Y') }}</p>
                @endif
            </div>

            {{-- Approval Status --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">Approval Status</h3>
                    @if($subscription->isApproved())
                        <i class="fas fa-check-circle text-green-500"></i>
                    @elseif($subscription->approval_status === 'rejected')
                        <i class="fas fa-times-circle text-red-500"></i>
                    @else
                        <i class="fas fa-clock text-yellow-500"></i>
                    @endif
                </div>
                <div class="text-2xl font-bold 
                    @if($subscription->isApproved()) text-green-600
                    @elseif($subscription->approval_status === 'rejected') text-red-600
                    @else text-yellow-600 @endif">
                    {{ ucfirst($subscription->approval_status) }}
                </div>
                @if($subscription->approved_at)
                    <p class="text-xs text-gray-500 mt-1">{{ $subscription->approved_at->format('M d, Y') }}</p>
                @endif
            </div>

            {{-- Subscription Status --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Subscription Status</h3>
                <div class="text-2xl font-bold 
                    @if($subscription->status === 'active') text-green-600
                    @else text-gray-600 @endif">
                    {{ ucfirst($subscription->status) }}
                </div>
                @if($subscription->isActive())
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $subscription->ends_at ? 'Until ' . $subscription->ends_at->format('M d, Y') : 'Lifetime' }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Subscription Details --}}
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-200 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Subscription Details</h3>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-600">Plan</p>
                    <p class="font-semibold text-gray-900">{{ $subscription->plan }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Amount Paid</p>
                    <p class="font-semibold text-gray-900">GHS {{ number_format($subscription->price_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Started</p>
                    <p class="font-semibold text-gray-900">{{ $subscription->started_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Expires</p>
                    <p class="font-semibold text-gray-900">
                        {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Never (Lifetime)' }}
                    </p>
                </div>
                @if($subscription->paystack_reference)
                <div class="md:col-span-2">
                    <p class="text-gray-600">Payment Reference</p>
                    <p class="font-mono text-xs text-gray-700">{{ $subscription->paystack_reference }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Timeline/Progress --}}
        @if($subscription->isPaid())
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Approval Timeline</h3>
                
                @if($subscription->isPendingApproval())
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-yellow-900">Under Review</h4>
                                <p class="text-sm text-yellow-800 mt-1">
                                    Your subscription is pending admin approval. This typically takes up to 24 hours.
                                </p>
                                @if($subscription->payment_expires_at)
                                    <p class="text-xs text-yellow-700 mt-2">
                                        <strong>Auto-approval in:</strong> {{ $subscription->payment_expires_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($subscription->isApproved())
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-green-900">Approved!</h4>
                                <p class="text-sm text-green-800 mt-1">
                                    Your subscription has been approved and is now active. Enjoy your {{ $subscription->plan }} benefits!
                                </p>
                                @if($subscription->approval_note)
                                    <p class="text-xs text-green-700 mt-2">
                                        <strong>Note:</strong> {{ $subscription->approval_note }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($subscription->approval_status === 'rejected')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-red-900">Rejected</h4>
                                <p class="text-sm text-red-800 mt-1">
                                    Unfortunately, your subscription was not approved.
                                </p>
                                @if($subscription->approval_note)
                                    <p class="text-xs text-red-700 mt-2">
                                        <strong>Reason:</strong> {{ $subscription->approval_note }}
                                    </p>
                                @endif
                                <a href="{{ route('vendor.subscriptions') }}" class="inline-block mt-3 text-sm text-red-700 font-semibold hover:text-red-800">
                                    Try Again →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Process Steps --}}
                <div class="mt-6">
                    <div class="flex items-center">
                        {{-- Step 1: Payment --}}
                        <div class="flex-1">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $subscription->isPaid() ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <p class="text-xs text-gray-600 mt-2 text-center">Payment</p>
                            </div>
                        </div>

                        <div class="flex-1 h-1 {{ $subscription->isPaid() ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                        {{-- Step 2: Review --}}
                        <div class="flex-1">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $subscription->isApproved() ? 'bg-green-500 text-white' : ($subscription->isPaid() ? 'bg-yellow-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <p class="text-xs text-gray-600 mt-2 text-center">Review</p>
                            </div>
                        </div>

                        <div class="flex-1 h-1 {{ $subscription->isApproved() ? 'bg-green-500' : 'bg-gray-300' }}"></div>

                        {{-- Step 3: Active --}}
                        <div class="flex-1">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $subscription->isApproved() && $subscription->status === 'active' ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <p class="text-xs text-gray-600 mt-2 text-center">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Help Section --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <h4 class="text-sm font-semibold text-blue-900 mb-2">Need Help?</h4>
            <p class="text-sm text-blue-800">
                If you have questions about your subscription status, please contact our support team.
            </p>
        </div>
    </div>
</x-vendor-layout>

