<x-app-layout>
    {{-- Admin Dashboard Header --}}
    <div class="bg-gradient-to-r from-red-600 to-yellow-500 text-white py-8 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold">Admin Dashboard 🇬🇭</h1>
            <p class="text-red-100 mt-1">Vendor & Client Management | Platform Analytics</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Key Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <x-card class="bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-600">
                <p class="text-sm font-medium text-gray-600">Total Vendors</p>
                <p class="text-3xl font-bold text-blue-900">{{ $stats['total_vendors'] }}</p>
            </x-card>

            <x-card class="bg-gradient-to-br from-green-50 to-green-100 border-l-4 border-green-600">
                <p class="text-sm font-medium text-gray-600">Verified Vendors</p>
                <p class="text-3xl font-bold text-green-900">{{ $stats['verified_vendors'] }}</p>
            </x-card>

            <x-card class="bg-gradient-to-br from-yellow-50 to-yellow-100 border-l-4 border-yellow-600">
                <p class="text-sm font-medium text-gray-600">Pending Verifications</p>
                <p class="text-3xl font-bold text-yellow-900">{{ $stats['pending_verifications'] }}</p>
            </x-card>

            <x-card class="bg-gradient-to-br from-teal-50 to-teal-100 border-l-4 border-teal-600">
                <p class="text-sm font-medium text-gray-600">Total Clients</p>
                <p class="text-3xl font-bold text-teal-900">{{ $stats['total_clients'] }}</p>
            </x-card>

            <x-card class="bg-gradient-to-br from-purple-50 to-purple-100 border-l-4 border-purple-600">
                <p class="text-sm font-medium text-gray-600">Active Revenue</p>
                <p class="text-2xl font-bold text-purple-900">GH₵ {{ number_format($stats['total_revenue'], 2) }}</p>
            </x-card>
        </div>

        {{-- Analytics Chart --}}
        <x-card class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Platform Growth Analytics ({{ now()->year }})</h3>
            <canvas id="growthChart" height="80"></canvas>
        </x-card>

        {{-- Recent Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Pending Verifications --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Pending Verifications</h3>
                    <a href="{{ route('admin.verifications.index') }}" class="text-sm text-purple-600 hover:underline">
                        View All →
                    </a>
                </div>
                @forelse($pendingVerifications as $request)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded mb-2">
                        <div>
                            <p class="font-medium">{{ $request->vendor->business_name }}</p>
                            <p class="text-sm text-gray-600">{{ $request->submitted_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.verifications.index') }}" class="text-purple-600 text-sm">Review</a>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No pending verifications</p>
                @endforelse
            </x-card>

            {{-- Top Rated Vendors --}}
            <x-card>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Rated Vendors</h3>
                @forelse($topRatedVendors as $vendor)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded mb-2">
                        <div>
                            <p class="font-medium">{{ $vendor->business_name }}</p>
                            <p class="text-sm text-yellow-600">★ {{ number_format($vendor->rating_cached, 1) }}</p>
                        </div>
                        <span class="text-green-600 text-sm">✓ Verified</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No rated vendors yet</p>
                @endforelse
            </x-card>
        </div>

        {{-- Quick Actions --}}
        <x-card class="bg-purple-50 border-2 border-purple-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <a href="{{ route('admin.verifications.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">✓</span>
                    <span class="text-sm font-medium">Verify Vendors</span>
                </a>
                <a href="{{ route('admin.clients.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">👥</span>
                    <span class="text-sm font-medium">Manage Clients</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">⚠️</span>
                    <span class="text-sm font-medium">View Reports</span>
                </a>
                <a href="{{ route('search.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">🔍</span>
                    <span class="text-sm font-medium">Browse Vendors</span>
                </a>
                <a href="{{ route('vendors.index') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">🏪</span>
                    <span class="text-sm font-medium">All Vendors</span>
                </a>
                <a href="{{ route('home') }}" class="flex flex-col items-center p-4 bg-white rounded-lg hover:shadow-md transition">
                    <span class="text-3xl mb-2">🏠</span>
                    <span class="text-sm font-medium">Public Site</span>
                </a>
            </div>
        </x-card>
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
                        borderColor: 'rgb(234, 179, 8)',
                        backgroundColor: 'rgba(234, 179, 8, 0.1)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'Clients',
                        data: @json(array_values($monthlyStats['clients'])),
                        borderColor: 'rgb(20, 184, 166)',
                        backgroundColor: 'rgba(20, 184, 166, 0.1)',
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

