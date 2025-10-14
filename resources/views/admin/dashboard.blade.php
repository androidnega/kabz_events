<x-admin-layout>
    <x-slot name="pageTitle">Admin Dashboard</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Page Header --}}
        <div class="mb-6 sm:mb-8 bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-tachometer-alt text-teal-600 mr-2 sm:mr-3"></i>
                        <span>Dashboard Overview</span>
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Monitor your platform's key metrics and activities</p>
                </div>
                <div class="text-left sm:text-right text-gray-600">
                    <p class="text-sm font-medium">{{ now()->format('l, F j, Y') }}</p>
                    <p class="text-xs text-gray-500">{{ now()->format('h:i A') }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 sm:space-y-8">
        {{-- Key Metrics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-6">
            {{-- Total Vendors --}}
            <x-card class="border-l-4 border-teal-600 bg-teal-50 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Vendors</p>
                        <p class="text-2xl sm:text-3xl font-bold text-teal-900 truncate">{{ $stats['total_vendors'] }}</p>
                    </div>
                    <i class="fas fa-store text-3xl sm:text-4xl text-teal-400 flex-shrink-0"></i>
                </div>
            </x-card>

            {{-- Verified Vendors --}}
            <x-card class="border-l-4 border-emerald-600 bg-emerald-50 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Verified Vendors</p>
                        <p class="text-2xl sm:text-3xl font-bold text-emerald-900 truncate">{{ $stats['verified_vendors'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-3xl sm:text-4xl text-emerald-400 flex-shrink-0"></i>
                </div>
            </x-card>

            {{-- Pending Verifications --}}
            <x-card class="border-l-4 border-amber-600 bg-amber-50 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Pending Verifications</p>
                        <p class="text-2xl sm:text-3xl font-bold text-amber-900 truncate">{{ $stats['pending_verifications'] }}</p>
                    </div>
                    <i class="fas fa-clock text-3xl sm:text-4xl text-amber-400 flex-shrink-0"></i>
                </div>
            </x-card>

            {{-- Total Clients --}}
            <x-card class="border-l-4 border-blue-600 bg-blue-50 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Clients</p>
                        <p class="text-2xl sm:text-3xl font-bold text-blue-900 truncate">{{ $stats['total_clients'] }}</p>
                    </div>
                    <i class="fas fa-users text-3xl sm:text-4xl text-blue-400 flex-shrink-0"></i>
                </div>
            </x-card>

            {{-- Active Revenue --}}
            <x-card class="border-l-4 border-purple-600 bg-purple-50 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Active Revenue</p>
                        <p class="text-xl sm:text-2xl font-bold text-purple-900 truncate">GH₵ {{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-3xl sm:text-4xl text-purple-400 flex-shrink-0"></i>
                </div>
            </x-card>
        </div>

        {{-- Analytics Chart --}}
        <section class="space-y-4 sm:space-y-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Platform Analytics</h2>
            <x-card class="bg-white p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Platform Growth - {{ now()->year }}</h3>
                <div class="w-full overflow-x-auto -mx-2 sm:mx-0">
                    <div class="min-w-[500px] px-2 sm:px-0">
                        <canvas id="growthChart" height="80"></canvas>
                    </div>
                </div>
            </x-card>
        </section>

        {{-- Management Sections --}}
        <section class="space-y-4 sm:space-y-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Management & Activity</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                {{-- Pending Verifications --}}
                <x-card class="bg-white p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-user-check text-amber-600 mr-2"></i>
                            Pending Verifications
                        </h3>
                        <a href="{{ route('admin.verifications.index') }}" class="text-sm text-teal-600 hover:text-teal-800 font-medium flex items-center">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="space-y-2">
                        @forelse($pendingVerifications as $request)
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $request->vendor->business_name }}</p>
                                    <p class="text-xs sm:text-sm text-gray-600">
                                        <i class="fas fa-clock text-gray-400 mr-1"></i>
                                        {{ $request->submitted_at->diffForHumans() }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.verifications.index') }}" class="text-teal-600 hover:text-teal-800 text-sm font-medium whitespace-nowrap">
                                    Review <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-check-double text-gray-300 text-4xl sm:text-5xl mb-3"></i>
                                <p class="text-sm text-gray-500">No pending verifications</p>
                            </div>
                        @endforelse
                    </div>
                </x-card>

                {{-- Top Rated Vendors --}}
                <x-card class="bg-white p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-star text-yellow-600 mr-2"></i>
                        Top Rated Vendors
                    </h3>
                    <div class="space-y-2">
                        @forelse($topRatedVendors as $vendor)
                            <div class="flex justify-between items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $vendor->business_name }}</p>
                                    <div class="flex items-center mt-1">
                                        <i class="fas fa-star text-yellow-500 text-xs sm:text-sm"></i>
                                        <span class="text-xs sm:text-sm text-gray-600 ml-1">{{ number_format($vendor->rating_cached, 1) }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 whitespace-nowrap">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-star-half-alt text-gray-300 text-4xl sm:text-5xl mb-3"></i>
                                <p class="text-sm text-gray-500">No rated vendors yet</p>
                            </div>
                        @endforelse
                    </div>
                </x-card>
        </div>
        </section>

        {{-- Quick Actions --}}
        <section class="space-y-4 sm:space-y-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Quick Actions</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                {{-- Verify Vendors --}}
                <a href="{{ route('admin.verifications.index') }}" class="flex flex-col items-center justify-center p-4 sm:p-5 bg-white rounded-lg hover:shadow-md transition border border-gray-200 min-h-[100px]">
                    <i class="fas fa-user-check text-2xl sm:text-3xl text-teal-600 mb-2"></i>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center">Verify Vendors</span>
                </a>

                {{-- Manage Clients --}}
                <a href="{{ route('admin.clients.index') }}" class="flex flex-col items-center justify-center p-4 sm:p-5 bg-white rounded-lg hover:shadow-md transition border border-gray-200 min-h-[100px]">
                    <i class="fas fa-users text-2xl sm:text-3xl text-blue-600 mb-2"></i>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center">Manage Clients</span>
                </a>

                {{-- View Reports --}}
                <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center justify-center p-4 sm:p-5 bg-white rounded-lg hover:shadow-md transition border border-gray-200 min-h-[100px]">
                    <i class="fas fa-flag text-2xl sm:text-3xl text-red-600 mb-2"></i>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center">View Reports</span>
                </a>

                {{-- Browse Vendors --}}
                <a href="{{ route('vendors.index') }}" class="flex flex-col items-center justify-center p-4 sm:p-5 bg-white rounded-lg hover:shadow-md transition border border-gray-200 min-h-[100px]">
                    <i class="fas fa-search text-2xl sm:text-3xl text-gray-600 mb-2"></i>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center">Browse Vendors</span>
                </a>

                {{-- All Vendors --}}
                <a href="{{ route('vendors.index') }}" class="flex flex-col items-center justify-center p-4 sm:p-5 bg-white rounded-lg hover:shadow-md transition border border-gray-200 min-h-[100px]">
                    <i class="fas fa-store-alt text-2xl sm:text-3xl text-purple-600 mb-2"></i>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center">All Vendors</span>
                </a>

                {{-- Public Site --}}
                <a href="{{ route('home') }}" class="flex flex-col items-center justify-center p-4 sm:p-5 bg-white rounded-lg hover:shadow-md transition border border-gray-200 min-h-[100px]">
                    <i class="fas fa-home text-2xl sm:text-3xl text-indigo-600 mb-2"></i>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center">Public Site</span>
                </a>
            </div>
        </section>

        {{-- Recent Activity Stats --}}
        <section class="space-y-4 sm:space-y-6 pb-6 sm:pb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Platform Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                {{-- Services & Content --}}
                <x-card class="bg-white p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cubes text-indigo-600 mr-2"></i>
                        Platform Content
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Total Services</span>
                            <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ $stats['total_vendors'] * 3 }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Active Vendors</span>
                            <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ $stats['verified_vendors'] }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Active Clients</span>
                            <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ $stats['total_clients'] }}</span>
                        </div>
                    </div>
                </x-card>

                {{-- Verification Status --}}
                <x-card class="bg-white p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tasks text-emerald-600 mr-2"></i>
                        Verification Status
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Verified</span>
                            <span class="font-semibold text-emerald-600 text-sm sm:text-base">{{ $stats['verified_vendors'] }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Pending</span>
                            <span class="font-semibold text-amber-600 text-sm sm:text-base">{{ $stats['pending_verifications'] }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Unverified</span>
                            <span class="font-semibold text-gray-600 text-sm sm:text-base">{{ $stats['total_vendors'] - $stats['verified_vendors'] }}</span>
                        </div>
                    </div>
                </x-card>

                {{-- Revenue Stats --}}
                <x-card class="bg-white p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                        Revenue Overview
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Total Revenue</span>
                            <span class="font-semibold text-gray-900 text-sm sm:text-base">GH₵ {{ number_format($stats['total_revenue'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Active Subscriptions</span>
                            <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ $stats['verified_vendors'] }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-600">Avg per Vendor</span>
                            <span class="font-semibold text-gray-900 text-sm sm:text-base">GH₵ {{ $stats['verified_vendors'] > 0 ? number_format($stats['total_revenue'] / $stats['verified_vendors'], 2) : '0.00' }}</span>
                        </div>
                    </div>
                </x-card>
            </div>
        </section>
        </div>
    </div>

    {{-- Chart.js Script --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script id="chart-data" type="application/json">
        {
            "vendors": @json(array_values($monthlyStats['vendors'] ?? [])),
            "clients": @json(array_values($monthlyStats['clients'] ?? []))
        }
    </script>
    <script>
        const chartDataElement = document.getElementById('chart-data');
        const chartData = JSON.parse(chartDataElement.textContent);
        const vendorsData = chartData.vendors;
        const clientsData = chartData.clients;
        
        const ctx = document.getElementById('growthChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Vendors',
                        data: vendorsData,
                        borderColor: 'rgb(20, 184, 166)',
                        backgroundColor: 'rgba(20, 184, 166, 0.1)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'Clients',
                        data: clientsData,
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
</x-admin-layout>
