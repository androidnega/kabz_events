<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Notification;
use App\Services\CloudinaryService;
use App\Services\SettingsService;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CloudinaryMediaController extends Controller
{
    protected $cloudinary;
    protected $cloudinaryService;

    public function __construct()
    {
        $this->cloudinaryService = new CloudinaryService();
    }

    protected function getCloudinary()
    {
        if (!$this->cloudinary) {
            $cloudName = SettingsService::get('cloudinary_cloud_name');
            $apiKey = SettingsService::get('cloudinary_api_key');
            $apiSecret = SettingsService::get('cloudinary_api_secret');

            if ($cloudName && $apiKey && $apiSecret) {
                $this->cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key' => $apiKey,
                        'api_secret' => $apiSecret
                    ]
                ]);
            }
        }
        return $this->cloudinary;
    }

    /**
     * Display all Cloudinary folders
     */
    public function index()
    {
        $cloudinary = $this->getCloudinary();
        
        if (!$cloudinary) {
            return redirect()->back()->with('error', 'Cloudinary not configured');
        }

        try {
            $folders = [
                [
                    'name' => 'vendor_sample_work',
                    'display_name' => 'Vendor Sample Work',
                    'description' => 'Vendor portfolio and sample work images',
                    'icon' => 'fa-images'
                ],
                [
                    'name' => 'profile_photos',
                    'display_name' => 'Profile Photos',
                    'description' => 'Vendor and user profile photos',
                    'icon' => 'fa-user-circle'
                ],
                [
                    'name' => 'promo_videos',
                    'display_name' => 'Promotional Videos',
                    'description' => 'Vendor promotional videos',
                    'icon' => 'fa-video'
                ],
                [
                    'name' => 'Ghana_card_verifications',
                    'display_name' => 'Verification Documents',
                    'description' => 'Ghana Card and verification documents',
                    'icon' => 'fa-id-card'
                ]
            ];

            // Get count for each folder
            foreach ($folders as &$folder) {
                try {
                    $response = $cloudinary->adminApi()->assets([
                        'type' => 'upload',
                        'prefix' => $folder['name'],
                        'max_results' => 500
                    ]);
                    $folder['count'] = isset($response['resources']) ? count($response['resources']) : 0;
                } catch (\Exception $e) {
                    Log::error('Folder count error: ' . $e->getMessage(), ['folder' => $folder['name']]);
                    $folder['count'] = 0;
                }
            }

            return view('admin.cloudinary.index', compact('folders'));

        } catch (\Exception $e) {
            Log::error('Cloudinary folders error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load Cloudinary folders');
        }
    }

    /**
     * Display media gallery for a specific folder
     */
    public function gallery(Request $request, $folder)
    {
        $cloudinary = $this->getCloudinary();
        
        if (!$cloudinary) {
            return redirect()->back()->with('error', 'Cloudinary not configured');
        }

        try {
            $perPage = 24;
            $search = $request->get('search');
            $sortBy = $request->get('sort', 'created_desc');

            $options = [
                'type' => 'upload',
                'prefix' => $folder,
                'max_results' => $perPage,
            ];

            // Add search if provided
            if ($search) {
                $options['search'] = $search;
            }

            // Add sorting
            switch ($sortBy) {
                case 'created_desc':
                    $options['order_by'] = 'created_at';
                    $options['direction'] = 'desc';
                    break;
                case 'created_asc':
                    $options['order_by'] = 'created_at';
                    $options['direction'] = 'asc';
                    break;
                case 'name_asc':
                    $options['order_by'] = 'public_id';
                    $options['direction'] = 'asc';
                    break;
                case 'name_desc':
                    $options['order_by'] = 'public_id';
                    $options['direction'] = 'desc';
                    break;
            }

            $response = $cloudinary->adminApi()->assets($options);
            
            $media = collect($response['resources'] ?? [])->map(function ($resource) use ($folder) {
                return $this->enrichMediaData($resource, $folder);
            });

            $totalCount = $response['total_count'] ?? 0;

            $folderDisplayNames = [
                'vendor_sample_work' => 'Vendor Sample Work',
                'profile_photos' => 'Profile Photos',
                'promo_videos' => 'Promotional Videos',
                'Ghana_card_verifications' => 'Verification Documents'
            ];

            return view('admin.cloudinary.gallery', [
                'folder' => $folder,
                'folderDisplayName' => $folderDisplayNames[$folder] ?? $folder,
                'media' => $media,
                'totalCount' => $totalCount,
                'search' => $search,
                'sortBy' => $sortBy
            ]);

        } catch (\Exception $e) {
            Log::error('Cloudinary gallery error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load media gallery');
        }
    }

    /**
     * Enrich media data with user/vendor information
     */
    protected function enrichMediaData($resource, $folder)
    {
        $data = [
            'public_id' => $resource['public_id'],
            'url' => $resource['secure_url'],
            'created_at' => $resource['created_at'],
            'format' => $resource['format'],
            'bytes' => $resource['bytes'],
            'width' => $resource['width'] ?? null,
            'height' => $resource['height'] ?? null,
            'resource_type' => $resource['resource_type'],
            'owner' => null,
            'owner_type' => null
        ];

        // Try to find the owner based on the folder and URL
        $data['owner'] = $this->findMediaOwner($resource['secure_url'], $folder);

        return $data;
    }

    /**
     * Find the owner of a media file
     */
    protected function findMediaOwner($url, $folder)
    {
        try {
            switch ($folder) {
                case 'vendor_sample_work':
                    $vendor = Vendor::whereRaw("JSON_SEARCH(sample_work_images, 'one', ?) IS NOT NULL", [$url])->first();
                    if ($vendor) {
                        return [
                            'type' => 'vendor',
                            'id' => $vendor->id,
                            'name' => $vendor->business_name,
                            'user_id' => $vendor->user_id
                        ];
                    }
                    break;

                case 'profile_photos':
                    $vendor = Vendor::where('profile_photo', $url)->first();
                    if ($vendor) {
                        return [
                            'type' => 'vendor_profile',
                            'id' => $vendor->id,
                            'name' => $vendor->business_name,
                            'user_id' => $vendor->user_id
                        ];
                    }
                    
                    $user = User::where('profile_photo', $url)->first();
                    if ($user) {
                        return [
                            'type' => 'user_profile',
                            'id' => $user->id,
                            'name' => $user->name,
                            'user_id' => $user->id
                        ];
                    }
                    break;

                case 'promo_videos':
                    $vendor = Vendor::whereRaw("JSON_EXTRACT(sample_work_video, '$.url') = ?", [$url])->first();
                    if ($vendor) {
                        return [
                            'type' => 'vendor_video',
                            'id' => $vendor->id,
                            'name' => $vendor->business_name,
                            'user_id' => $vendor->user_id
                        ];
                    }
                    break;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Find owner error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download media from Cloudinary
     */
    public function download(Request $request)
    {
        $publicId = $request->get('public_id');
        $cloudinary = $this->getCloudinary();
        
        if (!$cloudinary) {
            return response()->json(['error' => 'Cloudinary not configured'], 400);
        }

        try {
            $resource = $cloudinary->adminApi()->asset($publicId);
            return response()->json([
                'success' => true,
                'url' => $resource['secure_url']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'File not found'], 404);
        }
    }

    /**
     * Delete media from Cloudinary and database
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'public_id' => 'required|string',
            'reason' => 'required|string|min:10|max:500',
            'folder' => 'required|string'
        ]);

        try {
            $publicId = $request->public_id;
            $reason = $request->reason;
            $folder = $request->folder;

            // Get media details first
            $resource = $this->cloudinary->adminApi()->asset($publicId);
            $url = $resource['secure_url'];

            // Find owner
            $owner = $this->findMediaOwner($url, $folder);

            // Delete from Cloudinary
            if ($resource['resource_type'] === 'video') {
                $this->cloudinaryService->deleteVideo($publicId);
            } else {
                $this->cloudinaryService->deleteImage($publicId);
            }

            // Remove from database based on folder type
            $this->removeFromDatabase($url, $folder);

            // Send notification to user/vendor
            if ($owner) {
                $this->sendDeletionNotification($owner, $folder, $reason);
            }

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Media deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove media reference from database
     */
    protected function removeFromDatabase($url, $folder)
    {
        switch ($folder) {
            case 'vendor_sample_work':
                $vendors = Vendor::all();
                foreach ($vendors as $vendor) {
                    if ($vendor->sample_work_images) {
                        $images = array_filter($vendor->sample_work_images, function($img) use ($url) {
                            $imgUrl = is_array($img) ? ($img['url'] ?? null) : $img;
                            return $imgUrl !== $url;
                        });
                        $vendor->sample_work_images = array_values($images);
                        $vendor->save();
                    }
                }
                break;

            case 'profile_photos':
                Vendor::where('profile_photo', $url)->update(['profile_photo' => null]);
                User::where('profile_photo', $url)->update(['profile_photo' => null]);
                break;

            case 'promo_videos':
                Vendor::whereRaw("JSON_EXTRACT(sample_work_video, '$.url') = ?", [$url])
                    ->update(['sample_work_video' => null]);
                break;
        }
    }

    /**
     * Send notification to user about media deletion
     */
    protected function sendDeletionNotification($owner, $folder, $reason)
    {
        if (!$owner || !isset($owner['user_id'])) {
            return;
        }

        $userId = $owner['user_id'];
        $user = User::find($userId);

        if (!$user) {
            return;
        }

        $folderNames = [
            'vendor_sample_work' => 'sample work image',
            'profile_photos' => 'profile photo',
            'promo_videos' => 'promotional video',
            'Ghana_card_verifications' => 'verification document'
        ];

        $mediaType = $folderNames[$folder] ?? 'media file';

        // Create notification
        Notification::create([
            'user_id' => $userId,
            'type' => 'media_deleted',
            'title' => 'Media Removed by Admin',
            'message' => "Your {$mediaType} has been removed from the system. Reason: {$reason}",
            'data' => json_encode([
                'folder' => $folder,
                'reason' => $reason,
                'owner_name' => $owner['name']
            ])
        ]);

        // Send email notification
        $this->sendDeletionEmail($user, $mediaType, $reason);
    }

    /**
     * Send email notification about media deletion
     */
    protected function sendDeletionEmail($user, $mediaType, $reason)
    {
        try {
            $smtpEnabled = SettingsService::get('smtp_enabled');
            
            if (!$smtpEnabled) {
                return;
            }

            Mail::send('emails.media-deleted', [
                'userName' => $user->name,
                'mediaType' => $mediaType,
                'reason' => $reason
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Media Removed - Kabz Events');
            });

        } catch (\Exception $e) {
            Log::error('Failed to send deletion email: ' . $e->getMessage());
        }
    }
}
