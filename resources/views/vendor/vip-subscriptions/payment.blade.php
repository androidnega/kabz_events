<x-vendor-layout>
    <x-slot name="title">Complete VIP Subscription Payment</x-slot>

    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Complete Your VIP Payment</h2>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">VIP Subscription Summary</h3>
            
            <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Plan:</span>
                    <span class="font-semibold">{{ $subscription->vipPlan->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-semibold">{{ $subscription->vipPlan->getDurationLabel() }} ({{ $subscription->vipPlan->duration_days }} days)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Start Date:</span>
                    <span class="font-semibold">{{ $subscription->start_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">End Date:</span>
                    <span class="font-semibold">{{ $subscription->end_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Free Featured Ads:</span>
                    <span class="font-semibold">{{ $subscription->vipPlan->free_ads }} per cycle</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Portfolio Images:</span>
                    <span class="font-semibold">Up to {{ $subscription->vipPlan->image_limit }} images</span>
                </div>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold">Total Amount:</span>
                    <span class="text-2xl font-bold text-purple-600">GH₵ {{ number_format($subscription->vipPlan->price, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Note:</strong> Payment integration with Paystack will be completed. For now, you can proceed with a test payment.
            </p>
        </div>

        <form action="{{ route('vendor.vip-subscriptions.verify-payment', $subscription->id) }}" method="POST">
            @csrf
            <input type="hidden" name="reference" value="VIP_TEST_{{ uniqid() }}">
            
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Proceed with Payment (Test Mode)
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('vendor.vip-subscriptions.index') }}" class="text-gray-600 hover:text-gray-800">Cancel and go back</a>
        </div>
    </div>
</x-vendor-layout>

