<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vendor;
use App\Models\UserActivityLog;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class PersonalizedSearchService
{
    /**
     * Get personalized vendor recommendations based on user behavior.
     * 
     * @param User|null $user
     * @param array $options Additional filtering options
     * @return Collection
     */
    public static function getPersonalizedRecommendations(?User $user = null, array $options = []): Collection
    {
        if (!$user) {
            return self::getDefaultRecommendations($options);
        }

        $cacheKey = "personalized_recs_{$user->id}_" . md5(json_encode($options));
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user, $options) {
            $limit = $options['limit'] ?? 12;
            
            // Get user's behavioral data
            $userBehavior = self::analyzeUserBehavior($user);
            
            // Build query with scoring
            $query = Vendor::query()
                ->where('is_verified', true)
                ->with(['services.category', 'reviews']);
            
            // Apply filters based on user behavior and preferences
            if (!empty($userBehavior['preferred_categories'])) {
                $query->whereHas('services', function ($q) use ($userBehavior) {
                    $q->whereIn('category_id', $userBehavior['preferred_categories']);
                });
            }
            
            // Location-based filtering
            if ($user->latitude && $user->longitude) {
                $userLat = (float) $user->latitude;
                $userLng = (float) $user->longitude;
                $query = self::addDistanceCalculation($query, $userLat, $userLng);
                $query->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(vendors.latitude)) * cos(radians(vendors.longitude) - radians(?)) + sin(radians(?)) * sin(radians(vendors.latitude)))) <= ?", 
                    [$userLat, $userLng, $userLat, $user->search_radius_km ?? 50]
                );
            }
            
            $vendors = $query->get();
            
            // Score vendors based on multiple factors
            $scored = $vendors->map(function ($vendor) use ($userBehavior, $user) {
                $score = 0;
                
                // Category match score (40% weight)
                $categoryScore = self::calculateCategoryScore($vendor, $userBehavior['preferred_categories']);
                $score += $categoryScore * 0.40;
                
                // Distance score (25% weight)
                if ($user->latitude && $user->longitude && $vendor->distance !== null) {
                    $distanceScore = max(0, 1 - ($vendor->distance / ($user->search_radius_km ?? 50)));
                    $score += $distanceScore * 0.25;
                }
                
                // Rating score (20% weight)
                $ratingScore = ($vendor->rating_cached ?? 0) / 5;
                $score += $ratingScore * 0.20;
                
                // Interaction history score (10% weight)
                $interactionScore = self::calculateInteractionScore($vendor, $userBehavior);
                $score += $interactionScore * 0.10;
                
                // Recency score (5% weight)
                $days = now()->diffInDays($vendor->updated_at ?? $vendor->created_at);
                $recencyScore = max(0, 1 - ($days / 365));
                $score += $recencyScore * 0.05;
                
                // Store score as attribute (not a database field)
                $vendor->setAttribute('recommendation_score', $score);
                return $vendor;
            });
            
            return $scored->sortByDesc('recommendation_score')->values()->take($limit);
        });
    }
    
    /**
     * Analyze user behavior to understand preferences.
     * 
     * @param User $user
     * @return array
     */
    protected static function analyzeUserBehavior(User $user): array
    {
        // Get user's search history
        $recentActivity = UserActivityLog::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(3))
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();
        
        // Extract preferred categories from viewed vendors
        $viewedVendorIds = $recentActivity
            ->where('action', 'viewed_vendor')
            ->pluck('vendor_id')
            ->filter()
            ->unique()
            ->toArray();
        
        $preferredCategories = [];
        if (!empty($viewedVendorIds)) {
            $preferredCategories = DB::table('services')
                ->whereIn('vendor_id', $viewedVendorIds)
                ->select('category_id', DB::raw('COUNT(*) as count'))
                ->groupBy('category_id')
                ->orderByDesc('count')
                ->take(5)
                ->pluck('category_id')
                ->toArray();
        }
        
        // Use stored preferences if no behavioral data
        if (empty($preferredCategories) && $user->preferred_categories) {
            $preferredCategories = $user->preferred_categories;
        }
        
        // Extract search patterns
        $searchedTerms = $recentActivity
            ->where('action', 'searched')
            ->pluck('meta')
            ->filter()
            ->map(function ($meta) {
                return is_string($meta) ? json_decode($meta, true) : $meta;
            })
            ->filter()
            ->toArray();
        
        return [
            'preferred_categories' => $preferredCategories,
            'viewed_vendors' => $viewedVendorIds,
            'search_patterns' => $searchedTerms,
            'activity_count' => $recentActivity->count(),
        ];
    }
    
    /**
     * Calculate category match score for a vendor.
     * 
     * @param Vendor $vendor
     * @param array<int> $preferredCategories
     * @return float
     */
    protected static function calculateCategoryScore(Vendor $vendor, array $preferredCategories): float
    {
        if (empty($preferredCategories)) {
            return 0.5; // Neutral score
        }
        
        $vendorCategories = $vendor->services->pluck('category_id')->unique()->toArray();
        $matches = count(array_intersect($vendorCategories, $preferredCategories));
        
        return min(1.0, $matches / count($preferredCategories));
    }
    
    /**
     * Calculate interaction score based on past user interactions.
     * 
     * @param Vendor $vendor
     * @param array<string,mixed> $userBehavior
     * @return float
     */
    protected static function calculateInteractionScore(Vendor $vendor, array $userBehavior): float
    {
        if (empty($userBehavior['viewed_vendors'])) {
            return 0.0;
        }
        
        // Check if user has viewed this vendor before
        if (in_array($vendor->id, $userBehavior['viewed_vendors'])) {
            return 0.8; // High score for previously viewed vendors
        }
        
        return 0.0;
    }
    
    /**
     * Add distance calculation to query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Vendor> $query
     * @param float $lat
     * @param float $lng
     * @return \Illuminate\Database\Eloquent\Builder<Vendor>
     */
    protected static function addDistanceCalculation(\Illuminate\Database\Eloquent\Builder $query, float $lat, float $lng): \Illuminate\Database\Eloquent\Builder
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(vendors.latitude)) * cos(radians(vendors.longitude) - radians(?)) + sin(radians(?)) * sin(radians(vendors.latitude))))";
        
        return $query->selectRaw("vendors.*, $haversine as distance", [$lat, $lng, $lat])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }
    
    /**
     * Get default recommendations when user is not logged in or has no history.
     * 
     * @param array $options
     * @return Collection
     */
    protected static function getDefaultRecommendations(array $options = []): Collection
    {
        $limit = $options['limit'] ?? 12;
        
        return Vendor::where('is_verified', true)
            ->with(['services.category', 'reviews'])
            ->orderByDesc('rating_cached')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }
    
    /**
     * Log user search activity for future recommendations.
     * 
     * @param User|null $user
     * @param array $searchParams
     * @return void
     */
    public static function logSearchActivity(?User $user, array $searchParams): void
    {
        if (!$user) {
            return;
        }
        
        UserActivityLog::create([
            'user_id' => $user->id,
            'vendor_id' => null,
            'action' => 'searched',
            'meta' => json_encode($searchParams),
        ]);
        
        // Update user's search count
        $user->increment('total_searches');
        
        // Update preferred categories if provided
        if (!empty($searchParams['category_id'])) {
            $currentPreferred = $user->preferred_categories ?? [];
            if (!in_array($searchParams['category_id'], $currentPreferred)) {
                $currentPreferred[] = $searchParams['category_id'];
                $user->update(['preferred_categories' => array_slice($currentPreferred, -10)]); // Keep last 10
            }
        }
    }
    
    /**
     * Log vendor view for future recommendations.
     * 
     * @param User|null $user
     * @param Vendor $vendor
     * @return void
     */
    public static function logVendorView(?User $user, Vendor $vendor): void
    {
        if (!$user) {
            return;
        }
        
        UserActivityLog::create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'action' => 'viewed_vendor',
            'meta' => json_encode([
                'vendor_name' => $vendor->business_name,
                'categories' => $vendor->services->pluck('category_id')->unique()->toArray(),
            ]),
        ]);
        
        // Update user's view count
        $user->increment('total_vendor_views');
        
        // Update preferred categories based on viewed vendor
        $vendorCategories = $vendor->services->pluck('category_id')->unique()->toArray();
        if (!empty($vendorCategories)) {
            $currentPreferred = $user->preferred_categories ?? [];
            foreach ($vendorCategories as $catId) {
                if (!in_array($catId, $currentPreferred)) {
                    $currentPreferred[] = $catId;
                }
            }
            $user->update(['preferred_categories' => array_slice($currentPreferred, -10)]); // Keep last 10
        }
    }
    
    /**
     * Update user's location.
     * 
     * @param User $user
     * @param float $latitude
     * @param float $longitude
     * @param string|null $locationName
     * @return void
     */
    public static function updateUserLocation(User $user, float $latitude, float $longitude, ?string $locationName = null): void
    {
        $user->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_name' => $locationName,
            'location_updated_at' => now(),
        ]);
        
        // Clear cached recommendations
        Cache::forget("personalized_recs_{$user->id}_*");
    }
}

