<x-vendor-layout>
    <x-slot name="title">Create Featured Ad</x-slot>

    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Create Featured Ad</h2>
        <p class="text-sm text-gray-600">Promote your service to reach more clients</p>
    </div>

    {{-- Pricing Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-blue-900 mb-2">💰 Pricing</h3>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div>
                <strong class="text-blue-900">Homepage:</strong>
                <span class="text-blue-800">GH₵{{ $pricing['homepage'] }}/day</span>
            </div>
            <div>
                <strong class="text-blue-900">Category Page:</strong>
                <span class="text-blue-800">GH₵{{ $pricing['category'] }}/day</span>
            </div>
            <div>
                <strong class="text-blue-900">Search Results:</strong>
                <span class="text-blue-800">GH₵{{ $pricing['search'] }}/day</span>
            </div>
        </div>
    </div>

    @if($freeAdsRemaining > 0)
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-purple-900 mb-2">🎉 VIP Benefit!</h3>
            <p class="text-sm text-purple-800">
                You have <strong>{{ $freeAdsRemaining }}</strong> free featured ad(s) remaining. Check "Use Free Ad Slot" below to use one.
            </p>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('vendor.featured-ads.store') }}" method="POST" enctype="multipart/form-data" id="featuredAdForm">
        @csrf

        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            {{-- Service Selection --}}
            <div>
                <label for="service_id" class="block text-sm font-semibold text-gray-700 mb-2">Select Service*</label>
                <select name="service_id" id="service_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="">-- Choose a Service --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->title }}
                        </option>
                    @endforeach
                </select>
                @error('service_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Placement --}}
            <div>
                <label for="placement" class="block text-sm font-semibold text-gray-700 mb-2">Placement*</label>
                <select name="placement" id="placement" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="search" {{ old('placement') == 'search' ? 'selected' : '' }}>Search Results (GH₵{{ $pricing['search'] }}/day)</option>
                    <option value="category" {{ old('placement') == 'category' ? 'selected' : '' }}>Category Page (GH₵{{ $pricing['category'] }}/day)</option>
                    <option value="homepage" {{ old('placement') == 'homepage' ? 'selected' : '' }}>Homepage (GH₵{{ $pricing['homepage'] }}/day)</option>
                </select>
                @error('placement')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Duration & Start Date --}}
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="duration" class="block text-sm font-semibold text-gray-700 mb-2">Duration*</label>
                    <select name="duration" id="duration" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="3" {{ old('duration') == 3 ? 'selected' : '' }}>3 days</option>
                        <option value="7" {{ old('duration') == 7 ? 'selected' : '' }}>7 days</option>
                        <option value="14" {{ old('duration') == 14 ? 'selected' : '' }}>14 days</option>
                        <option value="30" {{ old('duration') == 30 ? 'selected' : '' }}>30 days</option>
                    </select>
                    @error('duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date*</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="{{ old('start_date', date('Y-m-d')) }}" 
                           min="{{ date('Y-m-d') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Headline --}}
            <div>
                <label for="headline" class="block text-sm font-semibold text-gray-700 mb-2">Headline* (max 100 characters)</label>
                <input type="text" name="headline" id="headline" maxlength="100" required
                       value="{{ old('headline') }}"
                       placeholder="e.g., Top Wedding Photographer in Accra"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                @error('headline')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description (optional)</label>
                <textarea name="description" id="description" rows="3" maxlength="500"
                          placeholder="Brief promotional description..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Custom Image --}}
            <div>
                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Custom Banner Image (optional)</label>
                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Max size: 2MB. Formats: JPG, PNG</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Use Free Ad Checkbox --}}
            @if($freeAdsRemaining > 0)
                <div class="flex items-center gap-2 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <input type="checkbox" name="use_free_ad" id="use_free_ad" value="1" {{ old('use_free_ad') ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600">
                    <label for="use_free_ad" class="text-sm font-semibold text-purple-900">
                        Use a free ad slot from my VIP subscription
                    </label>
                </div>
            @endif

            {{-- Price Calculation --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold text-gray-700">Total Cost:</span>
                    <span class="text-2xl font-bold text-teal-600" id="totalCost">GH₵ 0.00</span>
                </div>
                <p class="text-xs text-gray-500 mt-2" id="calculationDetails">Select placement and duration to calculate</p>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex gap-3">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    Create Featured Ad
                </button>
                <a href="{{ route('vendor.featured-ads.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</x-vendor-layout>

<script type="application/json" id="pricingData">
    @json([
        'homepage' => $pricing['homepage'] ?? 0,
        'category' => $pricing['category'] ?? 0,
        'search' => $pricing['search'] ?? 0
    ])
</script>

<script>
    // Price calculation
    const pricing = JSON.parse(document.getElementById('pricingData').textContent);

    const placementSelect = document.getElementById('placement');
    const durationSelect = document.getElementById('duration');
    const useFreeAdCheckbox = document.getElementById('use_free_ad');
    const totalCostElement = document.getElementById('totalCost');
    const calculationDetailsElement = document.getElementById('calculationDetails');

    function calculateCost() {
        const placement = placementSelect.value;
        const duration = parseInt(durationSelect.value);
        const useFreeAd = useFreeAdCheckbox ? useFreeAdCheckbox.checked : false;

        if (placement && duration) {
            const pricePerDay = pricing[placement];
            let totalCost = pricePerDay * duration;

            if (useFreeAd) {
                totalCost = 0;
            }

            totalCostElement.textContent = 'GH₵ ' + totalCost.toFixed(2);
            
            if (useFreeAd) {
                calculationDetailsElement.textContent = 'Using free VIP ad slot - No payment required!';
            } else {
                calculationDetailsElement.textContent = `GH₵${pricePerDay} × ${duration} days = GH₵${totalCost.toFixed(2)}`;
            }
        }
    }

    placementSelect.addEventListener('change', calculateCost);
    durationSelect.addEventListener('change', calculateCost);
    if (useFreeAdCheckbox) {
        useFreeAdCheckbox.addEventListener('change', calculateCost);
    }

    // Calculate on page load
    calculateCost();
</script>

