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
        // Start with verified vendors only
        $query = Vendor::with(['services.category', 'reviews'])
            ->where('is_verified', true);

        // Keyword search (business name)
        if ($request->filled('q')) {
            $query->where('business_name', 'like', "%{$request->q}%");
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
        $vendors = $query->paginate(12)->withQueryString();

        // Get all categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        // Ghana regions
        $regions = self::GHANA_REGIONS;

        return view('search.index', compact('vendors', 'categories', 'regions'));
    }
}
