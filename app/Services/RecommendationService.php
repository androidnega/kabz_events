<?php

namespace App\Services;

use App\Models\UserActivityLog;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class RecommendationService
{
    /**
     * Get personalized vendor recommendations
     *
     * @param int $limit Number of recommendations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecommendations(int $limit = 6)
    {
        $user = Auth::user();

        // For authenticated users, use their activity
        if ($user) {
            return self::getPersonalizedRecommendations($user, $limit);
        }

        // For guests, show top-rated verified vendors
        return self::getDefaultRecommendations($limit);
    }

    /**
     * Get personalized recommendations based on user activity
     */
    private static function getPersonalizedRecommendations($user, int $limit)
    {
        // Get user's recent activity to determine preferences
        $recentActivity = UserActivityLog::where('user_id', $user->id)
            ->where('action', 'viewed_vendor')
            ->latest()
            ->take(5)
            ->get();

        // Extract categories and regions from recent activity
        $viewedVendorIds = $recentActivity->pluck('vendor_id')->unique();
        
        $query = Vendor::where('is_verified', true)
            ->whereNotIn('id', $viewedVendorIds); // Exclude already viewed

        // If user has activity, find similar vendors
        if ($recentActivity->isNotEmpty()) {
            // Get categories from recently viewed vendors
            $categories = Vendor::whereIn('id', $viewedVendorIds)
                ->with('services.category')
                ->get()
                ->pluck('services')
                ->flatten()
                ->pluck('category_id')
                ->unique();

            if ($categories->isNotEmpty()) {
                $query->whereHas('services', function ($q) use ($categories) {
                    $q->whereIn('category_id', $categories);
                });
            }
        }

        $recommendations = $query
            ->orderByDesc('rating_cached')
            ->limit($limit)
            ->get();

        // If not enough recommendations, fill with top-rated
        if ($recommendations->count() < $limit) {
            $remaining = $limit - $recommendations->count();
            $topRated = self::getDefaultRecommendations($remaining);
            $recommendations = $recommendations->merge($topRated);
        }

        return $recommendations;
    }

    /**
     * Get default recommendations (top-rated vendors)
     */
    private static function getDefaultRecommendations(int $limit)
    {
        return Vendor::where('is_verified', true)
            ->where('rating_cached', '>', 0)
            ->orderByDesc('rating_cached')
            ->limit($limit)
            ->get();
    }

    /**
     * Get location-based recommendations
     */
    public static function getNearbyVendors($regionId, int $limit = 6)
    {
        return Vendor::where('is_verified', true)
            ->where('region_id', $regionId)
            ->orderByDesc('rating_cached')
            ->limit($limit)
            ->get();
    }

    /**
     * Log user activity
     */
    public static function logActivity(string $action, ?int $vendorId = null, ?string $meta = null): void
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'vendor_id' => $vendorId,
                'action' => $action,
                'meta' => $meta,
            ]);
        }
    }
}

