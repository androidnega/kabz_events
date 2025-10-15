<x-vendor-layout>
    <x-slot name="title">Callback Requests</x-slot>

    <div class="w-full">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Callback Requests</h2>
                    <p class="text-gray-600 mt-1">Manage client callback requests</p>
                </div>
                @if($pendingCount > 0)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                    <i class="fas fa-bell mr-2"></i> {{ $pendingCount }} Pending
                </span>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            @if($callbackRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($callbackRequests as $callback)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-indigo-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $callback->client_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="tel:{{ $callback->client_phone }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center">
                                        <i class="fas fa-phone mr-2"></i>
                                        {{ $callback->client_phone }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($callback->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i> Pending
                                        </span>
                                    @elseif($callback->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i> Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times mr-1"></i> Cancelled
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex flex-col">
                                        <span>{{ $callback->created_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-gray-400">{{ $callback->created_at->format('g:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($callback->status === 'pending')
                                        <div class="flex gap-2">
                                            <form action="{{ route('vendor.callbacks.complete', $callback) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 px-3 py-1 rounded bg-green-50 hover:bg-green-100 transition">
                                                    <i class="fas fa-check mr-1"></i> Complete
                                                </button>
                                            </form>
                                            <form action="{{ route('vendor.callbacks.cancel', $callback) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-gray-600 hover:text-gray-900 px-3 py-1 rounded bg-gray-50 hover:bg-gray-100 transition">
                                                    <i class="fas fa-times mr-1"></i> Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $callbackRequests->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-phone-slash text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Callback Requests</h3>
                    <p class="text-gray-500">When clients request callbacks, they will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</x-vendor-layout>

