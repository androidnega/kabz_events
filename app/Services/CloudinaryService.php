<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private $cloudName;
    private $apiKey;
    private $apiSecret;
    private $uploadUrl;

    public function __construct()
    {
        $this->cloudName = SettingsService::get('cloudinary_cloud_name');
        $this->apiKey = SettingsService::get('cloudinary_api_key');
        $this->apiSecret = SettingsService::get('cloudinary_api_secret');
        
        if ($this->cloudName) {
            $this->uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/upload";
        }
    }

    /**
     * Check if Cloudinary is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->cloudName) && 
               !empty($this->apiKey) && 
               !empty($this->apiSecret);
    }

    /**
     * Upload an image to Cloudinary with automatic compression.
     * 
     * @param UploadedFile $file
     * @param string $folder Cloudinary folder path
     * @param array $transformations Additional transformations (e.g., ['width' => 1920, 'quality' => 'auto'])
     * @return array|null
     */
    public function uploadImage(UploadedFile $file, string $folder = 'chat', array $transformations = []): ?array
    {
        if (!$this->isConfigured()) {
            Log::warning('Cloudinary is not configured');
            return null;
        }

        try {
            $timestamp = time();
            
            // Default transformations for automatic compression
            $defaultTransformations = [
                'quality' => 'auto:good', // Automatic quality adjustment
                'fetch_format' => 'auto', // Automatic format selection (WebP if supported)
            ];
            
            // Merge with custom transformations
            $allTransformations = array_merge($defaultTransformations, $transformations);
            
            $signature = $this->generateSignatureWithTransformations($timestamp, $folder, $allTransformations);

            $multipartData = [
                [
                    'name' => 'file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ],
                [
                    'name' => 'timestamp',
                    'contents' => $timestamp,
                ],
                [
                    'name' => 'folder',
                    'contents' => $folder,
                ],
                [
                    'name' => 'api_key',
                    'contents' => $this->apiKey,
                ],
                [
                    'name' => 'signature',
                    'contents' => $signature,
                ],
                [
                    'name' => 'resource_type',
                    'contents' => 'image',
                ],
            ];

            // Add transformation parameters
            foreach ($allTransformations as $key => $value) {
                $multipartData[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }

            $response = Http::asMultipart()->post($this->uploadUrl, $multipartData);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'url' => $data['secure_url'] ?? $data['url'],
                    'public_id' => $data['public_id'],
                    'format' => $data['format'],
                    'width' => $data['width'] ?? null,
                    'height' => $data['height'] ?? null,
                    'bytes' => $data['bytes'] ?? null,
                ];
            }

            Log::error('Cloudinary upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Cloudinary upload exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Upload an audio file to Cloudinary.
     */
    public function uploadAudio(UploadedFile $file, string $folder = 'chat'): ?array
    {
        if (!$this->isConfigured()) {
            Log::warning('Cloudinary is not configured');
            return null;
        }

        try {
            $timestamp = time();
            $signature = $this->generateSignature($timestamp, $folder);

            $response = Http::asMultipart()->post($this->uploadUrl, [
                [
                    'name' => 'file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ],
                [
                    'name' => 'timestamp',
                    'contents' => $timestamp,
                ],
                [
                    'name' => 'folder',
                    'contents' => $folder,
                ],
                [
                    'name' => 'api_key',
                    'contents' => $this->apiKey,
                ],
                [
                    'name' => 'signature',
                    'contents' => $signature,
                ],
                [
                    'name' => 'resource_type',
                    'contents' => 'video', // Cloudinary uses 'video' for audio files
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'url' => $data['secure_url'] ?? $data['url'],
                    'public_id' => $data['public_id'],
                    'format' => $data['format'],
                    'duration' => $data['duration'] ?? null,
                ];
            }

            Log::error('Cloudinary audio upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Cloudinary audio upload exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Generate signature for Cloudinary upload with transformations.
     */
    private function generateSignatureWithTransformations(int $timestamp, string $folder, array $transformations = []): string
    {
        $params = array_merge([
            'timestamp' => $timestamp,
            'folder' => $folder,
        ], $transformations);

        ksort($params);
        
        $signatureString = '';
        foreach ($params as $key => $value) {
            $signatureString .= "{$key}={$value}&";
        }
        $signatureString = rtrim($signatureString, '&');
        $signatureString .= $this->apiSecret;

        return sha1($signatureString);
    }

    /**
     * Generate signature for Cloudinary upload (legacy method).
     */
    private function generateSignature(int $timestamp, string $folder): string
    {
        return $this->generateSignatureWithTransformations($timestamp, $folder);
    }

    /**
     * Upload a video to Cloudinary.
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param int|null $maxDuration Maximum duration in seconds
     * @return array|null
     */
    public function uploadVideo(UploadedFile $file, string $folder = 'videos', ?int $maxDuration = null): ?array
    {
        if (!$this->isConfigured()) {
            Log::warning('Cloudinary is not configured');
            return null;
        }

        try {
            $timestamp = time();
            $signature = $this->generateSignature($timestamp, $folder);

            $multipartData = [
                [
                    'name' => 'file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ],
                [
                    'name' => 'timestamp',
                    'contents' => $timestamp,
                ],
                [
                    'name' => 'folder',
                    'contents' => $folder,
                ],
                [
                    'name' => 'api_key',
                    'contents' => $this->apiKey,
                ],
                [
                    'name' => 'signature',
                    'contents' => $signature,
                ],
                [
                    'name' => 'resource_type',
                    'contents' => 'video',
                ],
            ];

            // Add max duration if specified
            if ($maxDuration) {
                $multipartData[] = [
                    'name' => 'duration',
                    'contents' => $maxDuration,
                ];
            }

            $response = Http::timeout(120)->asMultipart()->post($this->uploadUrl, $multipartData);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'url' => $data['secure_url'] ?? $data['url'],
                    'public_id' => $data['public_id'],
                    'format' => $data['format'],
                    'duration' => $data['duration'] ?? null,
                    'width' => $data['width'] ?? null,
                    'height' => $data['height'] ?? null,
                    'bytes' => $data['bytes'] ?? null,
                ];
            }

            Log::error('Cloudinary video upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Cloudinary video upload exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Delete a file from Cloudinary.
     */
    public function deleteFile(string $publicId, string $resourceType = 'image'): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $timestamp = time();
            $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$this->apiSecret}");

            $deleteUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/{$resourceType}/destroy";

            $response = Http::asForm()->post($deleteUrl, [
                'public_id' => $publicId,
                'timestamp' => $timestamp,
                'api_key' => $this->apiKey,
                'signature' => $signature,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Cloudinary delete exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

