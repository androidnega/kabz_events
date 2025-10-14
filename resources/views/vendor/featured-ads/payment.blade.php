<x-vendor-layout>
    <x-slot name="title">Payment for Featured Ad</x-slot>

    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Complete Your Payment</h2>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Featured Ad Summary</h3>
            
            <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Service:</span>
                    <span class="font-semibold">{{ $featuredAd->service->title }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Headline:</span>
                    <span class="font-semibold">{{ $featuredAd->headline }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Placement:</span>
                    <span class="font-semibold">{{ ucfirst($featuredAd->placement) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-semibold">{{ $featuredAd->start_date->format('M d') }} - {{ $featuredAd->end_date->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold">Total Amount:</span>
                    <span class="text-2xl font-bold text-teal-600">GH₵ {{ number_format($featuredAd->price, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Note:</strong> Payment integration with Paystack will be completed. For now, you can proceed with a test payment.
            </p>
        </div>

        <form action="{{ route('vendor.featured-ads.verify-payment', $featuredAd->id) }}" method="POST">
            @csrf
            <input type="hidden" name="reference" value="TEST_{{ uniqid() }}">
            
            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Proceed with Payment (Test Mode)
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('vendor.featured-ads.index') }}" class="text-gray-600 hover:text-gray-800">Cancel and go back</a>
        </div>
    </div>
</x-vendor-layout>

