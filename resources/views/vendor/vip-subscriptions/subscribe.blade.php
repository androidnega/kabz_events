<x-vendor-layout>
    <x-slot name="title">Subscribe to {{ $plan->name }}</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('vendor.vip-subscriptions.index') }}" class="text-teal-600 hover:text-teal-700">← Back to VIP Plans</a>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            {{-- Plan Header --}}
            <div class="bg-gradient-to-r from-purple-600 to-pink-500 text-white p-8">
                <h2 class="text-3xl font-bold mb-2">{{ $plan->name }}</h2>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-bold">GH₵{{ number_format($plan->price, 0) }}</span>
                    <span class="text-lg opacity-90">/ {{ $plan->getDurationLabel() }}</span>
                </div>
            </div>

            {{-- Plan Features --}}
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">What's Included:</h3>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 text-xl mt-0.5">✓</span>
                        <span class="text-gray-700"><strong>{{ $plan->free_ads }}</strong> free featured ads per cycle</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 text-xl mt-0.5">✓</span>
                        <span class="text-gray-700">Top listing priority in search results</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 text-xl mt-0.5">✓</span>
                        <span class="text-gray-700">Upload up to <strong>{{ $plan->image_limit }}</strong> portfolio images</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 text-xl mt-0.5">✓</span>
                        <span class="text-gray-700">VIP badge displayed on your profile</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 text-xl mt-0.5">✓</span>
                        <span class="text-gray-700">Access to analytics dashboard</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 text-xl mt-0.5">✓</span>
                        <span class="text-gray-700">Priority customer support</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-green-600 text-xl mt-0.5">✓</span>
                        <span class="text-gray-700">Video uploads in portfolio (VIP only)</span>
                    </li>
                </ul>

                @if($plan->description)
                    <div class="bg-blue-50 rounded-lg p-4 mb-8">
                        <p class="text-sm text-blue-900">{{ $plan->description }}</p>
                    </div>
                @endif

                {{-- Payment Form --}}
                <form action="{{ route('vendor.vip-subscriptions.process', $plan->id) }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" id="payment_method" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="paystack">Paystack (Cards, Mobile Money)</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="agree_terms" value="1" required
                                   class="mt-1 w-5 h-5 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm text-gray-700">
                                I agree to the subscription terms and conditions. My subscription will automatically renew unless cancelled before the end date.
                            </span>
                        </label>
                        @error('agree_terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-700">Total Amount:</span>
                            <span class="text-3xl font-bold text-purple-600">GH₵{{ number_format($plan->price, 2) }}</span>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-6 py-4 rounded-lg font-bold text-lg transition">
                            Activate VIP Subscription
                        </button>
                        <a href="{{ route('vendor.vip-subscriptions.index') }}" 
                           class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-4 rounded-lg font-bold text-lg text-center transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-vendor-layout>

