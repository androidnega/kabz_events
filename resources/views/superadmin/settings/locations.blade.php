<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-map-marked-alt text-purple-600 mr-2"></i>Location Management
                    </h2>
                    <p class="text-gray-600">Manage regions, districts, and towns across Ghana</p>
                </div>
                <a href="{{ route('superadmin.locations.upload') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-file-upload mr-2"></i>
                    Bulk Import CSV
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Add New Location Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Add New Location
            </h3>
            <form action="{{ route('superadmin.locations.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location Type</label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Select Type</option>
                            <option value="region">Region</option>
                            <option value="district">District</option>
                            <option value="town">Town</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Location name" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Parent (Optional)</label>
                        <input type="number" name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Parent ID">
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-save mr-2"></i>
                    Add Location
                </button>
            </form>
        </div>

        {{-- Locations List --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-list text-indigo-600 mr-2"></i>Ghana Locations
                </h3>
            </div>
            <div class="p-6">
                @forelse($regions as $region)
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-purple-50 border border-purple-200 rounded-lg mb-3">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-purple-600 mr-3"></i>
                                <span class="font-bold text-gray-900">{{ $region->name }}</span>
                                <span class="ml-3 text-xs bg-purple-200 text-purple-800 px-2 py-1 rounded-full">Region</span>
                            </div>
                            <form action="{{ route('superadmin.locations.destroy', $region->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>

                        @foreach($region->districts as $district)
                            <div class="ml-8 mb-3">
                                <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-building text-blue-600 mr-3"></i>
                                        <span class="font-semibold text-gray-800">{{ $district->name }}</span>
                                        <span class="ml-3 text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full">District</span>
                                    </div>
                                    <form action="{{ route('superadmin.locations.destroy', $district->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                @foreach($district->towns as $town)
                                    <div class="ml-8 mb-2">
                                        <div class="flex items-center justify-between p-2 bg-gray-50 border border-gray-200 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-map-pin text-gray-600 mr-3 text-sm"></i>
                                                <span class="text-gray-700">{{ $town->name }}</span>
                                                <span class="ml-3 text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full">Town</span>
                                            </div>
                                            <form action="{{ route('superadmin.locations.destroy', $town->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-map-marked-alt text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500 mb-4">No locations found. Add your first location or import via CSV.</p>
                        <a href="{{ route('superadmin.locations.upload') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
                            <i class="fas fa-file-upload mr-2"></i>
                            Import CSV
                        </a>
                    </div>
                @endforelse

                {{-- Pagination Links --}}
                @if($regions->hasPages())
                    <div class="mt-6 border-t border-gray-200 pt-4">
                        {{ $regions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

