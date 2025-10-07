<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <x-card class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-blue-700">Database Backups 🇬🇭</h2>
                <form action="{{ route('superadmin.backups.create') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        🗄️ Create New Backup
                    </button>
                </form>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{-- Backup Statistics --}}
            <div class="grid grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
                    <p class="text-sm text-gray-600">Total Backups</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total_backups'] }}</p>
                </div>
                <div class="bg-purple-50 border-l-4 border-purple-600 p-4 rounded">
                    <p class="text-sm text-gray-600">Total Storage</p>
                    <p class="text-2xl font-bold text-purple-900">
                        {{ $stats['total_size'] >= 1048576 ? number_format($stats['total_size'] / 1048576, 2) . ' MB' : number_format($stats['total_size'] / 1024, 2) . ' KB' }}
                    </p>
                </div>
                <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded">
                    <p class="text-sm text-gray-600">Latest Backup</p>
                    <p class="text-lg font-bold text-green-900">
                        {{ $stats['latest_backup'] ? $stats['latest_backup']->created_at->diffForHumans() : 'No backups yet' }}
                    </p>
                </div>
            </div>

            @if($backups->isEmpty())
                <div class="text-center py-16">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No backups yet</h3>
                    <p class="text-gray-600 mb-6">Create your first database backup to secure your data</p>
                    <form action="{{ route('superadmin.backups.create') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                            Create First Backup
                        </button>
                    </form>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($backups as $backup)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $backup->file_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($backup->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $backup->file_size }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $backup->created_at->format('d M Y, h:i A') }}</div>
                                        <div class="text-xs text-gray-500">{{ $backup->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-3">
                                            <a href="{{ route('superadmin.backups.download', $backup->id) }}" 
                                               class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                                ⬇️ Download
                                            </a>
                                            <form action="{{ route('superadmin.backups.destroy', $backup->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        onclick="return confirm('Delete this backup?')"
                                                        class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($backups->hasPages())
                    <div class="mt-6">
                        {{ $backups->links() }}
                    </div>
                @endif
            @endif

            {{-- Info Section --}}
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">ℹ️ Backup Information</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>✓ Backups are created using MySQL dump</li>
                    <li>✓ Automatic cleanup after 7 days (configurable)</li>
                    <li>✓ Backups stored in: <code class="bg-blue-100 px-2 py-1 rounded">storage/app/backups/</code></li>
                    <li>✓ Recommended: Schedule daily backups via cron</li>
                    <li>✓ For production: Consider spatie/laravel-backup package</li>
                </ul>
            </div>
        </x-card>
    </div>
</x-app-layout>

