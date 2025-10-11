<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;

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
        
        // Start with verified vendors only
        $query = Vendor::with(['services.category', 'reviews'])
            ->where('is_verified', true);

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
}
