<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class SampleWorkController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }
    /**
     * Display sample work management page.
     */
    public function index(): View
    {
        $vendor = Auth::user()->vendor;
        
        return view('vendor.sample-work.index', compact('vendor'));
    }

    /**
     * Upload sample work images (with Cloudinary support and automatic compression).
     */
    public function uploadImages(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        // Calculate how many images vendor currently has
        $currentImageCount = $vendor->sample_work_images ? count($vendor->sample_work_images) : 0;
        $maxImages = $vendor->getMaxSampleImages();
        $availableSlots = $maxImages - $currentImageCount;
        
        if ($availableSlots <= 0) {
            return response()->json([
                'success' => false,
                'message' => "You have reached the maximum limit of {$maxImages} images."
            ], 400);
        }
        
        // Validate images - Allow larger files as they will be compressed
        $request->validate([
            'images' => 'required|array|max:' . $availableSlots,
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:10240', // Allow up to 10MB, will be compressed
        ]);
        
        $uploadedImages = [];
        $currentImages = $vendor->sample_work_images ?? [];
        
        foreach ($request->file('images') as $image) {
            // Use CloudinaryService which handles both Cloudinary and local storage
            $result = $this->cloudinaryService->uploadImage(
                $image,
                'vendor_sample_work',
                [
                    'width' => 1920, // Max width
                    'crop' => 'limit', // Don't upscale, only downscale if larger
                    'quality' => 'auto:good', // Automatic quality optimization
                ]
            );
            
            if ($result['success']) {
                $uploadedImages[] = [
                    'url' => $result['url'] ?? $result['path'],
                    'public_id' => $result['public_id'],
                    'type' => $result['provider']
                ];
                
                Log::info('Image uploaded', [
                    'vendor_id' => $vendor->id,
                    'provider' => $result['provider'],
                    'url' => $result['url'] ?? $result['path']
                ]);
            }
        }
        
        // Merge with existing images
        $allImages = array_merge($currentImages, $uploadedImages);
        
        // Update vendor
        $vendor->sample_work_images = $allImages;
        
        // If no preview image set, use the first uploaded image
        if (!$vendor->preview_image && !empty($allImages)) {
            $vendor->preview_image = $allImages[0];
        }
        
        $vendor->save();
        
        $storageType = $useCloudinary ? 'Cloudinary (compressed)' : 'local storage';
        
        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully to ' . $storageType . '!',
            'images' => $allImages,
            'storage_type' => $useCloudinary ? 'cloudinary' : 'local'
        ]);
    }

    /**
     * Delete a sample work image (handles both Cloudinary and local storage).
     */
    public function deleteImage(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        $request->validate([
            'image_index' => 'required|integer'
        ]);
        
        $imageIndex = $request->image_index;
        $currentImages = $vendor->sample_work_images ?? [];
        
        if (!isset($currentImages[$imageIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found.'
            ], 404);
        }
        
        $imageData = $currentImages[$imageIndex];
        
        // Delete from appropriate storage
        if (is_array($imageData)) {
            if ($imageData['type'] === 'cloudinary' && !empty($imageData['public_id'])) {
                // Delete from Cloudinary
                $this->cloudinaryService->deleteFile($imageData['public_id'], 'image');
            } elseif ($imageData['type'] === 'local' && Storage::disk('public')->exists($imageData['url'])) {
                // Delete from local storage
                Storage::disk('public')->delete($imageData['url']);
            }
        } else {
            // Legacy format (simple string path)
            if (Storage::disk('public')->exists($imageData)) {
                Storage::disk('public')->delete($imageData);
            }
        }
        
        // Remove from array
        unset($currentImages[$imageIndex]);
        $updatedImages = array_values($currentImages);
        
        // Update vendor
        $vendor->sample_work_images = $updatedImages;
        
        // If deleted image was preview, set new preview
        if ($vendor->preview_image === $imageData || 
            (is_array($vendor->preview_image) && is_array($imageData) && 
             $vendor->preview_image['url'] === $imageData['url'])) {
            $vendor->preview_image = !empty($updatedImages) ? $updatedImages[0] : null;
        }
        
        $vendor->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully!'
        ]);
    }

    /**
     * Set preview image.
     */
    public function setPreview(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        $request->validate([
            'image_index' => 'required|integer'
        ]);
        
        $imageIndex = $request->image_index;
        $currentImages = $vendor->sample_work_images ?? [];
        
        // Verify image exists
        if (!isset($currentImages[$imageIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image selected.'
            ], 400);
        }
        
        $vendor->preview_image = $currentImages[$imageIndex];
        $vendor->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Preview image updated successfully!'
        ]);
    }

    /**
     * Upload sample work video (VIP only, with Cloudinary support).
     */
    public function uploadVideo(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        // Check VIP status
        if (!$vendor->canUploadVideo()) {
            return response()->json([
                'success' => false,
                'message' => 'Video upload is only available for VIP members. Upgrade your account to unlock this feature!'
            ], 403);
        }
        
        // Validate video
        $request->validate([
            'video' => 'required|file|mimes:mp4,mov,avi,wmv|max:10240' // 10MB max
        ]);
        
        $video = $request->file('video');
        $useCloudinary = SettingsService::get('cloud_storage') === 'cloudinary' && $this->cloudinaryService->isConfigured();
        
        // Delete old video if exists
        if ($vendor->sample_work_video) {
            if (is_array($vendor->sample_work_video)) {
                if ($vendor->sample_work_video['type'] === 'cloudinary') {
                    $this->cloudinaryService->deleteFile($vendor->sample_work_video['public_id'], 'video');
                } elseif (Storage::disk('public')->exists($vendor->sample_work_video['url'])) {
                    Storage::disk('public')->delete($vendor->sample_work_video['url']);
                }
            } elseif (Storage::disk('public')->exists($vendor->sample_work_video)) {
                Storage::disk('public')->delete($vendor->sample_work_video);
            }
        }
        
        // Upload new video using CloudinaryService
        $result = $this->cloudinaryService->uploadVideo(
            $video,
            'promo_videos'
        );
        
        if ($result['success']) {
            $videoData = [
                'url' => $result['url'] ?? $result['path'],
                'public_id' => $result['public_id'],
                'type' => $result['provider']
            ];
            
            $vendor->sample_work_video = $videoData;
            $vendor->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully!',
                'video_data' => $videoData
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Video upload failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete sample work video (handles both Cloudinary and local storage).
     */
    public function deleteVideo(): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        if ($vendor->sample_work_video) {
            if (is_array($vendor->sample_work_video)) {
                if ($vendor->sample_work_video['type'] === 'cloudinary' && !empty($vendor->sample_work_video['public_id'])) {
                    $this->cloudinaryService->deleteVideo($vendor->sample_work_video['public_id']);
                } elseif ($vendor->sample_work_video['type'] === 'local' && Storage::disk('public')->exists($vendor->sample_work_video['url'])) {
                    Storage::disk('public')->delete($vendor->sample_work_video['url']);
                }
            } elseif (Storage::disk('public')->exists($vendor->sample_work_video)) {
                Storage::disk('public')->delete($vendor->sample_work_video);
            }
        }
        
        $vendor->sample_work_video = null;
        $vendor->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully!'
        ]);
    }

    /**
     * Update sample work title.
     */
    public function updateTitle(Request $request): RedirectResponse
    {
        $vendor = Auth::user()->vendor;
        
        $request->validate([
            'sample_work_title' => 'nullable|string|max:255'
        ]);
        
        $vendor->sample_work_title = $request->sample_work_title;
        $vendor->save();
        
        return back()->with('success', 'Sample work title updated successfully!');
    }
}
