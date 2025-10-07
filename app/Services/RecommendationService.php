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
     * Get recommendations for a user (MVP).
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
                        ->with(['user', 'reviews'])
                        ->get();
                    
                    // Preserve the order from recommendations
                    $vendorMap = $vendors->keyBy('id');
                    $ordered = collect($pre)
                        ->map(fn($id) => $vendorMap->get($id))
                        ->filter();
                    
                    return $ordered;
                }
            }

            // Fall back to on-the-fly scoring
            return self::onTheFlyScore($lat, $lng, $categoryId, $limit);
        });
    }

    /**
     * On-the-fly scoring: proximity + category + rating + recency.
     */
    protected static function onTheFlyScore($lat, $lng, $categoryId, $limit): Collection
    {
        // base query: verified vendors
        $query = Vendor::query()->select('vendors.*');

        // calculate distance if lat/lng provided using Haversine formula in km
        if ($lat && $lng) {
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(vendors.latitude)) * cos(radians(vendors.longitude) - radians(?)) + sin(radians(?)) * sin(radians(vendors.latitude))))";
            $query->selectRaw("$haversine as distance", [$lat, $lng, $lat]);
        } else {
            // fallback big distance to keep scoring maths safe
            $query->selectRaw("9999 as distance");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $vendors = $query->where('is_verified', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['user', 'reviews'])
            ->get();

        // compute score locally (php) for flexibility
        $scored = $vendors->map(function ($v) use ($lat, $lng) {
            $categoryMatch = 0; // category logic can be applied externally
            
            // rating score: use cached rating or calculate from reviews
            $avgRating = $v->rating_cached ?? $v->reviews->avg('rating') ?? 3.0;
            $ratingScore = $avgRating / 5.0; // normalized 0..1
            
            // proximity score: distance -> proximityScore 1..0
            $distance = (float) ($v->distance ?? 9999);
            $proximityScore = max(0, 1 - ($distance / 100)); // above 100km → near 0

            // recency: more recent updated vendors get small boost (0..1)
            $days = now()->diffInDays($v->updated_at ?? $v->created_at ?? now());
            $recencyScore = max(0, 1 - ($days / 365)); // older than 1 year → ~0

            // Weighted sum
            $score = (0.4 * $categoryMatch) + (0.35 * $proximityScore) + (0.2 * $ratingScore) + (0.05 * $recencyScore);

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
