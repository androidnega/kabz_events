<x-admin-layout>
    <x-slot name="pageTitle">Pending Subscriptions</x-slot>

    <div class="p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Pending Subscription Approvals</h2>
            <p class="text-gray-600">Review and approve vendor subscription payments</p>
        </div>

        @if(session('success'))
            <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
        @endif

        @if(session('error'))
            <x-alert type="danger" class="mb-6">{{ session('error') }}</x-alert>
        @endif

        @if($subscriptions->isEmpty())
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">All Caught Up!</h3>
                <p class="text-gray-600">No pending subscription approvals at the moment.</p>
            </div>
        @else
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auto-Approval</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $subscription->vendor->business_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $subscription->vendor->user->email }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($subscription->plan === 'Gold') bg-yellow-100 text-yellow-800
                                        @elseif($subscription->plan === 'Premium') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $subscription->plan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    GHS {{ number_format($subscription->price_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $subscription->paid_at->format('M d, Y') }}<br>
                                    <span class="text-xs">{{ $subscription->paid_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($subscription->payment_expires_at)
                                        <span class="text-yellow-600 font-medium">
                                            {{ $subscription->payment_expires_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- Approve Button --}}
                                        <button
                                            data-action="approve"
                                            data-id="{{ $subscription->id }}"
                                            data-vendor="{{ $subscription->vendor->business_name }}"
                                            data-plan="{{ $subscription->plan }}"
                                            class="approve-btn text-green-600 hover:text-green-900">
                                            <i class="fas fa-check-circle"></i> Approve
                                        </button>

                                        {{-- Reject Button --}}
                                        <button
                                            data-action="reject"
                                            data-id="{{ $subscription->id }}"
                                            data-vendor="{{ $subscription->vendor->business_name }}"
                                            class="reject-btn text-red-600 hover:text-red-900">
                                            <i class="fas fa-times-circle"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

    {{-- Approve Modal --}}
    <div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Approve Subscription</h3>
            <p class="text-sm text-gray-600 mb-4" id="approveModalText"></p>
            
            <form id="approveForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note (Optional)</label>
                    <textarea name="approval_note" rows="3" 
                              class="w-full border-gray-300 rounded-lg text-sm"
                              placeholder="Add a note for the vendor..."></textarea>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Approve
                    </button>
                    <button type="button" onclick="closeApproveModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Subscription</h3>
            <p class="text-sm text-gray-600 mb-4" id="rejectModalText"></p>
            
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                    <textarea name="approval_note" rows="3" required
                              class="w-full border-gray-300 rounded-lg text-sm"
                              placeholder="Explain why this subscription is being rejected..."></textarea>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Reject
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Approve button event listeners
            document.querySelectorAll('.approve-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const vendor = this.dataset.vendor;
                    const plan = this.dataset.plan;
                    openApproveModal(id, vendor, plan);
                });
            });

            // Reject button event listeners
            document.querySelectorAll('.reject-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const vendor = this.dataset.vendor;
                    openRejectModal(id, vendor);
                });
            });
        });

        function openApproveModal(id, vendor, plan) {
            document.getElementById('approveModalText').textContent = 
                `Approve ${vendor}'s ${plan} subscription?`;
            document.getElementById('approveForm').action = `/dashboard/admin/subscriptions/${id}/approve`;
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function openRejectModal(id, vendor) {
            document.getElementById('rejectModalText').textContent = 
                `Reject ${vendor}'s subscription? This action cannot be undone.`;
            document.getElementById('rejectForm').action = `/dashboard/admin/subscriptions/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-admin-layout>

