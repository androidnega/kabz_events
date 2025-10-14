<x-admin-layout>
    <x-slot name="pageTitle">Media Management</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-cloud text-blue-600 mr-2"></i>
                Cloudinary Media Management
            </h1>
            <p class="mt-1 text-sm text-gray-600">Manage all media files stored in Cloudinary CDN</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-3 rounded">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-3 rounded">
                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {{-- Total Files --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Total Files</p>
                        <p class="text-2xl font-bold text-gray-900">{{ collect($folders)->sum('count') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-images text-purple-600"></i>
                    </div>
                </div>
            </div>

            {{-- Profile Photos --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Profiles</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $folders[1]['count'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-circle text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Sample Work --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Sample Work</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $folders[0]['count'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-images text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Videos --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Videos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $folders[2]['count'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-video text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Folders Grid - Compact Design --}}
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-folder-open text-gray-600 mr-2"></i>
            Media Folders
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($folders as $folder)
                <a href="{{ route('admin.media.gallery', $folder['name']) }}" 
                   class="bg-white rounded-lg transition-all duration-200 p-4 border border-gray-200 hover:border-purple-400 group">
                    
                    {{-- Icon --}}
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-br 
                        @if($loop->index == 0) from-green-100 to-green-200
                        @elseif($loop->index == 1) from-blue-100 to-blue-200
                        @elseif($loop->index == 2) from-red-100 to-red-200
                        @else from-yellow-100 to-yellow-200
                        @endif mb-3">
                        <i class="fas {{ $folder['icon'] }} text-2xl 
                            @if($loop->index == 0) text-green-600
                            @elseif($loop->index == 1) text-blue-600
                            @elseif($loop->index == 2) text-red-600
                            @else text-yellow-600
                            @endif"></i>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-sm font-semibold text-gray-900 mb-1 group-hover:text-purple-600 transition">
                        {{ $folder['display_name'] }}
                    </h3>

                    {{-- Count --}}
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-500">Files:</span>
                        <span class="text-sm font-bold 
                            @if($loop->index == 0) text-green-600
                            @elseif($loop->index == 1) text-blue-600
                            @elseif($loop->index == 2) text-red-600
                            @else text-yellow-600
                            @endif">{{ $folder['count'] }}</span>
                    </div>

                    {{-- Arrow --}}
                    <div class="mt-2 flex justify-end">
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600 transition"></i>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Quick Actions --}}
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                <div class="flex-1">
                    <h4 class="font-semibold text-blue-900 mb-1">Cloudinary Management Tips</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Click any folder to view and manage its contents</li>
                        <li>• Use search to find specific images by filename</li>
                        <li>• Hover over images to view or delete them</li>
                        <li>• Deleted media triggers automatic user notifications and emails</li>
                        <li>• All images are delivered via fast CDN worldwide</li>
                    </ul>
                    <div class="mt-3">
                        <a href="https://cloudinary.com/console/media_library" target="_blank" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 font-medium">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View in Cloudinary Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
