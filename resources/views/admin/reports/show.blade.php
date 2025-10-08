<x-admin-layout>
    <x-slot name="pageTitle">Report Details</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
        @endif
        @if(session('error'))
            <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
        @endif
        @if(session('info'))
            <x-alert type="info" class="mb-4">{{ session('info') }}</x-alert>
        @endif

        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('admin.reports.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Reports
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Report Details --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Report Header --}}
                <x-card class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-flag text-red-600 mr-3"></i>
                                Report #{{ $report->id }}
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Submitted {{ $report->created_at->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        
                        {{-- Status Badge --}}
                        <div>
                            @if($report->status === 'resolved')
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ Resolved
                                </span>
                            @elseif($report->status === 'in_review')
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    🔍 In Review
                                </span>
                            @else
                                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    ⚠ Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Report Type & Category --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase mb-1">Report Type</p>
                            <p class="font-semibold text-gray-900">
                                <i class="fas fa-{{ $report->target_type === 'vendor' ? 'store' : ($report->target_type === 'client' ? 'user' : 'box') }} mr-2"></i>
                                {{ ucfirst($report->target_type ?? $report->type) }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 uppercase mb-1">Category</p>
                            <p class="font-semibold text-gray-900">{{ $report->category ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Report Message --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3">Report Details</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $report->message }}</p>
                        </div>
                    </div>

                    {{-- Admin Response (if exists) --}}
                    @if($report->admin_response)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3">Previous Admin Response</h3>
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                                <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $report->admin_response }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Admin Note (if exists) --}}
                    @if($report->admin_note)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3">Admin Notes</h3>
                            <div class="bg-purple-50 border-l-4 border-purple-400 p-4 rounded-lg">
                                <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $report->admin_note }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Resolved At --}}
                    @if($report->resolved_at)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>Resolved on:</strong> {{ $report->resolved_at->format('F j, Y \a\t g:i A') }}
                                ({{ $report->resolved_at->diffForHumans() }})
                            </p>
                        </div>
                    @endif
                </x-card>

                {{-- Update Status Form --}}
                <x-card class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-edit mr-2"></i>
                        Update Report Status
                    </h3>

                    <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select name="status" id="status" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_review" {{ $report->status === 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        <div>
                            <label for="admin_note" class="block text-sm font-medium text-gray-700 mb-2">
                                Admin Note (Internal)
                            </label>
                            <textarea name="admin_note" id="admin_note" rows="4"
                                      placeholder="Add internal notes about actions taken or findings..."
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none">{{ old('admin_note', $report->admin_note) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                This note is for internal admin use and will not be shown to the reporter.
                            </p>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            @if($report->status === 'resolved')
                                <a href="{{ route('admin.reports.reopen', $report->id) }}"
                                   onclick="event.preventDefault(); document.getElementById('reopen-form').submit();"
                                   class="px-6 py-3 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-redo mr-2"></i>
                                    Reopen Report
                                </a>
                            @endif
                            <button type="submit"
                                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-save mr-2"></i>
                                Update Status
                            </button>
                        </div>
                    </form>

                    @if($report->status === 'resolved')
                        <form id="reopen-form" action="{{ route('admin.reports.reopen', $report->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endif
                </x-card>
            </div>

            {{-- Sidebar - User Information --}}
            <div class="space-y-6">
                
                {{-- Reporter Information --}}
                <x-card class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                        <i class="fas fa-user mr-2"></i>
                        Reporter
                    </h3>
                    @if($report->user || $report->reporter)
                        @php $reporter = $report->reporter ?? $report->user; @endphp
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr($reporter->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-900">{{ $reporter->name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $reporter->id }}</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-gray-200 space-y-2">
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                    {{ $reporter->email }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-phone mr-2 text-gray-400"></i>
                                    {{ $reporter->phone ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-user-tag mr-2 text-gray-400"></i>
                                    {{ ucfirst($reporter->getRoleNames()->first() ?? 'User') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No reporter information available.</p>
                    @endif
                </x-card>

                {{-- Target Information --}}
                @if($report->target_id && $report->target)
                    <x-card class="p-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                            <i class="fas fa-bullseye mr-2"></i>
                            Reported User
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr($report->target->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-900">{{ $report->target->name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $report->target->id }}</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-gray-200 space-y-2">
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                    {{ $report->target->email }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-phone mr-2 text-gray-400"></i>
                                    {{ $report->target->phone ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-user-tag mr-2 text-gray-400"></i>
                                    {{ ucfirst($report->target->getRoleNames()->first() ?? 'User') }}
                                </p>
                            </div>
                        </div>
                    </x-card>
                @elseif($report->vendor_id && $report->vendor)
                    <x-card class="p-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                            <i class="fas fa-store mr-2"></i>
                            Reported Vendor
                        </h3>
                        <div class="space-y-3">
                            <p class="font-semibold text-gray-900">{{ $report->vendor->business_name }}</p>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-id-card mr-2 text-gray-400"></i>
                                    Vendor ID: {{ $report->vendor->id }}
                                </p>
                                @if($report->vendor->user)
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        Owner: {{ $report->vendor->user->name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                        {{ $report->vendor->user->email }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </x-card>
                @endif

                {{-- Quick Actions --}}
                <x-card class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                    <div class="space-y-2">
                        @if($report->target_id && $report->target)
                            <a href="#" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition text-center">
                                <i class="fas fa-user-shield mr-2"></i>
                                View User Profile
                            </a>
                        @endif
                        @if($report->vendor_id && $report->vendor)
                            <a href="#" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition text-center">
                                <i class="fas fa-store mr-2"></i>
                                View Vendor Details
                            </a>
                        @endif
                        <a href="#" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition text-center">
                            <i class="fas fa-envelope mr-2"></i>
                            Contact Reporter
                        </a>
                    </div>
                </x-card>

                {{-- Report Timeline --}}
                <x-card class="p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">
                        <i class="fas fa-history mr-2"></i>
                        Timeline
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 text-xs"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Report Created</p>
                                <p class="text-xs text-gray-500">{{ $report->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                        @if($report->resolved_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Report Resolved</p>
                                    <p class="text-xs text-gray-500">{{ $report->resolved_at->format('M j, Y g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-admin-layout>

