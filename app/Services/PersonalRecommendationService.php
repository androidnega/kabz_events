<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\UserInteraction;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class PersonalRecommendationService
{
    /**
     * Compute personalized recommendations.
     *
     * $options: user_id, lat, lng, category_id, limit
     */
    public static function get(array $options = []): Collection
    {
        $userId = $options['user_id'] ?? auth()->id();
        $lat = $options['lat'] ?? null;
        $lng = $options['lng'] ?? null;
        $categoryId = $options['category_id'] ?? null;
        $limit = $options['limit'] ?? 12;

        $cacheKey = "personal_recs_{$userId}_" . ($lat ?? 'noloc') . '_' . ($lng ?? 'noloc') . '_' . ($categoryId ?? 'all');

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($userId, $lat, $lng, $categoryId, $limit) {
            // If user opted out, fallback to RecommendationService
            $prefs = UserPreference::where('user_id', $userId)->first();
            if ($prefs && !$prefs->personalization_enabled) {
                return RecommendationService::get([
                    'user_id' => $userId,
                    'lat' => $lat,
                    'lng' => $lng,
                    'category_id' => $categoryId,
                    'limit' => $limit
                ]);
            }

            // 1) base: aggregate interactions weights by vendor (recent 90 days)
            $cutoff = now()->subDays(90);
            $interactions = UserInteraction::where('user_id', $userId)
                ->where('created_at', '>=', $cutoff)
                ->selectRaw('vendor_id, SUM(weight) as score')
                ->groupBy('vendor_id')
                ->orderByDesc('score')
                ->pluck('score', 'vendor_id')
                ->toArray();

            // Fetch vendor records for these vendors
            $vendorIds = array_keys($interactions);
            $vendors = Vendor::whereIn('id', $vendorIds)
                ->where('is_verified', 1)
                ->with(['services', 'reviews'])
                ->get();

            // compute composite score (interactionScore + prefBoost + proximity + rating)
            $results = $vendors->map(function ($v) use ($interactions, $prefs, $lat, $lng) {
                $interactionScore = $interactions[$v->id] ?? 0; // unnormalized

                // preference boost:
                $prefBoost = 0;
                if ($prefs) {
                    $pcats = $prefs->preferred_categories ?? [];
                    $ptowns = $prefs->preferred_towns ?? [];

                    // Check if vendor's services match preferred categories
                    $vendorCategories = $v->services->pluck('category_id')->toArray();
                    foreach ($pcats as $pcat) {
                        if (in_array($pcat, $vendorCategories)) {
                            $prefBoost += 1.0;
                            break;
                        }
                    }

                    // Check if vendor's town matches preferred towns
                    if (!empty($ptowns) && in_array($v->town_id, $ptowns)) {
                        $prefBoost += 0.8;
                    }
                }

                // proximity score
                $distance = 9999;
                if ($lat && $lng && $v->latitude && $v->longitude) {
                    $distance = self::haversine($lat, $lng, $v->latitude, $v->longitude);
                }
                $proximityScore = max(0, 1 - ($distance / 100)); // normalize

                // rating score
                $ratingScore = ($v->rating_cached ?? $v->reviews->avg('rating') ?? 3) / 5.0;

                // weights - interaction heavy, then preference, proximity, rating
                $score = (0.6 * $interactionScore) + (0.2 * $prefBoost) + (0.15 * $proximityScore) + (0.05 * $ratingScore);

                $v->personal_score = $score;
                return $v;
            });

            $sorted = $results->sortByDesc('personal_score')->values();

            // supplement with fallback vendors if fewer than limit
            if ($sorted->count() < $limit) {
                $more = RecommendationService::get([
                    'lat' => $lat,
                    'lng' => $lng,
                    'category_id' => $categoryId,
                    'limit' => $limit
                ]);
                
                // merge while preserving sorted order and uniqueness
                $existingIds = $sorted->pluck('id')->toArray();
                foreach ($more as $m) {
                    if (!in_array($m->id, $existingIds)) {
                        $sorted->push($m);
                        $existingIds[] = $m->id;
                        if ($sorted->count() >= $limit) {
                            break;
                        }
                    }
                }
            }

            return $sorted->take($limit);
        });
    }

    /**
     * Calculate distance between two lat/lng coordinates using Haversine formula.
     */
    protected static function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    /**
     * Clear cache for a specific user or all personal recommendations.
     */
    public static function clearCache(?int $userId = null): void
    {
        if ($userId) {
            // In production, use cache tags for more targeted clearing
            $patterns = ["personal_recs_{$userId}_*"];
            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }
        } else {
            Cache::flush();
        }
    }
}

