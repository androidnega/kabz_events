<x-admin-layout>
    <x-slot name="pageTitle">{{ $folderDisplayName }}</x-slot>

    <div class="p-4 sm:p-6 lg:p-8">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('admin.media.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 text-sm font-medium mb-3 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Media Folders
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $folderDisplayName }}</h1>
                    <p class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-images mr-2 text-purple-500"></i>
                        <span class="font-semibold">{{ $totalCount }}</span>
                        <span class="ml-1">{{ $totalCount === 1 ? 'file' : 'files' }} total</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Search and Filter Bar - Enhanced Styling --}}
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200 shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('admin.media.gallery', $folder) }}" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4">
                    {{-- Search Input --}}
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-1"></i> Search Files
                        </label>
                        <input type="text" 
                               id="search"
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Search by filename..." 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent text-sm shadow-sm transition">
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="md:w-64">
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sort mr-1"></i> Sort By
                        </label>
                        <select name="sort" 
                                id="sort"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent text-sm shadow-sm transition bg-white">
                            <option value="created_desc" {{ $sortBy == 'created_desc' ? 'selected' : '' }}>Newest First</option>
                            <option value="created_asc" {{ $sortBy == 'created_asc' ? 'selected' : '' }}>Oldest First</option>
                            <option value="name_asc" {{ $sortBy == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                            <option value="name_desc" {{ $sortBy == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-sm font-medium">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>

                    @if($search || $sortBy !== 'created_desc')
                        <a href="{{ route('admin.media.gallery', $folder) }}" 
                           class="px-6 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm font-medium">
                            <i class="fas fa-times mr-2"></i> Clear All
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Media Grid --}}
        @if($media->count() > 0)
            <div id="mediaGrid" 
                 class="grid grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3"
                 data-has-more="{{ $media->count() >= 24 ? '1' : '0' }}">
                @foreach($media as $item)
                    <div class="media-item bg-white rounded border border-gray-200 overflow-hidden hover:border-purple-400 transition-all duration-200">
                        {{-- Image/Video Preview --}}
                        <div class="h-40 bg-gray-100 relative group" 
                             data-url="{{ $item['url'] }}" 
                             data-title="{{ basename($item['public_id']) }}" 
                             data-type="{{ $item['resource_type'] }}">
                            @if($item['resource_type'] === 'video')
                                <div class="w-full h-full flex items-center justify-center bg-gray-800">
                                    <i class="fas fa-play-circle text-white text-3xl"></i>
                                </div>
                            @else
                                <img src="{{ $item['url'] }}" 
                                     alt="{{ basename($item['public_id']) }}" 
                                     class="w-full h-full object-cover" 
                                     loading="lazy">
                            @endif

                            {{-- Icon Actions on Hover - More Visible --}}
                            <div class="absolute inset-0 bg-black bg-opacity-70 hidden group-hover:flex items-center justify-center gap-6">
                                <i class="view-btn fas fa-eye text-white text-3xl cursor-pointer hover:scale-110 hover:text-blue-400 transition-all" 
                                   data-url="{{ $item['url'] }}" 
                                   data-title="{{ basename($item['public_id']) }}" 
                                   data-type="{{ $item['resource_type'] }}"
                                   title="View"></i>
                                <i class="download-btn fas fa-download text-white text-3xl cursor-pointer hover:scale-110 hover:text-green-400 transition-all" 
                                   data-url="{{ $item['url'] }}" 
                                   data-filename="{{ basename($item['public_id']) }}.{{ $item['format'] }}" 
                                   title="Download"></i>
                                <i class="delete-btn fas fa-trash text-white text-3xl cursor-pointer hover:scale-110 hover:text-red-400 transition-all" 
                                   data-public-id="{{ $item['public_id'] }}" 
                                   data-filename="{{ basename($item['public_id']) }}" 
                                   data-folder="{{ $folder }}" 
                                   title="Delete"></i>
                            </div>
                        </div>

                        {{-- Media Info --}}
                        <div class="p-2 bg-gray-50">
                            @if($item['owner'])
                                <p class="text-xs text-gray-600 truncate font-medium" title="{{ $item['owner']['name'] }}">
                                    <i class="fas fa-user text-purple-500 mr-1"></i>{{ Str::limit($item['owner']['name'], 12) }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ number_format($item['bytes'] / 1024, 0) }}KB</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Loading Indicator for Infinite Scroll --}}
            <div id="loadingIndicator" class="text-center py-12 hidden">
                <i class="fas fa-spinner fa-spin text-4xl text-purple-600 mb-3"></i>
                <p class="text-gray-600 font-medium">Loading more images...</p>
            </div>

            {{-- End of Results Message --}}
            <div id="endMessage" class="text-center py-8 hidden">
                <p class="text-gray-500">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    You've reached the end of the gallery
                </p>
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-xl border-2 border-dashed border-gray-300">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl font-semibold text-gray-600 mb-2">No media files found</p>
                <p class="text-sm text-gray-500">
                    @if($search)
                        Try adjusting your search criteria
                    @else
                        This folder is currently empty
                    @endif
                </p>
            </div>
        @endif
    </div>

    {{-- Enhanced Lightbox Modal --}}
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-95 hidden items-center justify-center z-50">
        {{-- Close Button --}}
        <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white text-4xl hover:text-gray-300 transition z-10">
            <i class="fas fa-times"></i>
        </button>

        {{-- Navigation Buttons --}}
        <button onclick="navigateLightbox(-1)" class="absolute left-6 top-1/2 -translate-y-1/2 text-white text-4xl hover:text-gray-300 transition z-10">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button onclick="navigateLightbox(1)" class="absolute right-6 top-1/2 -translate-y-1/2 text-white text-4xl hover:text-gray-300 transition z-10">
            <i class="fas fa-chevron-right"></i>
        </button>

        {{-- Media Container --}}
        <div class="max-w-7xl max-h-screen w-full p-8 flex flex-col items-center justify-center" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" alt="" class="max-w-full max-h-[85vh] object-contain mx-auto rounded-lg shadow-2xl">
            <video id="lightboxVideo" src="" controls class="max-w-full max-h-[85vh] mx-auto rounded-lg shadow-2xl hidden"></video>
            <div class="mt-4 text-center">
                <p id="lightboxTitle" class="text-white text-lg font-medium"></p>
                <p id="lightboxInfo" class="text-gray-400 text-sm mt-1"></p>
            </div>
        </div>
    </div>

    {{-- Enhanced Delete Modal --}}
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-lg w-full mx-4 shadow-2xl" onclick="event.stopPropagation()">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">Delete Media</h3>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <p class="text-sm text-gray-600 mb-1">Deleting file:</p>
                <p class="text-base font-semibold text-gray-900" id="deleteFileName"></p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    Reason for deletion <span class="text-red-500">*</span>
                </label>
                <textarea id="deleteReason" 
                          rows="4" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent text-sm"
                          placeholder="E.g., Violates terms, inappropriate content, user request..."></textarea>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    User will be notified via bell notification and email
                </p>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800 mb-1">Warning: Permanent Action</p>
                        <p class="text-xs text-yellow-700">This will permanently delete the file from Cloudinary and remove all references from user accounts.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="confirmDelete()" class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold shadow-sm">
                    <i class="fas fa-trash mr-2"></i> Delete Permanently
                </button>
                <button onclick="closeDeleteModal()" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
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
        let currentMediaIndex = 0;
        let mediaItems = [];

        // Initialize media items array
        document.addEventListener('DOMContentLoaded', function() {
            updateMediaItemsArray();
        });

        function updateMediaItemsArray() {
            mediaItems = Array.from(document.querySelectorAll('.view-media-trigger')).map(el => ({
                url: el.dataset.url,
                title: el.dataset.title,
                type: el.dataset.type
            }));
        }

        // View media in lightbox
        function viewMedia(url, title, type, index = 0) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightboxImage');
            const video = document.getElementById('lightboxVideo');
            const titleEl = document.getElementById('lightboxTitle');
            const infoEl = document.getElementById('lightboxInfo');

            currentMediaIndex = index;

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
            infoEl.textContent = `${currentMediaIndex + 1} of ${mediaItems.length}`;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            const video = document.getElementById('lightboxVideo');
            
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            video.pause();
            video.src = '';
            
            // Restore body scroll
            document.body.style.overflow = '';
        }

        function navigateLightbox(direction) {
            if (mediaItems.length === 0) return;
            
            currentMediaIndex += direction;
            if (currentMediaIndex < 0) currentMediaIndex = mediaItems.length - 1;
            if (currentMediaIndex >= mediaItems.length) currentMediaIndex = 0;
            
            const item = mediaItems[currentMediaIndex];
            viewMedia(item.url, item.title, item.type, currentMediaIndex);
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
                alert('⚠️ Please provide a detailed reason (minimum 10 characters)');
                return;
            }

            const deleteBtn = event.target;
            const originalContent = deleteBtn.innerHTML;
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Deleting...';

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
                    // Show success message
                    alert('✅ Media deleted successfully');
                    window.location.reload();
                } else {
                    alert('❌ Error: ' + data.message);
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = originalContent;
                }
            })
            .catch(error => {
                alert('❌ An error occurred while deleting the media');
                console.error('Error:', error);
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalContent;
            });
        }

        // Event delegation for media items
        document.addEventListener('click', function(e) {
            // View icon - Opens lightbox
            const viewBtn = e.target.closest('.view-btn');
            if (viewBtn) {
                e.stopPropagation();
                e.preventDefault();
                const url = viewBtn.getAttribute('data-url');
                const title = viewBtn.getAttribute('data-title');
                const type = viewBtn.getAttribute('data-type');
                const allViewBtns = Array.from(document.querySelectorAll('.view-btn'));
                const index = allViewBtns.indexOf(viewBtn);
                viewMedia(url, title, type, index);
                return;
            }

            // Download icon
            const downloadBtn = e.target.closest('.download-btn');
            if (downloadBtn) {
                e.stopPropagation();
                e.preventDefault();
                const url = downloadBtn.getAttribute('data-url');
                const filename = downloadBtn.getAttribute('data-filename');
                downloadMedia(url, filename);
                return;
            }

            // Delete icon
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.stopPropagation();
                e.preventDefault();
                const publicId = deleteBtn.getAttribute('data-public-id');
                const filename = deleteBtn.getAttribute('data-filename');
                const folder = deleteBtn.getAttribute('data-folder');
                deleteMedia(publicId, filename, folder);
                return;
            }

            // Close lightbox when clicking outside
            if (e.target.id === 'lightbox') {
                closeLightbox();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(event) {
            const lightbox = document.getElementById('lightbox');
            const isLightboxOpen = !lightbox.classList.contains('hidden');
            
            if (event.key === 'Escape') {
                closeLightbox();
                closeDeleteModal();
            } else if (isLightboxOpen && event.key === 'ArrowLeft') {
                navigateLightbox(-1);
            } else if (isLightboxOpen && event.key === 'ArrowRight') {
                navigateLightbox(1);
            }
        });

        // Infinite Scroll disabled - All images load at once (max 500 per folder)
    </script>
    @endpush
</x-admin-layout>
