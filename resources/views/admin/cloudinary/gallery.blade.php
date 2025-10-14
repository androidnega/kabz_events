<x-admin-layout>
    <x-slot name="pageTitle">{{ $folderDisplayName }}</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.media.index') }}" class="text-purple-600 hover:text-purple-700 text-sm inline-block mb-1">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $folderDisplayName }}</h1>
                <p class="text-sm text-gray-600">{{ $totalCount }} files total</p>
            </div>
        </div>

        {{-- Search and Sort Bar --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('admin.media.gallery', $folder) }}" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Search by filename..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent text-sm">
                </div>

                <select name="sort" class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 text-sm">
                    <option value="created_desc" {{ $sortBy == 'created_desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="created_asc" {{ $sortBy == 'created_asc' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name_asc" {{ $sortBy == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name_desc" {{ $sortBy == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>

                @if($search)
                    <a href="{{ route('admin.media.gallery', $folder) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Media Grid - Compact with Infinite Scroll --}}
        @if($media->count() > 0)
            <div id="mediaGrid" 
                 class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-3"
                 data-has-more="{{ $media->count() >= 24 ? '1' : '0' }}">
                @foreach($media as $item)
                    <div class="media-item bg-white rounded border border-gray-200 overflow-hidden hover:border-purple-400 transition">
                        {{-- Image/Video Preview --}}
                        <div class="aspect-square bg-gray-50 relative group cursor-pointer view-media-trigger" 
                             data-url="{{ $item['url'] }}" 
                             data-title="{{ basename($item['public_id']) }}" 
                             data-type="{{ $item['resource_type'] }}">
                            @if($item['resource_type'] === 'video')
                                <div class="w-full h-full flex items-center justify-center bg-gray-800">
                                    <i class="fas fa-play-circle text-white text-4xl"></i>
                                </div>
                                <div class="absolute top-1 left-1 px-1.5 py-0.5 bg-red-600 text-white text-xs rounded">
                                    VIDEO
                                </div>
                            @else
                                <img src="{{ $item['url'] }}" alt="{{ basename($item['public_id']) }}" 
                                     class="w-full h-full object-cover" loading="lazy">
                            @endif

                            {{-- Hover Actions --}}
                            <div class="absolute inset-0 bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1">
                                <button class="download-btn p-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-xs" 
                                        data-url="{{ $item['url'] }}" 
                                        data-filename="{{ basename($item['public_id']) }}" 
                                        title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="delete-btn p-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-xs" 
                                        data-public-id="{{ $item['public_id'] }}" 
                                        data-filename="{{ basename($item['public_id']) }}" 
                                        data-folder="{{ $folder }}" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="p-2">
                            @if($item['owner'])
                                <p class="text-xs text-gray-600 truncate mb-1" title="{{ $item['owner']['name'] }}">
                                    <i class="fas fa-user text-purple-500 mr-1"></i>{{ $item['owner']['name'] }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-500 truncate">{{ number_format($item['bytes'] / 1024, 0) }} KB</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Loading Indicator for Infinite Scroll --}}
            <div id="loadingIndicator" class="text-center py-8 hidden">
                <i class="fas fa-spinner fa-spin text-3xl text-purple-600"></i>
                <p class="text-gray-600 mt-2">Loading more images...</p>
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-lg border border-gray-200">
                <i class="fas fa-images text-5xl text-gray-300 mb-3"></i>
                <p class="text-lg text-gray-600">No media files found</p>
            </div>
        @endif
    </div>

    {{-- Lightbox Modal --}}
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50" onclick="closeLightbox()">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
        <div class="max-w-7xl max-h-screen p-4" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" alt="" class="max-w-full max-h-screen object-contain mx-auto">
            <video id="lightboxVideo" src="" controls class="max-w-full max-h-screen mx-auto hidden"></video>
            <p id="lightboxTitle" class="text-white text-center mt-4"></p>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" onclick="event.stopPropagation()">
            <h3 class="text-lg font-bold text-gray-900 mb-3">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                Delete Media
            </h3>
            
            <p class="text-sm text-gray-600 mb-3">
                Deleting: <strong id="deleteFileName"></strong>
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Reason for deletion <span class="text-red-500">*</span>
                </label>
                <textarea id="deleteReason" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 text-sm"
                          placeholder="E.g., Violates terms, inappropriate content..."></textarea>
                <p class="text-xs text-gray-500 mt-1">User will be notified via bell + email</p>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4">
                <p class="text-xs text-yellow-700">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Permanent deletion. File removed from Cloudinary and all user accounts.
                </p>
            </div>

            <div class="flex gap-3">
                <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const deleteMediaUrl = '{{ route("admin.media.destroy") }}';
        const csrfToken = '{{ csrf_token() }}';
        let currentPublicId = '';
        let currentFolder = '';

        // View media in lightbox
        function viewMedia(url, title, type) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightboxImage');
            const video = document.getElementById('lightboxVideo');
            const titleEl = document.getElementById('lightboxTitle');

            if (type === 'video') {
                img.classList.add('hidden');
                video.classList.remove('hidden');
                video.src = url;
            } else {
                video.classList.add('hidden');
                img.classList.remove('hidden');
                img.src = url;
            }
            
            titleEl.textContent = title;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.add('hidden');
            document.getElementById('lightbox').classList.remove('flex');
            document.getElementById('lightboxVideo').pause();
        }

        // Download media
        function downloadMedia(url, filename) {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Delete media
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

            const deleteBtn = event.target;
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Deleting...';

            fetch(deleteMediaUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
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
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = '<i class="fas fa-trash mr-1"></i> Delete';
                }
            })
            .catch(error => {
                alert('An error occurred while deleting the media');
                console.error('Error:', error);
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash mr-1"></i> Delete';
            });
        }

        // Event delegation for dynamically loaded media items
        document.addEventListener('click', function(e) {
            // View media trigger
            const viewTrigger = e.target.closest('.view-media-trigger');
            if (viewTrigger && !e.target.closest('.download-btn') && !e.target.closest('.delete-btn')) {
                const url = viewTrigger.dataset.url;
                const title = viewTrigger.dataset.title;
                const type = viewTrigger.dataset.type;
                viewMedia(url, title, type);
                return;
            }

            // Download button
            const downloadBtn = e.target.closest('.download-btn');
            if (downloadBtn) {
                e.stopPropagation();
                const url = downloadBtn.dataset.url;
                const filename = downloadBtn.dataset.filename;
                downloadMedia(url, filename);
                return;
            }

            // Delete button
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.stopPropagation();
                const publicId = deleteBtn.dataset.publicId;
                const filename = deleteBtn.dataset.filename;
                const folder = deleteBtn.dataset.folder;
                deleteMedia(publicId, filename, folder);
                return;
            }
        });

        // Close modals on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLightbox();
                closeDeleteModal();
            }
        });

        // Infinite Scroll
        let page = 1;
        let loading = false;
        const mediaGrid = document.getElementById('mediaGrid');
        let hasMore = mediaGrid ? mediaGrid.dataset.hasMore === '1' : false;

        window.addEventListener('scroll', function() {
            if (loading || !hasMore) return;

            const scrollPosition = window.innerHeight + window.scrollY;
            const pageHeight = document.documentElement.scrollHeight;

            if (scrollPosition >= pageHeight - 500) {
                loadMoreImages();
            }
        });

        function loadMoreImages() {
            if (loading || !hasMore) return;
            
            loading = true;
            document.getElementById('loadingIndicator').classList.remove('hidden');
            
            page++;
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newItems = doc.querySelectorAll('.media-item');
                    
                    if (newItems.length === 0) {
                        hasMore = false;
                    } else {
                        const grid = document.getElementById('mediaGrid');
                        newItems.forEach(item => grid.appendChild(item.cloneNode(true)));
                    }
                    
                    loading = false;
                    document.getElementById('loadingIndicator').classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error loading more images:', error);
                    loading = false;
                    document.getElementById('loadingIndicator').classList.add('hidden');
                });
        }
    </script>
    @endpush
</x-admin-layout>
