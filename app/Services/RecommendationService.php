<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\UserActivityLog;
use App\Models\VendorRecommendation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Get recommendations for a user (Enhanced with user behavior).
     *
     * $options = [
     *   'user_id' => null,
     *   'lat' => null,
     *   'lng' => null,
     *   'category_id' => null,
     *   'limit' => 12,
     * ]
     */
    public static function get(array $options = []): Collection
    {
        $userId = $options['user_id'] ?? Auth::id();
        $lat = $options['lat'] ?? null;
        $lng = $options['lng'] ?? null;
        $categoryId = $options['category_id'] ?? null;
        $limit = $options['limit'] ?? 12;

        // Cache key per user/location/category
        $cacheKey = 'recs_' . ($userId ?? 'guest') . '_' . ($lat ?? 'noloc') . '_' . ($lng ?? 'noloc') . '_' . ($categoryId ?? 'all');

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($userId, $lat, $lng, $categoryId, $limit) {
            // If we have precomputed recommendations for this user, use them as a base
            if ($userId) {
                $pre = VendorRecommendation::where('user_id', $userId)
                    ->orderByDesc('score')
                    ->limit($limit)
                    ->pluck('vendor_id')
                    ->toArray();

                if (!empty($pre)) {
                    $vendors = Vendor::whereIn('id', $pre)
                        ->where('is_verified', true)
                        ->with(['services.category', 'reviews'])
                        ->get();
                    
                    // Preserve the order from recommendations
                    $vendorMap = $vendors->keyBy('id');
                    $ordered = collect($pre)
                        ->map(fn($id) => $vendorMap->get($id))
                        ->filter();
                    
                    return $ordered;
                }
                
                // Check if user has behavior data for personalized recommendations
                $user = \App\Models\User::find($userId);
                if ($user && $user->total_searches > 2 && $user->total_vendor_views > 2) {
                    // Use PersonalizedSearchService for users with enough activity
                    return \App\Services\PersonalizedSearchService::getPersonalizedRecommendations($user, [
                        'limit' => $limit,
                        'lat' => $lat,
                        'lng' => $lng,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            // Fall back to on-the-fly scoring
            return self::onTheFlyScore($lat, $lng, $categoryId, $limit);
        });
    }

    /**
     * On-the-fly scoring: proximity + category + rating + recency + popularity.
     */
    protected static function onTheFlyScore($lat, $lng, $categoryId, $limit): Collection
    {
        // base query: verified vendors
        $query = Vendor::query()->select('vendors.*');

        // calculate distance if lat/lng provided using Haversine formula in km
        if ($lat && $lng) {
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(vendors.latitude)) * cos(radians(vendors.longitude) - radians(?)) + sin(radians(?)) * sin(radians(vendors.latitude))))";
            $query->selectRaw("$haversine as distance", [$lat, $lng, $lat])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude');
        } else {
            // fallback big distance to keep scoring maths safe
            $query->selectRaw("9999 as distance");
        }

        if ($categoryId) {
            $query->whereHas('services', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $vendors = $query->where('is_verified', 1)
            ->with(['services.category', 'reviews' => function($q) {
                $q->where('approved', true);
            }])
            ->get();

        // Get view counts for popularity scoring
        $viewCounts = UserActivityLog::where('action', 'viewed_vendor')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('vendor_id, COUNT(*) as view_count')
            ->groupBy('vendor_id')
            ->pluck('view_count', 'vendor_id');

        // compute score locally (php) for flexibility
        $scored = $vendors->map(function ($v) use ($lat, $lng, $categoryId, $viewCounts) {
            // category match score
            $categoryMatch = 0;
            if ($categoryId) {
                $hasCategory = $v->services->pluck('category_id')->contains($categoryId);
                $categoryMatch = $hasCategory ? 1.0 : 0.3;
            } else {
                $categoryMatch = 0.5; // neutral for no category filter
            }
            
            // rating score: use cached rating or calculate from approved reviews
            $avgRating = $v->rating_cached ?? $v->reviews->avg('rating') ?? 3.0;
            $ratingScore = $avgRating / 5.0; // normalized 0..1
            
            // proximity score: distance -> proximityScore 1..0
            $distance = (float) ($v->distance ?? 9999);
            $proximityScore = max(0, 1 - ($distance / 100)); // above 100km → near 0

            // recency: more recent updated vendors get small boost (0..1)
            $days = now()->diffInDays($v->updated_at ?? $v->created_at ?? now());
            $recencyScore = max(0, 1 - ($days / 365)); // older than 1 year → ~0
            
            // popularity score based on recent views (0..1)
            $views = $viewCounts[$v->id] ?? 0;
            $popularityScore = min(1.0, $views / 100); // cap at 100 views

            // Weighted sum (total = 1.0)
            $score = (0.25 * $categoryMatch) + 
                     (0.30 * $proximityScore) + 
                     (0.25 * $ratingScore) + 
                     (0.05 * $recencyScore) +
                     (0.15 * $popularityScore);

            $v->computed_score = $score;
            return $v;
        });

        // sort by computed_score desc
        $sorted = $scored->sortByDesc('computed_score')->values()->take($limit);

        return $sorted;
    }

    /**
     * Clear cache for a specific user or all recommendations.
     */
    public static function clearCache(?int $userId = null): void
    {
        if ($userId) {
            // Clear specific user caches (all location/category combinations)
            Cache::flush(); // In production, use more targeted cache clearing with tags
        } else {
            Cache::flush();
        }
    }
}
