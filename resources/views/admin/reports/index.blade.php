<x-admin-layout>
    <x-slot name="pageTitle">Reports</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        <x-card class="p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-flag text-red-600 mr-3"></i>
                    User Reports & Issues
                </h2>
                <p class="text-sm text-gray-600 mt-1">Review and resolve user-submitted reports</p>
            </div>

            @if(session('success'))
                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
            @endif
            @if(session('info'))
                <x-alert type="info" class="mb-4">{{ session('info') }}</x-alert>
            @endif

            {{-- Filters & Search --}}
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        {{-- Search --}}
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search message or category..."
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Status Filter --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        {{-- Target Type Filter --}}
                        <div>
                            <label for="target_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="target_type" id="target_type" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                <option value="vendor" {{ request('target_type') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                <option value="client" {{ request('target_type') == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="service" {{ request('target_type') == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="other" {{ request('target_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category" id="category" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Categories</option>
                                @if(isset($categories))
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-filter mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.reports.index') }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                        <div class="text-sm text-gray-600">
                            Showing {{ $reports->total() }} report(s)
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reporter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Target</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-mono font-bold text-gray-900">#{{ $report->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $reporter = $report->reporter ?? $report->user;
                                    @endphp
                                    @if($reporter)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                                {{ substr($reporter->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $reporter->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $reporter->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        @if($report->target_type)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                {{ ucfirst($report->target_type) }}
                                            </span>
                                        @elseif($report->type)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                {{ ucfirst($report->type) }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($report->target)
                                        <div class="text-xs text-gray-500 mt-1">{{ $report->target->name }}</div>
                                    @elseif($report->vendor)
                                        <div class="text-xs text-gray-500 mt-1">{{ $report->vendor->business_name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($report->category)
                                        <span class="text-sm font-medium text-gray-900">{{ $report->category }}</span>
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($report->message, 40) }}</div>
                                    @else
                                        <div class="text-sm text-gray-700">{{ Str::limit($report->message, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($report->status === 'resolved')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✓ Resolved
                                        </span>
                                    @elseif($report->status === 'in_review')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            🔍 In Review
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            ⚠ Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $report->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $report->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.reports.show', $report->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-xs font-medium rounded-md text-blue-600 hover:bg-blue-50 transition">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                        @if($report->status !== 'resolved')
                                            <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Resolve
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-5xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">No reports found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your filters</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($reports->hasPages())
                <div class="mt-6">
                    {{ $reports->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-admin-layout>

