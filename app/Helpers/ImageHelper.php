<?php

if (!function_exists('get_image_url')) {
    /**
     * Get the proper URL for displaying an image
     * Handles both Cloudinary URLs and local storage paths
     *
     * @param string|array|null $imageData Can be a URL string, path string, or array with 'url' key
     * @param string|null $default Default image to show if no image exists
     * @return string
     */
    function get_image_url($imageData, ?string $default = null): string
    {
        // Handle array format (e.g., ['url' => '...', 'public_id' => '...'])
        if (is_array($imageData)) {
            $imageData = $imageData['url'] ?? null;
        }

        // If no image data, return default or null
        if (empty($imageData)) {
            return $default ?? '';
        }

        // If it's already a full URL (Cloudinary), return as is
        if (str_starts_with($imageData, 'http://') || str_starts_with($imageData, 'https://')) {
            return $imageData;
        }

        // Otherwise, it's a local storage path
        return \Illuminate\Support\Facades\Storage::url($imageData);
    }
}

