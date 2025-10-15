<x-admin-layout>
    <x-slot name="pageTitle">Edit VIP Plan</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-3xl mx-auto">
            <x-card class="p-6">
                <div class="mb-6">
                    <a href="{{ route('admin.vip-plans.index') }}" class="text-purple-600 hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Back to VIP Plans
                    </a>
                </div>

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-crown text-purple-600 mr-3"></i>
                        Edit VIP Plan: {{ $plan->name }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Update VIP subscription plan details</p>
                </div>

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <ul class="list-disc list-inside text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.vip-plans.update', $plan->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Plan Name *</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               value="{{ old('name', $plan->name) }}"
                               placeholder="e.g., Gold VIP, Platinum VIP">
                        <p class="text-xs text-gray-500 mt-1">Choose a memorable name for this plan</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price (GH₵) *</label>
                            <input type="number" name="price" required min="0" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                   value="{{ old('price', $plan->price) }}"
                                   placeholder="0.00">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Days) *</label>
                            <input type="number" name="duration_days" required min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                   value="{{ old('duration_days', $plan->duration_days) }}"
                                   placeholder="30">
                            <p class="text-xs text-gray-500 mt-1">Common: 30, 60, 90, 365 days</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image Limit *</label>
                            <input type="number" name="image_limit" required min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                   value="{{ old('image_limit', $plan->image_limit) }}"
                                   placeholder="10">
                            <p class="text-xs text-gray-500 mt-1">Max images vendor can upload</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Free Ads *</label>
                            <input type="number" name="free_ads" required min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                   value="{{ old('free_ads', $plan->free_ads) }}"
                                   placeholder="3">
                            <p class="text-xs text-gray-500 mt-1">Number of free featured ads</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority Level *</label>
                            <select name="priority_level" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('priority_level', $plan->priority_level) == $i ? 'selected' : '' }}>
                                        Level {{ $i }} {{ $i >= 8 ? '(Highest)' : ($i <= 3 ? '(Lowest)' : '') }}
                                    </option>
                                @endfor
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Higher = More visibility</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                  placeholder="Describe the benefits and features of this plan...">{{ old('description', $plan->description) }}</textarea>
                    </div>

                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="status" value="1" {{ old('status', $plan->status) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm font-medium text-gray-700">Active (available for vendors to purchase)</span>
                        </label>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                        <p class="text-sm text-yellow-700">
                            <strong>⚠️ Important:</strong><br>
                            • Changes to price and duration won't affect existing subscriptions<br>
                            • Deactivating will hide the plan from new vendors (existing subscriptions continue)<br>
                            • This plan has {{ $plan->subscriptions_count ?? 0 }} active subscription(s)
                        </p>
                    </div>

                    <div class="flex items-center space-x-4 pt-4">
                        <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-save mr-2"></i> Update VIP Plan
                        </button>
                        <a href="{{ route('admin.vip-plans.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </a>
                        
                        @if(($plan->subscriptions_count ?? 0) == 0)
                        <form action="{{ route('admin.vip-plans.destroy', $plan->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this VIP plan?');"
                              class="ml-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-2"></i> Delete Plan
                            </button>
                        </form>
                        @endif
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-admin-layout>

