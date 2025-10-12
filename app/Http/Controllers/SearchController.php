<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Ghana regions for filtering
     */
    private const GHANA_REGIONS = [
        'Greater Accra',
        'Western',
        'Ashanti',
        'Central',
        'Northern',
        'Eastern',
        'Volta',
        'Upper East',
        'Upper West',
        'Brong-Ahafo',
    ];

    /**
     * Display search results with filters
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Log search activity for recommendations
        if ($user && $request->hasAny(['q', 'category', 'region'])) {
            \App\Services\PersonalizedSearchService::logSearchActivity($user, $request->only([
                'q', 'category', 'region'
            ]));
        }
        
        // Start with all vendors (verified and non-verified)
        $query = Vendor::with(['services.category', 'reviews']);

        // Keyword search (business name, description, address)
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('business_name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->whereHas('category', function ($catQuery) use ($request) {
                    $catQuery->where('slug', $request->category);
                });
            });
        }

        // Region filter (Ghana regions)
        if ($request->filled('region')) {
            $query->where('address', 'like', "%{$request->region}%");
        }
        
        // Rating filter (minimum rating)
        if ($request->filled('min_rating')) {
            $minRating = (float) $request->min_rating;
            $query->where('rating_cached', '>=', $minRating);
        }

        // Sorting
        if ($request->filled('sort')) {
            match ($request->sort) {
                'rating' => $query->orderByDesc('rating_cached'),
                'recent' => $query->latest('created_at'),
                'name' => $query->orderBy('business_name'),
                default => $query->orderByDesc('rating_cached'),
            };
        } else {
            // Default sort by rating
            $query->orderByDesc('rating_cached');
        }

        // Paginate results
        $vendors = $query->paginate(12);
        $vendors->appends(request()->query());

        // Get all categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        // Ghana regions
        $regions = self::GHANA_REGIONS;
        
        // Get personalized recommendations if user has enough activity
        $showPersonalized = $user && $user->total_searches > 2 && $user->total_vendor_views > 2;
        $personalizedVendors = $showPersonalized 
            ? \App\Services\PersonalizedSearchService::getPersonalizedRecommendations($user, ['limit' => 6])
            : collect();

        return view('search.index', compact('vendors', 'categories', 'regions', 'showPersonalized', 'personalizedVendors'));
    }
    
    /**
     * Live search API endpoint (AJAX)
     */
    public function liveSearch(Request $request)
    {
        $user = auth()->user();
        
        // Start with all vendors (verified and non-verified)
        $query = Vendor::with(['services.category', 'reviews' => function ($q) {
                $q->where('approved', true)->latest()->take(2);
            }]);

        // Keyword search
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('business_name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->whereHas('category', function ($catQuery) use ($request) {
                    $catQuery->where('slug', $request->category);
                });
            });
        }

        // Region filter
        if ($request->filled('region')) {
            $query->where('address', 'like', "%{$request->region}%");
        }
        
        // District filter
        if ($request->filled('district')) {
            $query->where('address', 'like', "%{$request->district}%");
        }
        
        // Rating filter
        if ($request->filled('min_rating')) {
            $minRating = (float) $request->min_rating;
            $query->where('rating_cached', '>=', $minRating);
        }
        
        // GPS location filtering
        if ($request->filled(['lat', 'lng'])) {
            $lat = (float) $request->lat;
            $lng = (float) $request->lng;
            $radius = $request->filled('radius') ? (float) $request->radius : 50;
            
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(vendors.latitude)) * cos(radians(vendors.longitude) - radians(?)) + sin(radians(?)) * sin(radians(vendors.latitude))))";
            
            $query->selectRaw("vendors.*, $haversine as distance", [$lat, $lng, $lat])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->havingRaw("distance <= ?", [$radius]);
        }

        // Sorting with premium vendors prioritized
        $sortBy = $request->input('sort', 'rating');
        
        // Load active subscriptions for premium sorting
        if ($sortBy === 'premium' || $sortBy === 'rating') {
            $query->with(['subscriptions' => function ($q) {
                $q->where('status', 'active')
                  ->where(function ($query) {
                      $query->whereNull('ends_at')
                          ->orWhere('ends_at', '>=', now());
                  });
            }]);
        }
        
        match ($sortBy) {
            'premium' => $query->orderByRaw('(SELECT COUNT(*) FROM vendor_subscriptions WHERE vendor_subscriptions.vendor_id = vendors.id AND status = "active" AND (ends_at IS NULL OR ends_at >= NOW())) DESC')
                              ->orderByDesc('rating_cached'),
            'rating' => $query->orderByDesc('rating_cached')
                             ->orderByRaw('(SELECT COUNT(*) FROM vendor_subscriptions WHERE vendor_subscriptions.vendor_id = vendors.id AND status = "active" AND (ends_at IS NULL OR ends_at >= NOW())) DESC'),
            'recent' => $query->latest('created_at'),
            'name' => $query->orderBy('business_name'),
            'distance' => $request->filled(['lat', 'lng']) ? $query->orderBy('distance') : $query->orderByDesc('rating_cached'),
            default => $query->orderByDesc('rating_cached'),
        };

        // Get results
        $vendors = $query->take(12)->get();
        
        // Format distance if GPS search
        if ($request->filled(['lat', 'lng'])) {
            foreach ($vendors as $vendor) {
                if (isset($vendor->distance)) {
                    $km = $vendor->distance;
                    if ($km < 1) {
                        $vendor->distance_formatted = round($km * 1000) . 'm away';
                    } elseif ($km < 10) {
                        $vendor->distance_formatted = round($km, 1) . 'km away';
                    } else {
                        $vendor->distance_formatted = round($km) . 'km away';
                    }
                }
            }
        }
        
        // Return JSON response
        return response()->json([
            'success' => true,
            'vendors' => $vendors->map(function ($vendor) {
                // Get subscription status
                $subscription = $vendor->activeSubscription();
                $subscriptionType = null;
                if ($subscription) {
                    $subscriptionType = $subscription->plan_name ?? 'subscribed';
                }
                
                return [
                    'id' => $vendor->id,
                    'business_name' => $vendor->business_name,
                    'slug' => $vendor->slug,
                    'description' => Str::limit($vendor->description, 100),
                    'address' => $vendor->address,
                    'rating' => number_format($vendor->rating_cached, 1),
                    'review_count' => $vendor->reviews->count(),
                    'distance' => $vendor->distance_formatted ?? null,
                    'categories' => $vendor->services->pluck('category.name')->unique()->values()->toArray(),
                    'url' => route('vendors.show', $vendor->slug),
                    'verified' => $vendor->is_verified,
                    'subscription_type' => $subscriptionType,
                    'is_premium' => $subscription && in_array(strtolower($subscription->plan_name ?? ''), ['premium', 'vip']),
                ];
            }),
            'count' => $vendors->count(),
        ]);
    }
}
