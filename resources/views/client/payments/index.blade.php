<x-client-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">
            Payments & Invoices
        </h2>
        <p class="text-sm text-gray-600 mt-1">View your payment history and invoices</p>
    </x-slot>

    @if($payments->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $payment['transaction_id'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $payment['vendor'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                GH₵ {{ number_format($payment['amount'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $payment['status'] === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $payment['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $payment['status'] === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($payment['status']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $payment['date']->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('client.payments.show', $payment['id']) }}" class="text-teal-600 hover:text-teal-900 font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-700 mb-2">No Payments Yet</h3>
            <p class="text-gray-600 mb-6">Your payment history will appear here once you make transactions.</p>
            <a href="{{ route('vendors.index') }}" class="inline-block bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition font-medium">
                Browse Vendors
            </a>
        </div>
    @endif

    {{-- Payment Info Card --}}
    <div class="mt-8 bg-blue-50 border-l-4 border-blue-400 p-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Payment Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>All payments are securely processed. You can download invoices and receipts for your records.</p>
                    <p class="mt-2">For payment issues or refund requests, please contact our <a href="{{ route('client.support.index') }}" class="underline font-medium">support team</a>.</p>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>

