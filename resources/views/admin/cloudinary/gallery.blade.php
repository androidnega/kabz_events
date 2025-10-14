<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.media.index') }}" class="text-purple-600 hover:text-purple-700 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Media Management
                </a>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $folderDisplayName }}
                </h1>
                <p class="mt-2 text-gray-600">{{ $totalCount }} files total</p>
            </div>
        </div>

        {{-- Search and Sort Bar --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" action="{{ route('admin.media.gallery', $folder) }}" class="flex flex-col md:flex-row gap-4">
                {{-- Search --}}
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Search by filename..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                {{-- Sort --}}
                <div class="w-full md:w-64">
                    <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                        <option value="created_desc" {{ $sortBy == 'created_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_asc" {{ $sortBy == 'created_asc' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ $sortBy == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ $sortBy == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    </select>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-filter mr-2"></i> Apply
                </button>

                @if($search)
                    <a href="{{ route('admin.media.gallery', $folder) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Media Grid --}}
        @if($media->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($media as $item)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 media-item">
                        {{-- Image/Video Preview --}}
                        <div class="aspect-square bg-gray-100 relative group">
                            @if($item['resource_type'] === 'video')
                                <video src="{{ $item['url'] }}" class="w-full h-full object-cover" controls></video>
                                <div class="absolute top-2 left-2 px-2 py-1 bg-purple-600 text-white text-xs rounded">
                                    <i class="fas fa-video mr-1"></i> Video
                                </div>
                            @else
                                <img src="{{ $item['url'] }}" alt="{{ basename($item['public_id']) }}" 
                                     class="w-full h-full object-cover" loading="lazy">
                            @endif

                            {{-- Overlay Actions --}}
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-2">
                                <a href="{{ $item['url'] }}" target="_blank" 
                                   class="p-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="deleteMedia('{{ $item['public_id'] }}', '{{ basename($item['public_id']) }}', '{{ $folder }}')" 
                                        class="p-3 bg-red-600 text-white rounded-full hover:bg-red-700 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Media Info --}}
                        <div class="p-4">
                            {{-- Owner Info --}}
                            @if($item['owner'])
                                <div class="mb-2 flex items-center text-sm">
                                    <i class="fas fa-user text-purple-600 mr-2"></i>
                                    <span class="text-gray-700 font-medium truncate">{{ $item['owner']['name'] }}</span>
                                </div>
                            @endif

                            {{-- Filename --}}
                            <p class="text-sm text-gray-600 truncate mb-2" title="{{ basename($item['public_id']) }}">
                                {{ basename($item['public_id']) }}
                            </p>

                            {{-- Stats --}}
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>{{ number_format($item['bytes'] / 1024, 1) }} KB</span>
                                <span>{{ date('M d, Y', strtotime($item['created_at'])) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-lg shadow-md">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl text-gray-600">No media files found</p>
                @if($search)
                    <p class="text-gray-500 mt-2">Try adjusting your search</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                Delete Media
            </h3>
            
            <p class="text-gray-600 mb-4">
                You are about to delete: <strong id="deleteFileName"></strong>
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for deletion <span class="text-red-500">*</span>
                </label>
                <textarea id="deleteReason" rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                          placeholder="E.g., Violates terms of service, inappropriate content, copyright infringement..."></textarea>
                <p class="text-xs text-gray-500 mt-1">The user will receive this reason via notification and email</p>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                <p class="text-sm text-yellow-700">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    This action cannot be undone. The file will be permanently deleted from Cloudinary and removed from all vendor/user accounts.
                </p>
            </div>

            <div class="flex gap-4">
                <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentPublicId = '';
        let currentFolder = '';

        function deleteMedia(publicId, fileName, folder) {
            currentPublicId = publicId;
            currentFolder = folder;
            document.getElementById('deleteFileName').textContent = fileName;
            document.getElementById('deleteReason').value = '';
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        function confirmDelete() {
            const reason = document.getElementById('deleteReason').value.trim();
            
            if (reason.length < 10) {
                alert('Please provide a detailed reason (minimum 10 characters)');
                return;
            }

            // Disable button and show loading
            const deleteBtn = event.target;
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Deleting...';

            fetch('{{ route('admin.media.destroy') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    public_id: currentPublicId,
                    reason: reason,
                    folder: currentFolder
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeleteModal();
                    // Reload page to show updated gallery
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = '<i class="fas fa-trash mr-2"></i> Delete';
                }
            })
            .catch(error => {
                alert('An error occurred while deleting the media');
                console.error('Error:', error);
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash mr-2"></i> Delete';
            });
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });

        // Close modal on clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDeleteModal();
            }
        });
    </script>
    @endpush
</x-app-layout>

