<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-cloud text-blue-600 mr-3"></i>
                Cloudinary Media Management
            </h1>
            <p class="mt-2 text-gray-600">Manage all media files stored in Cloudinary CDN</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Folders Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($folders as $folder)
                <a href="{{ route('admin.media.gallery', $folder['name']) }}" 
                   class="block bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6">
                        {{-- Icon --}}
                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-purple-100 to-blue-100 mb-4">
                            <i class="fas {{ $folder['icon'] }} text-3xl text-purple-600"></i>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $folder['display_name'] }}
                        </h3>

                        {{-- Description --}}
                        <p class="text-sm text-gray-600 mb-4">
                            {{ $folder['description'] }}
                        </p>

                        {{-- Stats --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <span class="text-sm text-gray-500">Total Files:</span>
                            <span class="text-lg font-bold text-purple-600">{{ $folder['count'] }}</span>
                        </div>
                    </div>

                    {{-- Hover Effect --}}
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 h-1 rounded-b-lg"></div>
                </a>
            @endforeach
        </div>

        {{-- Quick Stats --}}
        <div class="mt-12 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                Cloudinary Statistics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-purple-600">
                        {{ collect($folders)->sum('count') }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">Total Media Files</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-blue-600">
                        {{ count($folders) }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">Active Folders</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600">
                        <i class="fas fa-check-circle"></i>
                    </p>
                    <p class="text-sm text-gray-600 mt-1">CDN Active</p>
                </div>
                <div class="text-center">
                    <a href="https://cloudinary.com/console/media_library" target="_blank" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        View Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

