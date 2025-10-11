<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SampleWorkController extends Controller
{
    /**
     * Display sample work management page.
     */
    public function index(): View
    {
        $vendor = Auth::user()->vendor;
        
        return view('vendor.sample-work.index', compact('vendor'));
    }

    /**
     * Upload sample work images.
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
        
        // Validate images
        $request->validate([
            'images' => 'required|array|max:' . $availableSlots,
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:1024', // 1MB per image
        ]);
        
        $uploadedImages = [];
        $currentImages = $vendor->sample_work_images ?? [];
        
        foreach ($request->file('images') as $image) {
            // Store image
            $path = $image->store('vendors/sample_work', 'public');
            $uploadedImages[] = $path;
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
        
        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully!',
            'images' => $allImages
        ]);
    }

    /**
     * Delete a sample work image.
     */
    public function deleteImage(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        $request->validate([
            'image_path' => 'required|string'
        ]);
        
        $imagePath = $request->image_path;
        $currentImages = $vendor->sample_work_images ?? [];
        
        // Remove from array
        $updatedImages = array_values(array_filter($currentImages, function($img) use ($imagePath) {
            return $img !== $imagePath;
        }));
        
        // Delete from storage
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
        
        // Update vendor
        $vendor->sample_work_images = $updatedImages;
        
        // If deleted image was preview, set new preview
        if ($vendor->preview_image === $imagePath) {
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
            'image_path' => 'required|string'
        ]);
        
        $imagePath = $request->image_path;
        $currentImages = $vendor->sample_work_images ?? [];
        
        // Verify image exists in vendor's images
        if (!in_array($imagePath, $currentImages)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image selected.'
            ], 400);
        }
        
        $vendor->preview_image = $imagePath;
        $vendor->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Preview image updated successfully!'
        ]);
    }

    /**
     * Upload sample work video (VIP only).
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
        
        // Check video duration (30 seconds max) - requires FFmpeg
        // For now, we'll rely on file size as a proxy
        
        // Delete old video if exists
        if ($vendor->sample_work_video && Storage::disk('public')->exists($vendor->sample_work_video)) {
            Storage::disk('public')->delete($vendor->sample_work_video);
        }
        
        // Store new video
        $path = $video->store('vendors/sample_work/videos', 'public');
        
        $vendor->sample_work_video = $path;
        $vendor->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Video uploaded successfully!',
            'video_path' => $path
        ]);
    }

    /**
     * Delete sample work video.
     */
    public function deleteVideo(): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        if ($vendor->sample_work_video && Storage::disk('public')->exists($vendor->sample_work_video)) {
            Storage::disk('public')->delete($vendor->sample_work_video);
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
