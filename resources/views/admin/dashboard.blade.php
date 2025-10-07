<x-app-layout>
    {{-- Page Header --}}
    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-user-shield text-teal-600 mr-3"></i> Admin Dashboard
            </h1>
            <div class="text-right text-gray-600">
                <p class="text-sm">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-xs">{{ now()->format('h:i A') }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto mt-8 px-4 sm:px-6 lg:px-8 space-y-8">
        {{-- Key Metrics Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            {{-- Total Vendors --}}
            <x-card class="border-l-4 border-teal-600 bg-teal-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Vendors</p>
                        <p class="text-3xl font-bold text-teal-900">{{ $stats['total_vendors'] }}</p>
                    </div>
                    <i class="fas fa-store text-4xl text-teal-400"></i>
                </div>
            </x-card>

            {{-- Verified Vendors --}}
            <x-card class="border-l-4 border-emerald-600 bg-emerald-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Verified Vendors</p>
                        <p class="text-3xl font-bold text-emerald-900">{{ $stats['verified_vendors'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-emerald-400"></i>
                </div>
            </x-card>

            {{-- Pending Verifications --}}
            <x-card class="border-l-4 border-amber-600 bg-amber-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Verifications</p>
                        <p class="text-3xl font-bold text-amber-900">{{ $stats['pending_verifications'] }}</p>
                    </div>
                    <i class="fas fa-clock text-4xl text-amber-400"></i>
                </div>
            </x-card>

            {{-- Total Clients --}}
            <x-card class="border-l-4 border-blue-600 bg-blue-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Clients</p>
                        <p class="text-3xl font-bold text-blue-900">{{ $stats['total_clients'] }}</p>
                    </div>
                    <i class="fas fa-users text-4xl text-blue-400"></i>
                </div>
            </x-card>

            {{-- Active Revenue --}}
            <x-card class="border-l-4 border-purple-600 bg-purple-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Revenue</p>
                        <p class="text-2xl font-bold text-purple-900">GH₵ {{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-4xl text-purple-400"></i>
                </div>
            </x-card>
        </div>

        {{-- Analytics Chart --}}
        <section class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Platform Analytics</h2>
            <x-card class="bg-white">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Platform Growth - {{ now()->year }}</h3>
                <div class="w-full overflow-x-auto">
                    <canvas id="growthChart" class="max-w-full" height="80"></canvas>
                </div>
            </x-card>
        </section>

        {{-- Management Sections --}}
        <section class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Management & Activity</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Pending Verifications --}}
                <x-card class="bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-user-check text-amber-600 mr-2"></i>
                            Pending Verifications
                        </h3>
                        <a href="{{ route('admin.verifications.index') }}" class="text-sm text-teal-600 hover:text-teal-800 font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="space-y-2">
                        @forelse($pendingVerifications as $request)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $request->vendor->business_name }}</p>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-clock text-gray-400 mr-1"></i>
                                        {{ $request->submitted_at->diffForHumans() }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.verifications.index') }}" class="text-teal-600 hover:text-teal-800 text-sm font-medium">
                                    Review <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-check-double text-gray-300 text-5xl mb-3"></i>
                                <p class="text-gray-500">No pending verifications</p>
                            </div>
                        @endforelse
                    </div>
                </x-card>

                {{-- Top Rated Vendors --}}
                <x-card class="bg-white">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-star text-yellow-600 mr-2"></i>
                        Top Rated Vendors
                    </h3>
                    <div class="space-y-2">
                        @forelse($topRatedVendors as $vendor)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $vendor->business_name }}</p>
                                    <div class="flex items-center mt-1">
                                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                                        <span class="text-sm text-gray-600 ml-1">{{ number_format($vendor->rating_cached, 1) }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-star-half-alt text-gray-300 text-5xl mb-3"></i>
                                <p class="text-gray-500">No rated vendors yet</p>
                            </div>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </section>

        {{-- Quick Actions --}}
        <section class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Quick Actions</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                {{-- Verify Vendors --}}
                <a href="{{ route('admin.verifications.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                    <i class="fas fa-user-check text-3xl text-teal-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 text-center">Verify Vendors</span>
                </a>

                {{-- Manage Clients --}}
                <a href="{{ route('admin.clients.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                    <i class="fas fa-users text-3xl text-blue-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 text-center">Manage Clients</span>
                </a>

                {{-- View Reports --}}
                <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                    <i class="fas fa-flag text-3xl text-red-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 text-center">View Reports</span>
                </a>

                {{-- Browse Vendors --}}
                <a href="{{ route('search.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                    <i class="fas fa-search text-3xl text-gray-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 text-center">Browse Vendors</span>
                </a>

                {{-- All Vendors --}}
                <a href="{{ route('vendors.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                    <i class="fas fa-store-alt text-3xl text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 text-center">All Vendors</span>
                </a>

                {{-- Public Site --}}
                <a href="{{ route('home') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition border border-gray-200">
                    <i class="fas fa-home text-3xl text-indigo-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 text-center">Public Site</span>
                </a>
            </div>
        </section>

        {{-- Recent Activity Stats --}}
        <section class="space-y-6 pb-8">
            <h2 class="text-2xl font-bold text-gray-800">Platform Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Services & Content --}}
                <x-card class="bg-white">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cubes text-indigo-600 mr-2"></i>
                        Platform Content
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Services</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_vendors'] * 3 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Active Vendors</span>
                            <span class="font-semibold text-gray-900">{{ $stats['verified_vendors'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Active Clients</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_clients'] }}</span>
                        </div>
                    </div>
                </x-card>

                {{-- Verification Status --}}
                <x-card class="bg-white">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tasks text-emerald-600 mr-2"></i>
                        Verification Status
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Verified</span>
                            <span class="font-semibold text-emerald-600">{{ $stats['verified_vendors'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pending</span>
                            <span class="font-semibold text-amber-600">{{ $stats['pending_verifications'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Unverified</span>
                            <span class="font-semibold text-gray-600">{{ $stats['total_vendors'] - $stats['verified_vendors'] }}</span>
                        </div>
                    </div>
                </x-card>

                {{-- Revenue Stats --}}
                <x-card class="bg-white">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                        Revenue Overview
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Revenue</span>
                            <span class="font-semibold text-gray-900">GH₵ {{ number_format($stats['total_revenue'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Active Subscriptions</span>
                            <span class="font-semibold text-gray-900">{{ $stats['verified_vendors'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Avg per Vendor</span>
                            <span class="font-semibold text-gray-900">GH₵ {{ $stats['verified_vendors'] > 0 ? number_format($stats['total_revenue'] / $stats['verified_vendors'], 2) : '0.00' }}</span>
                        </div>
                    </div>
                </x-card>
            </div>
        </section>
    </div>

    {{-- Chart.js Script --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('growthChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Vendors',
                        data: @json(array_values($monthlyStats['vendors'])),
                        borderColor: 'rgb(20, 184, 166)',
                        backgroundColor: 'rgba(20, 184, 166, 0.1)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'Clients',
                        data: @json(array_values($monthlyStats['clients'])),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly User Growth - {{ now()->year }}'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
