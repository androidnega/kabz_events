<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <x-card class="p-6">
            <h2 class="text-2xl font-bold text-purple-700 mb-6">📍 Bulk Import Ghana Locations</h2>
            
            <p class="text-gray-600 mb-6">
                Upload a CSV file with columns: <strong class="text-purple-700">region, district, town</strong>
            </p>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Current Statistics --}}
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
                    <p class="text-sm text-gray-600">Regions</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total_regions'] }}</p>
                </div>
                <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded">
                    <p class="text-sm text-gray-600">Districts</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['total_districts'] }}</p>
                </div>
                <div class="bg-purple-50 border-l-4 border-purple-600 p-4 rounded">
                    <p class="text-sm text-gray-600">Towns</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $stats['total_towns'] }}</p>
                </div>
            </div>

            {{-- Upload Form --}}
            <form action="{{ route('superadmin.locations.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Select CSV File *
                    </label>
                    <input 
                        type="file" 
                        name="csv_file" 
                        id="csv_file"
                        accept=".csv,.txt"
                        required
                        class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                               file:mr-4 file:py-3 file:px-6
                               file:rounded-lg file:border-0
                               file:text-sm file:font-semibold
                               file:bg-purple-50 file:text-purple-700
                               hover:file:bg-purple-100"
                    />
                    <p class="mt-2 text-sm text-gray-500">
                        Accepted formats: CSV, TXT (max 2MB)
                    </p>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('superadmin.locations.index') }}" class="text-gray-600 hover:text-gray-800 underline">
                        ← Back to Locations
                    </a>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                        Import CSV Data
                    </button>
                </div>
            </form>

            {{-- CSV Format Guide --}}
            <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 CSV Format Guide</h3>
                
                <p class="text-sm text-gray-700 mb-3">
                    Your CSV file must have these exact column headers (case-insensitive):
                </p>
                
                <div class="bg-white border border-gray-300 rounded-lg p-4 mb-4">
                    <p class="font-mono text-sm text-gray-800 mb-2"><strong>Required Columns:</strong></p>
                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">region</code> - Region name</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">district</code> - District name</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">town</code> - Town name</li>
                    </ul>
                </div>

                <p class="text-sm font-semibold text-gray-800 mb-2">Example CSV content:</p>
                <pre class="bg-gray-800 text-green-400 p-4 rounded-lg text-xs overflow-x-auto">region,district,town
Greater Accra,Accra Metropolitan,Accra Central
Greater Accra,Accra Metropolitan,Osu
Greater Accra,Tema Metropolitan,Tema
Ashanti,Kumasi Metropolitan,Adum
Ashanti,Obuasi Municipal,Obuasi
Western,Sekondi-Takoradi,Sekondi
Central,Cape Coast Metropolitan,Cape Coast
Northern,Tamale Metropolitan,Tamale
Eastern,Koforidua,Koforidua
Volta,Ho Municipal,Ho
Upper East,Bolgatanga Municipal,Bolgatanga
Upper West,Wa Municipal,Wa
Bono,Sunyani Municipal,Sunyani</pre>
            </div>

            {{-- Important Notes --}}
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-yellow-900 mb-2">⚠️ Important Notes</h4>
                <ul class="text-sm text-yellow-800 space-y-1">
                    <li>• Duplicate entries will be automatically skipped</li>
                    <li>• Slugs are auto-generated from names</li>
                    <li>• Relationships are created automatically</li>
                    <li>• Maximum file size: 2MB</li>
                    <li>• Empty rows are automatically skipped</li>
                </ul>
            </div>
        </x-card>
    </div>
</x-app-layout>

