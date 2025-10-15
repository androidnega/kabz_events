<x-admin-layout>
    <x-slot name="pageTitle">Create VIP Subscription</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('admin.vip-subscriptions.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to VIP Subscriptions
            </a>
        </div>

        <x-card class="p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-gem text-purple-600 mr-3"></i>
                    Create VIP Subscription
                </h2>
                <p class="text-sm text-gray-600 mt-1">Grant VIP access to a verified vendor</p>
            </div>

            <form action="{{ route('admin.vip-subscriptions.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Vendor Selection --}}
                <div>
                    <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Vendor <span class="text-red-500">*</span>
                    </label>
                    <select name="vendor_id" id="vendor_id" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Choose a vendor...</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">
                                {{ $vendor->business_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- VIP Plan Selection --}}
                <div>
                    <label for="vip_plan_id" class="block text-sm font-medium text-gray-700 mb-2">
                        VIP Plan <span class="text-red-500">*</span>
                    </label>
                    <select name="vip_plan_id" id="vip_plan_id" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Choose a plan...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" data-price="{{ $plan->price }}" data-duration="{{ $plan->duration_days }}">
                                {{ $plan->name }} - GHS {{ number_format($plan->price, 2) }} ({{ $plan->duration_days }} days)
                            </option>
                        @endforeach
                    </select>
                    @error('vip_plan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" required
                               value="{{ old('start_date', date('Y-m-d')) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                {{-- Payment Reference --}}
                <div>
                    <label for="payment_ref" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Reference (Optional)
                    </label>
                    <input type="text" name="payment_ref" id="payment_ref"
                           placeholder="e.g., PAY-12345678"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.vip-subscriptions.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-200 transition">
                        <i class="fas fa-plus mr-2"></i>
                        Create Subscription
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        // Auto-calculate end date based on plan selection
        document.getElementById('vip_plan_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const durationDays = parseInt(selectedOption.getAttribute('data-duration') || 0);
            
            if (durationDays > 0) {
                const startDate = document.getElementById('start_date').value;
                if (startDate) {
                    const start = new Date(startDate);
                    const end = new Date(start.getTime() + (durationDays * 24 * 60 * 60 * 1000));
                    document.getElementById('end_date').value = end.toISOString().split('T')[0];
                }
            }
        });

        // Auto-calculate end date when start date changes
        document.getElementById('start_date').addEventListener('change', function() {
            const selectedPlan = document.getElementById('vip_plan_id');
            const selectedOption = selectedPlan.options[selectedPlan.selectedIndex];
            const durationDays = parseInt(selectedOption.getAttribute('data-duration') || 0);
            
            if (durationDays > 0 && this.value) {
                const start = new Date(this.value);
                const end = new Date(start.getTime() + (durationDays * 24 * 60 * 60 * 1000));
                document.getElementById('end_date').value = end.toISOString().split('T')[0];
            }
        });
    </script>
    @endpush
</x-admin-layout>

