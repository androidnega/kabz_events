<x-admin-layout>
    <x-slot name="pageTitle">Verifications</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <x-card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user-check text-amber-600 mr-3"></i>
                        Vendor Verification Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Review pending requests and manage verified vendors</p>
                </div>
                <div class="text-sm text-gray-600">
                    @if(isset($requests))
                        Total: {{ $requests->total() }} pending
                    @elseif(isset($vendors))
                        Total: {{ $vendors->total() }} verified
                    @endif
                </div>
            </div>

            {{-- Tab Navigation --}}
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="?tab=pending" 
                       class="@if(!isset($tab) || $tab === 'pending') border-amber-500 text-amber-600 @else border-transparent text-gray-500 hover:text-gray-700 @endif
                              whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <i class="fas fa-clock mr-2"></i>Pending Requests
                        @if(isset($requests)) <span class="ml-2 bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-xs">{{ $requests->total() }}</span> @endif
                    </a>
                    <a href="?tab=verified" 
                       class="@if(isset($tab) && $tab === 'verified') border-green-500 text-green-600 @else border-transparent text-gray-500 hover:text-gray-700 @endif
                              whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <i class="fas fa-check-circle mr-2"></i>Verified Vendors
                        @if(isset($vendors)) <span class="ml-2 bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs">{{ $vendors->total() }}</span> @endif
                    </a>
                </nav>
            </div>

            @if(session('success'))
                <x-alert type="success" class="mb-4">
                    {{ session('success') }}
                </x-alert>
            @endif

            @if(session('error'))
                <x-alert type="danger" class="mb-4">
                    {{ session('error') }}
                </x-alert>
            @endif

            @if(!isset($tab) || $tab === 'pending')
                {{-- PENDING REQUESTS TAB --}}
                @if(isset($requests) && $requests->isEmpty())
                    <div class="text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No verification requests yet</h3>
                        <p class="text-gray-600">Verification requests from vendors will appear here</p>
                    </div>
                @elseif(isset($requests))
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vendor
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Submitted
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Document
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($requests as $req)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Vendor Info --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $req->vendor->business_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $req->vendor->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($req->status === 'approved')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                ✓ Approved
                                            </span>
                                        @elseif($req->status === 'rejected')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                ✗ Rejected
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                ⏳ Pending
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Submitted Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $req->submitted_at->format('d M Y') }}
                                        <br>
                                        <span class="text-xs text-gray-400">{{ $req->submitted_at->format('h:i A') }}</span>
                                    </td>

                                    {{-- Document Link --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ asset('storage/' . $req->id_document_path) }}" 
                                           target="_blank"
                                           class="text-purple-600 hover:text-purple-900 underline flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            View Document
                                        </a>
                                        
                                        @if($req->social_links && (isset($req->social_links['facebook']) || isset($req->social_links['instagram'])))
                                            <div class="mt-2 flex gap-2">
                                                @if(isset($req->social_links['facebook']))
                                                    <a href="{{ $req->social_links['facebook'] }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Facebook">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                @if(isset($req->social_links['instagram']))
                                                    <a href="{{ $req->social_links['instagram'] }}" target="_blank" class="text-pink-600 hover:text-pink-800" title="Instagram">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($req->status === 'pending')
                                            <div class="flex gap-2">
                                                {{-- Approve Button --}}
                                                <form action="{{ route('admin.verifications.approve', $req->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                                        ✓ Approve
                                                    </button>
                                                </form>

                                                {{-- Reject Button --}}
                                                <form action="{{ route('admin.verifications.reject', $req->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="admin_note" value="Rejected by admin - documentation not clear">
                                                    <button type="submit" 
                                                            onclick="return confirm('Are you sure you want to reject this verification request?')"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                                        ✗ Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">
                                                @if($req->decided_at)
                                                    Decided {{ $req->decided_at->diffForHumans() }}
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Admin Note Row (if exists) --}}
                                @if(($req->admin_note || $req->decidedBy) && $req->status !== 'pending')
                                    <tr class="bg-gray-50">
                                        <td colspan="5" class="px-6 py-3">
                                            <div class="text-sm space-y-1">
                                                @if($req->decidedBy)
                                                    <div>
                                                        <span class="font-semibold text-gray-700">Reviewed by:</span>
                                                        <span class="text-gray-900">{{ $req->decidedBy->name }}</span>
                                                        <span class="text-xs text-gray-500">({{ $req->decidedBy->getRoleNames()->first() }})</span>
                                                    </div>
                                                @endif
                                                @if($req->admin_note)
                                                    <div>
                                                        <span class="font-semibold text-gray-700">Note:</span>
                                                        <span class="text-gray-600">{{ $req->admin_note }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($requests->hasPages())
                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>
                @endif
                @endif
            @elseif($tab === 'verified')
                {{-- VERIFIED VENDORS TAB --}}
                @if(isset($vendors) && $vendors->isEmpty())
                    <div class="text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No verified vendors yet</h3>
                        <p class="text-gray-600">Approved vendors will appear here</p>
                    </div>
                @elseif(isset($vendors))
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vendor ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vendor Details
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Services
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Verified On
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($vendors as $vendor)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-mono font-bold text-green-600">
                                            {{ $vendor->user->display_id ?? 'VND-' . str_pad($vendor->id, 6, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-store text-green-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $vendor->business_name }}
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                                    </span>
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $vendor->user->email ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-400 mt-1">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $vendor->address ?? 'No address' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            @if($vendor->services->isNotEmpty())
                                                @foreach($vendor->services->take(2) as $service)
                                                    <span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs mr-1 mb-1">
                                                        {{ $service->category->name ?? 'N/A' }}
                                                    </span>
                                                @endforeach
                                                @if($vendor->services->count() > 2)
                                                    <span class="text-xs text-gray-500">+{{ $vendor->services->count() - 2 }} more</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 text-xs">No services</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $vendor->verified_at ? $vendor->verified_at->format('M d, Y') : 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $vendor->verified_at ? $vendor->verified_at->diffForHumans() : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('vendors.show', $vendor) }}" target="_blank" 
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        
                                        <form action="{{ route('admin.verifications.suspend', $vendor->id) }}" method="POST" class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to suspend this vendor\'s verification? This is temporary and can be restored.');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition">
                                                <i class="fas fa-pause-circle mr-1"></i> Suspend
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.verifications.cancel', $vendor->id) }}" method="POST" class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to CANCEL this vendor\'s verification? This is permanent!');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition">
                                                <i class="fas fa-times-circle mr-1"></i> Cancel
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($vendors->hasPages())
                    <div class="mt-6">
                        {{ $vendors->appends(['tab' => 'verified'])->links() }}
                    </div>
                @endif
                @endif
            @endif
        </x-card>
    </div>
</x-admin-layout>

