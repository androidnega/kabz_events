<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CloudinaryService
{
    protected ?Cloudinary $cloudinary = null;
    protected bool $isEnabled = false;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initialize Cloudinary connection
     */
    protected function initialize(): void
    {
        $cloudName = SettingsService::get('cloudinary_cloud_name');
        $apiKey = SettingsService::get('cloudinary_api_key');
        $apiSecret = SettingsService::get('cloudinary_api_secret');
        $storageProvider = SettingsService::get('cloud_storage', 'local');

        if ($storageProvider === 'cloudinary' && $cloudName && $apiKey && $apiSecret) {
            try {
                $this->cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key' => $apiKey,
                        'api_secret' => $apiSecret
                    ]
                ]);
                $this->isEnabled = true;
            } catch (\Exception $e) {
                Log::error('Cloudinary initialization failed: ' . $e->getMessage());
                $this->isEnabled = false;
            }
        }
    }

    /**
     * Check if Cloudinary is enabled and configured
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Upload an image to Cloudinary
     * 
     * @param UploadedFile $file The uploaded file
     * @param string $folder The Cloudinary folder to upload to (e.g., 'vendors/profiles')
     * @param array $options Additional upload options
     * @return array Returns ['success' => bool, 'url' => string|null, 'public_id' => string|null, 'path' => string|null]
     */
    public function uploadImage(UploadedFile $file, string $folder, array $options = []): array
    {
        if (!$this->isEnabled) {
            // Fallback to local storage
            return $this->uploadToLocal($file, $folder);
        }

        try {
            $uploadOptions = array_merge([
                'folder' => $folder,
                'resource_type' => 'image',
                'use_filename' => true,
                'unique_filename' => true,
            ], $options);

            $response = $this->cloudinary->uploadApi()->upload(
                $file->getPathname(),
                $uploadOptions
            );

            return [
                'success' => true,
                'url' => $response['secure_url'],
                'public_id' => $response['public_id'],
                'path' => null, // Cloudinary doesn't use local paths
                'provider' => 'cloudinary'
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage());
            // Fallback to local storage
            return $this->uploadToLocal($file, $folder);
        }
    }

    /**
     * Upload a video to Cloudinary
     * 
     * @param UploadedFile $file The uploaded video file
     * @param string $folder The Cloudinary folder to upload to
     * @param array $options Additional upload options
     * @return array Returns ['success' => bool, 'url' => string|null, 'public_id' => string|null, 'path' => string|null]
     */
    public function uploadVideo(UploadedFile $file, string $folder, array $options = []): array
    {
        if (!$this->isEnabled) {
            return $this->uploadToLocal($file, $folder . '/videos');
        }

        try {
            $uploadOptions = array_merge([
                'folder' => $folder,
                'resource_type' => 'video',
                'use_filename' => true,
                'unique_filename' => true,
            ], $options);

            $response = $this->cloudinary->uploadApi()->upload(
                $file->getPathname(),
                $uploadOptions
            );

            return [
                'success' => true,
                'url' => $response['secure_url'],
                'public_id' => $response['public_id'],
                'path' => null,
                'provider' => 'cloudinary'
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary video upload failed: ' . $e->getMessage());
            return $this->uploadToLocal($file, $folder . '/videos');
        }
    }

    /**
     * Delete an image from Cloudinary
     * 
     * @param string $publicId The Cloudinary public_id
     * @return bool
     */
    public function deleteImage(string $publicId): bool
    {
        if (!$this->isEnabled || empty($publicId)) {
            return false;
        }

        try {
            $this->cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
            return true;
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a video from Cloudinary
     * 
     * @param string $publicId The Cloudinary public_id
     * @return bool
     */
    public function deleteVideo(string $publicId): bool
    {
        if (!$this->isEnabled || empty($publicId)) {
            return false;
        }

        try {
            $this->cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'video']);
            return true;
        } catch (\Exception $e) {
            Log::error('Cloudinary video delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fallback: Upload to local storage
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @return array
     */
    protected function uploadToLocal(UploadedFile $file, string $folder): array
    {
        try {
            $path = $file->store($folder, 'public');
            
            return [
                'success' => true,
                'url' => Storage::url($path),
                'public_id' => null,
                'path' => $path,
                'provider' => 'local'
            ];
        } catch (\Exception $e) {
            Log::error('Local storage upload failed: ' . $e->getMessage());
            return [
                'success' => false,
                'url' => null,
                'public_id' => null,
                'path' => null,
                'provider' => 'local'
            ];
        }
    }

    /**
     * Get the URL for displaying an image
     * Handles both Cloudinary URLs and local storage paths
     * 
     * @param string|null $urlOrPath The URL or path to the image
     * @return string|null
     */
    public static function getImageUrl(?string $urlOrPath): ?string
    {
        if (empty($urlOrPath)) {
            return null;
        }

        // If it's already a full URL (Cloudinary), return as is
        if (str_starts_with($urlOrPath, 'http://') || str_starts_with($urlOrPath, 'https://')) {
            return $urlOrPath;
        }

        // Otherwise, it's a local storage path
        return Storage::url($urlOrPath);
    }
}
