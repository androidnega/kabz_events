<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight flex items-center">
                <i class="fas fa-flag text-red-600 mr-3"></i>
                Report an Issue
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <x-card class="p-6 sm:p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Report an Issue</h3>
                    <p class="text-sm text-gray-600">
                        Help us maintain a safe and trustworthy platform. Your report will be reviewed by our team.
                    </p>
                </div>

                <form action="{{ route('report.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Report Type --}}
                    <div>
                        <label for="target_type" class="block text-sm font-medium text-gray-700 mb-2">
                            What are you reporting? <span class="text-red-500">*</span>
                        </label>
                        <select name="target_type" id="target_type" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Select type...</option>
                            <option value="vendor">A Vendor</option>
                            <option value="client">A Client/User</option>
                            <option value="service">A Service/Listing</option>
                            <option value="other">Other Issue</option>
                        </select>
                        @error('target_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Issue Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Select category...</option>
                            <option value="Fake vendor">Fake vendor</option>
                            <option value="Payment dispute">Payment dispute</option>
                            <option value="Poor service quality">Poor service quality</option>
                            <option value="Harassment">Harassment</option>
                            <option value="Scam/Fraud">Scam/Fraud</option>
                            <option value="Inappropriate content">Inappropriate content</option>
                            <option value="Non-delivery">Non-delivery of service</option>
                            <option value="Technical issue">Technical issue</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Target User ID (Optional) --}}
                    <div>
                        <label for="target_id" class="block text-sm font-medium text-gray-700 mb-2">
                            User ID (if applicable)
                        </label>
                        <input type="number" name="target_id" id="target_id"
                               placeholder="Leave blank if not reporting a specific user"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <p class="mt-1 text-xs text-gray-500">
                            If reporting a specific user or vendor, enter their ID. Otherwise, leave blank.
                        </p>
                        @error('target_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Detailed Message --}}
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Detailed Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="6" required
                                  maxlength="1000"
                                  placeholder="Please provide as much detail as possible about the issue..."
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"></textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-500">
                                Be specific and include relevant details (dates, amounts, etc.)
                            </p>
                            <p class="text-xs text-gray-500">
                                <span id="charCount">0</span>/1000
                            </p>
                        </div>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notice Box --}}
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Important:</strong> False reports may result in account restrictions. 
                                    Please only submit genuine concerns. All reports are reviewed by our team.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-200 transition">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Report
                        </button>
                    </div>
                </form>
            </x-card>

            {{-- Help Section --}}
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                    <i class="fas fa-question-circle mr-2"></i>
                    Need Help?
                </h4>
                <p class="text-sm text-blue-800">
                    For urgent matters, you can also contact our support team directly at 
                    <a href="mailto:support@kabzsevent.com" class="font-medium underline">support@kabzsevent.com</a>
                    or call our Ghana hotline: <strong>+233 XX XXX XXXX</strong>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Character counter for message textarea
        document.getElementById('message').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });
    </script>
    @endpush
</x-app-layout>

