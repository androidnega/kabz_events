<x-admin-layout>
    <x-slot name="pageTitle">Create Featured Ad</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('admin.featured-ads.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Featured Ads
            </a>
        </div>

        <x-card class="p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-bullhorn text-purple-600 mr-3"></i>
                    Create New Featured Ad
                </h2>
                <p class="text-sm text-gray-600 mt-1">Create a featured ad for a verified vendor</p>
            </div>

            <form action="{{ route('admin.featured-ads.store') }}" method="POST" class="space-y-6">
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
                            <option value="{{ $vendor->id }}" data-services="{{ json_encode($vendor->services) }}">
                                {{ $vendor->business_name }} ({{ $vendor->services->count() }} services)
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Service Selection --}}
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Service <span class="text-red-500">*</span>
                    </label>
                    <select name="service_id" id="service_id" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">First select a vendor...</option>
                    </select>
                    @error('service_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Placement --}}
                    <div>
                        <label for="placement" class="block text-sm font-medium text-gray-700 mb-2">
                            Ad Placement <span class="text-red-500">*</span>
                        </label>
                        <select name="placement" id="placement" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="homepage">Homepage</option>
                            <option value="category">Category Page</option>
                            <option value="search">Search Results</option>
                        </select>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (GHS) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price" step="0.01" min="0" required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                {{-- Headline --}}
                <div>
                    <label for="headline" class="block text-sm font-medium text-gray-700 mb-2">
                        Ad Headline <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="headline" id="headline" maxlength="100" required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4" maxlength="500"
                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                {{-- Payment Reference --}}
                <div>
                    <label for="payment_ref" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Reference
                    </label>
                    <input type="text" name="payment_ref" id="payment_ref"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Admin Notes --}}
                <div>
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Admin Notes (Internal)
                    </label>
                    <textarea name="admin_notes" id="admin_notes" rows="3"
                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.featured-ads.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-200 transition">
                        <i class="fas fa-plus mr-2"></i>
                        Create Featured Ad
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        // Update service dropdown when vendor is selected
        document.getElementById('vendor_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const services = JSON.parse(selectedOption.getAttribute('data-services') || '[]');
            const serviceSelect = document.getElementById('service_id');
            
            serviceSelect.innerHTML = '<option value="">Choose a service...</option>';
            
            services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name + (service.price ? ' - GHS ' + service.price : '');
                serviceSelect.appendChild(option);
            });
        });
    </script>
    @endpush
</x-admin-layout>

