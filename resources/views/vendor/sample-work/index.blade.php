<x-vendor-layout>
    <x-slot name="title">Sample Work</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Sample Work Gallery</h2>
            <p class="text-sm sm:text-base text-gray-600">Showcase your best work to attract more clients. Your first image will be used as your profile preview.</p>
        </div>

        {{-- Upload Stats & Limits --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Upload Limits</h3>
                    <div class="space-y-1 text-xs sm:text-sm">
                        <p class="text-gray-700">
                            <span class="font-medium">Images:</span>
                            {{ $vendor->sample_work_images ? count($vendor->sample_work_images) : 0 }} / {{ $vendor->getMaxSampleImages() }}
                            @if(!$vendor->is_verified && !$vendor->hasVipSubscription())
                                <span class="text-purple-600">(Free: 5 images max, 1MB each)</span>
                            @else
                                <span class="text-green-600">({{ $vendor->hasVipSubscription() ? 'VIP' : 'Verified' }}: 20 images max)</span>
                            @endif
                        </p>
                        <p class="text-gray-700">
                            <span class="font-medium">Video:</span>
                            @if($vendor->canUploadVideo())
                                @if($vendor->sample_work_video)
                                    <span class="text-green-600">✓ Uploaded (VIP Feature)</span>
                                @else
                                    <span class="text-gray-600">Available (VIP Feature, 30s max, 10MB)</span>
                                @endif
                            @else
                                <span class="text-gray-400">🔒 Upgrade to VIP to unlock video uploads</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                @if(!$vendor->is_verified && !$vendor->hasVipSubscription())
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('vendor.verification') }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-certificate mr-2"></i> Get Verified
                        </a>
                        <a href="{{ route('vendor.subscriptions') }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-purple-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-crown mr-2"></i> Upgrade to VIP
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sample Work Title --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Gallery Title</h3>
            <form action="{{ route('vendor.sample-work.title') }}" method="POST">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <input 
                        type="text" 
                        name="sample_work_title"
                        value="{{ $vendor->sample_work_title }}"
                        placeholder="e.g., Our Recent Events, Portfolio, Sample Work"
                        class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 text-sm sm:text-base"
                    />
                    <button type="submit" class="px-4 sm:px-6 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition text-sm sm:text-base">
                        <i class="fas fa-save mr-2"></i> Save Title
                    </button>
                </div>
            </form>
        </div>

        {{-- Image Upload --}}
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Upload Images</h3>
            
            @if(($vendor->sample_work_images ? count($vendor->sample_work_images) : 0) < $vendor->getMaxSampleImages())
                <div id="uploadSection">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 sm:p-8 text-center hover:border-purple-500 transition">
                        <input 
                            type="file" 
                            id="imageInput"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            multiple
                            class="hidden"
                        />
                        <label for="imageInput" class="cursor-pointer">
                            <div class="text-gray-600 mb-2">
                                <i class="fas fa-cloud-upload-alt text-4xl sm:text-5xl text-purple-500 mb-3"></i>
                            </div>
                            <p class="text-sm sm:text-base font-medium text-gray-700 mb-1">Click to upload or drag and drop</p>
                            <p class="text-xs sm:text-sm text-gray-500">JPEG, JPG, PNG, WEBP (Up to 10MB - auto-compressed)</p>
                            @php
                                $useCloudinary = \App\Services\SettingsService::get('cloud_storage') === 'cloudinary';
                            @endphp
                            @if($useCloudinary)
                                <p class="text-xs sm:text-sm text-blue-600 mt-1"><i class="fas fa-cloud mr-1"></i> Uploaded to Cloudinary with auto-compression</p>
                            @else
                                <p class="text-xs sm:text-sm text-gray-500 mt-1"><i class="fas fa-server mr-1"></i> Stored locally</p>
                            @endif
                            <p class="text-xs sm:text-sm text-purple-600 mt-2">{{ $vendor->getMaxSampleImages() - ($vendor->sample_work_images ? count($vendor->sample_work_images) : 0) }} slots available</p>
                        </label>
                    </div>
                    
                    <div id="uploadProgress" class="hidden mt-4">
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-spinner fa-spin text-purple-600 mr-3"></i>
                                <span class="text-sm text-purple-800">Uploading images...</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        You've reached your upload limit. Delete some images to upload new ones, or upgrade your account for more space.
                    </p>
                </div>
            @endif
        </div>

        {{-- Image Gallery --}}
        @if($vendor->sample_work_images && count($vendor->sample_work_images) > 0)
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Your Gallery ({{ count($vendor->sample_work_images) }} images)</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4" id="imageGallery">
                    @foreach($vendor->sample_work_images as $index => $imageData)
                        @php
                            $imageUrl = $vendor->getImageUrl($imageData);
                            $isPreview = $vendor->preview_image === $imageData || 
                                        (is_array($vendor->preview_image) && is_array($imageData) && 
                                         ($vendor->preview_image['url'] ?? null) === ($imageData['url'] ?? null));
                        @endphp
                        <div class="relative group" data-index="{{ $index }}">
                            <img 
                                src="{{ $imageUrl }}" 
                                alt="Sample work {{ $index + 1 }}"
                                class="w-full h-32 sm:h-48 object-cover rounded-lg border-2 {{ $isPreview ? 'border-purple-500' : 'border-gray-200' }}"
                                loading="lazy"
                            />
                            
                            {{-- Cloud Badge --}}
                            @if(is_array($imageData) && $imageData['type'] === 'cloudinary')
                                <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                    <i class="fas fa-cloud"></i>
                                </div>
                            @endif
                            
                            {{-- Preview Badge --}}
                            @if($isPreview)
                                <div class="absolute top-2 left-2 bg-purple-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                                    <i class="fas fa-star mr-1"></i> Preview
                                </div>
                            @endif
                            
                            {{-- Action Buttons --}}
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <div class="flex gap-2">
                                    @if(!$isPreview)
                                        <button 
                                            onclick="setPreview({{ $index }})"
                                            class="px-2 sm:px-3 py-1.5 sm:py-2 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition"
                                            title="Set as preview"
                                        >
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endif
                                    <button 
                                        onclick="deleteImage({{ $index }})"
                                        class="px-2 sm:px-3 py-1.5 sm:py-2 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition"
                                        title="Delete"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Video Upload (VIP Only) --}}
        @if($vendor->canUploadVideo())
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-crown text-yellow-500 mr-2"></i> Video Upload (VIP Feature)
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Show off your work with a 30-second video (Max 10MB)</p>
                    </div>
                </div>

                @if($vendor->sample_work_video)
                    <div class="space-y-4">
                        @php
                            $videoUrl = $vendor->getVideoUrl();
                            $isCloudinary = is_array($vendor->sample_work_video) && ($vendor->sample_work_video['type'] ?? null) === 'cloudinary';
                        @endphp
                        <div class="relative">
                            <video controls class="w-full rounded-lg border border-gray-300 max-h-96">
                                <source src="{{ $videoUrl }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            @if($isCloudinary)
                                <div class="mt-2 text-center text-sm text-blue-600">
                                    <i class="fas fa-cloud mr-1"></i> Hosted on Cloudinary
                                </div>
                            @endif
                        </div>
                        <button 
                            onclick="deleteVideo()"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition"
                        >
                            <i class="fas fa-trash mr-2"></i> Delete Video
                        </button>
                    </div>
                @else
                    <div>
                        <input 
                            type="file" 
                            id="videoInput"
                            accept="video/mp4,video/mov,video/avi,video/wmv"
                            class="hidden"
                        />
                        <label for="videoInput" class="cursor-pointer">
                            <div class="border-2 border-dashed border-purple-300 rounded-lg p-6 sm:p-8 text-center hover:border-purple-500 transition">
                                <i class="fas fa-video text-4xl sm:text-5xl text-purple-500 mb-3"></i>
                                <p class="text-sm sm:text-base font-medium text-gray-700 mb-1">Click to upload video</p>
                                <p class="text-xs sm:text-sm text-gray-500">MP4, MOV, AVI, WMV (Max 10MB, 30 seconds)</p>
                            </div>
                        </label>
                        
                        <div id="videoUploadProgress" class="hidden mt-4">
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-spinner fa-spin text-purple-600 mr-3"></i>
                                    <span class="text-sm text-purple-800">Uploading video...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Image Upload
        document.getElementById('imageInput')?.addEventListener('change', async function(e) {
            const files = Array.from(e.target.files);
            if (files.length === 0) return;

            // Validate file sizes
            const maxSize = 10 * 1024 * 1024; // 10MB (will be auto-compressed)
            const invalidFiles = files.filter(file => file.size > maxSize);
            if (invalidFiles.length > 0) {
                alert(`${invalidFiles.length} file(s) exceed 10MB limit. Please choose smaller images.`);
                return;
            }

            const formData = new FormData();
            files.forEach(file => formData.append('images[]', file));

            document.getElementById('uploadProgress').classList.remove('hidden');

            try {
                const response = await fetch('{{ route('vendor.sample-work.images.upload') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Upload failed');
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('An error occurred during upload');
            } finally {
                document.getElementById('uploadProgress').classList.add('hidden');
                e.target.value = '';
            }
        });

        // Delete Image
        async function deleteImage(imageIndex) {
            if (!confirm('Are you sure you want to delete this image?')) return;

            try {
                const response = await fetch('{{ route('vendor.sample-work.images.delete') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ image_index: imageIndex })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Delete failed');
                }
            } catch (error) {
                console.error('Delete error:', error);
                alert('An error occurred while deleting');
            }
        }

        // Set Preview Image
        async function setPreview(imageIndex) {
            try {
                const response = await fetch('{{ route('vendor.sample-work.preview') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ image_index: imageIndex })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to set preview');
                }
            } catch (error) {
                console.error('Preview error:', error);
                alert('An error occurred');
            }
        }

        // Video Upload
        document.getElementById('videoInput')?.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file size
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                alert('Video file exceeds 10MB limit');
                return;
            }

            const formData = new FormData();
            formData.append('video', file);

            document.getElementById('videoUploadProgress').classList.remove('hidden');

            try {
                const response = await fetch('{{ route('vendor.sample-work.video.upload') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Upload failed');
                }
            } catch (error) {
                console.error('Video upload error:', error);
                alert('An error occurred during upload');
            } finally {
                document.getElementById('videoUploadProgress').classList.add('hidden');
                e.target.value = '';
            }
        });

        // Delete Video
        async function deleteVideo() {
            if (!confirm('Are you sure you want to delete this video?')) return;

            try {
                const response = await fetch('{{ route('vendor.sample-work.video.delete') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Delete failed');
                }
            } catch (error) {
                console.error('Delete error:', error);
                alert('An error occurred while deleting');
            }
        }
    </script>
    @endpush
</x-vendor-layout>

