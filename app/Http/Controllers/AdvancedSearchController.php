<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Vendor;
use App\Services\PersonalizedSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdvancedSearchController extends Controller
{
    /**
     * Ghana regions and districts for filtering
     */
    private const GHANA_LOCATIONS = [
        'Greater Accra' => ['Accra Metropolitan', 'Tema Metropolitan', 'Ga East', 'Ga West', 'Ga South'],
        'Ashanti' => ['Kumasi Metropolitan', 'Obuasi Municipal', 'Ejisu', 'Mampong Municipal'],
        'Western' => ['Sekondi-Takoradi', 'Tarkwa', 'Prestea', 'Axim'],
        'Central' => ['Cape Coast Metropolitan', 'Kasoa', 'Winneba', 'Agona Swedru'],
        'Northern' => ['Tamale Metropolitan', 'Yendi', 'Savelugu', 'Gumani'],
        'Eastern' => ['Koforidua', 'New Juaben', 'Akropong', 'Nsawam'],
        'Volta' => ['Ho Municipal', 'Hohoe', 'Keta', 'Aflao'],
        'Upper East' => ['Bolgatanga Municipal', 'Bongo', 'Navrongo'],
        'Upper West' => ['Wa Municipal', 'Wechiau', 'Lawra'],
        'Bono' => ['Sunyani Municipal', 'Berekum', 'Techiman'],
    ];
    
    /**
     * Display advanced search results with comprehensive filters
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Log search activity for recommendations
        if ($user && $request->hasAny(['q', 'category', 'region', 'district'])) {
            PersonalizedSearchService::logSearchActivity($user, $request->only([
                'q', 'category_id', 'region', 'district', 'lat', 'lng'
            ]));
        }
        
        // Start with all vendors (verified and non-verified)
        $query = Vendor::query();
        
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
        
        // Category ID filter (for API/direct calls)
        if ($request->filled('category_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        
        // Region filter
        if ($request->filled('region')) {
            $query->where('address', 'like', "%{$request->region}%");
        }
        
        // District/town filter
        if ($request->filled('district')) {
            $query->where('address', 'like', "%{$request->district}%");
        }
        
        // Rating filter (minimum rating)
        if ($request->filled('min_rating')) {
            $minRating = (float) $request->min_rating;
            $query->where('rating_cached', '>=', $minRating);
        }
        
        // Location-based search (GPS coordinates)
        $hasLocation = $request->filled(['lat', 'lng']);
        $userLat = $hasLocation ? (float) $request->lat : ($user->latitude ?? null);
        $userLng = $hasLocation ? (float) $request->lng : ($user->longitude ?? null);
        $radius = $request->filled('radius') ? (float) $request->radius : ($user->search_radius_km ?? 50);
        
        if ($userLat && $userLng) {
            // Calculate distance using Haversine formula
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(vendors.latitude)) * cos(radians(vendors.longitude) - radians(?)) + sin(radians(?)) * sin(radians(vendors.latitude))))";
            
            $query->selectRaw("vendors.*, $haversine as distance", [$userLat, $userLng, $userLat])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->havingRaw("distance <= ?", [$radius]);
        }
        
        // Sorting
        $sortBy = $request->input('sort', 'recommended');
        
        switch ($sortBy) {
            case 'distance':
                if ($userLat && $userLng) {
                    $query->orderBy('distance', 'asc');
                } else {
                    $query->orderByDesc('rating_cached');
                }
                break;
            case 'rating':
                $query->orderByDesc('rating_cached');
                break;
            case 'recent':
                $query->latest('created_at');
                break;
            case 'name':
                $query->orderBy('business_name');
                break;
            case 'recommended':
            default:
                // For logged-in users with behavior data, use personalized scoring
                if ($user && $user->total_searches > 2 && $user->total_vendor_views > 2) {
                    // Get personalized recommendations
                    $personalizedIds = PersonalizedSearchService::getPersonalizedRecommendations($user, [
                        'limit' => 100
                    ])->pluck('id')->toArray();
                    
                    if (!empty($personalizedIds)) {
                        // Order by personalized recommendation score
                        $query->orderByRaw('FIELD(vendors.id, ' . implode(',', $personalizedIds) . ') DESC');
                    }
                } else {
                    // Default: rating then distance
                    $query->orderByDesc('rating_cached');
                    if ($userLat && $userLng) {
                        $query->orderBy('distance', 'asc');
                    }
                }
                break;
        }
        
        // Load relationships
        $query->with(['services.category', 'reviews' => function ($q) {
            $q->where('approved', true)->latest()->take(3);
        }]);
        
        // Paginate results
        $vendors = $query->paginate(12);
        
        // Preserve query string parameters
        $vendors->appends(request()->query());
        
        // Add user-friendly distance to results
        if ($userLat && $userLng) {
            foreach ($vendors as $vendor) {
                if (isset($vendor->distance)) {
                    $vendor->distance_formatted = $this->formatDistance($vendor->distance);
                }
            }
        }
        
        // Get all categories for filter dropdown
        $categories = Category::orderBy('name')->get();
        
        // Ghana regions and districts
        $regions = array_keys(self::GHANA_LOCATIONS);
        $districts = self::GHANA_LOCATIONS;
        
        // Get personalized recommendations if user has enough activity
        $showPersonalized = $user && $user->total_searches > 2 && $user->total_vendor_views > 2;
        $personalizedVendors = $showPersonalized 
            ? PersonalizedSearchService::getPersonalizedRecommendations($user, ['limit' => 6])
            : collect();
        
        return view('search.advanced', compact(
            'vendors', 
            'categories', 
            'regions', 
            'districts',
            'showPersonalized',
            'personalizedVendors',
            'userLat',
            'userLng'
        ));
    }
    
    /**
     * Update user's location (AJAX endpoint)
     */
    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
        ]);
        
        PersonalizedSearchService::updateUserLocation(
            $user,
            $validated['latitude'],
            $validated['longitude'],
            $validated['location_name'] ?? null
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'location' => [
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'name' => $user->location_name,
            ]
        ]);
    }
    
    /**
     * Get nearby vendors based on user's location (AJAX endpoint)
     */
    public function getNearbyVendors(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:200',
            'category_id' => 'nullable|exists:categories,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);
        
        $lat = $validated['latitude'];
        $lng = $validated['longitude'];
        $radius = $validated['radius'] ?? 50;
        $limit = $validated['limit'] ?? 12;
        
        // Calculate distance and filter by radius
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(vendors.latitude)) * cos(radians(vendors.longitude) - radians(?)) + sin(radians(?)) * sin(radians(vendors.latitude))))";
        
        $query = Vendor::selectRaw("vendors.*, $haversine as distance", [$lat, $lng, $lat])
            ->where('is_verified', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->havingRaw("distance <= ?", [$radius])
            ->orderBy('distance', 'asc');
        
        if (!empty($validated['category_id'])) {
            $query->whereHas('services', function ($q) use ($validated) {
                $q->where('category_id', $validated['category_id']);
            });
        }
        
        $vendors = $query->with(['services.category'])
            ->take($limit)
            ->get();
        
        // Format response
        $vendors->transform(function ($vendor) {
            return [
                'id' => $vendor->id,
                'business_name' => $vendor->business_name,
                'slug' => $vendor->slug,
                'rating' => $vendor->rating_cached,
                'distance' => round($vendor->distance, 2),
                'distance_formatted' => $this->formatDistance($vendor->distance),
                'address' => $vendor->address,
                'latitude' => $vendor->latitude,
                'longitude' => $vendor->longitude,
                'categories' => $vendor->services->pluck('category.name')->unique()->values(),
                'url' => route('vendors.show', $vendor->slug),
            ];
        });
        
        return response()->json([
            'success' => true,
            'vendors' => $vendors,
            'count' => $vendors->count(),
            'search_params' => [
                'latitude' => $lat,
                'longitude' => $lng,
                'radius_km' => $radius,
            ]
        ]);
    }
    
    /**
     * Format distance for display
     */
    private function formatDistance(float $km): string
    {
        if ($km < 1) {
            return round($km * 1000) . 'm away';
        } elseif ($km < 10) {
            return round($km, 1) . 'km away';
        } else {
            return round($km) . 'km away';
        }
    }
}

