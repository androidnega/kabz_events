<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UserActivityLog;
use App\Models\Vendor;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorProfileController extends Controller
{
    /**
     * Display a listing of verified vendors.
     */
    public function index(Request $request): View
    {
        // Show ALL vendors (subscribed, verified, unverified) with priority sorting
        $query = Vendor::with(['services.category', 'reviews' => function ($query) {
                $query->where('approved', true);
            }, 'subscriptions'])
            ->select('vendors.*');

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('business_name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%")
                  ->orWhereHas('services.category', function ($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', "%{$searchTerm}%")
                                   ->orWhere('description', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Region filter
        if ($request->has('region') && $request->region) {
            $query->where('address', 'like', "%{$request->region}%");
        }

        // Sort filter
        $sortBy = $request->input('sort', 'rating');

        // Priority Sorting: Subscribed > Verified > Unverified
        $query->leftJoin('vendor_subscriptions', function ($join) {
            $join->on('vendors.id', '=', 'vendor_subscriptions.vendor_id')
                 ->where('vendor_subscriptions.status', '=', 'active')
                 ->where(function ($q) {
                     $q->whereNull('vendor_subscriptions.ends_at')
                       ->orWhere('vendor_subscriptions.ends_at', '>=', now());
                 });
        })
        ->selectRaw('vendors.*, 
            CASE 
                WHEN vendor_subscriptions.id IS NOT NULL THEN 3
                WHEN vendors.is_verified = 1 THEN 2
                ELSE 1
            END as priority_score');

        // Apply sorting based on user selection
        match ($sortBy) {
            'recent' => $query->orderByDesc('priority_score')->latest('vendors.created_at'),
            'name' => $query->orderByDesc('priority_score')->orderBy('business_name'),
            default => $query->orderByDesc('priority_score')->orderByDesc('rating_cached'),
        };

        $vendors = $query->paginate(9);

        $categories = Category::orderBy('name')->get();

        return view('vendors.index', compact('vendors', 'categories'));
    }

    /**
     * Display the specified vendor's profile.
     */
    public function show(string $slug): View
    {
        // Allow viewing ALL vendors (subscribed, verified, and unverified)
        $vendor = Vendor::where('slug', $slug)
            ->with([
                'services' => function ($query) {
                    $query->where('is_active', true)->with('category');
                },
                'reviews' => function ($query) {
                    $query->where('approved', true)->with('user')->latest();
                },
                'user',
                'subscriptions'
            ])
            ->firstOrFail();

        // Calculate statistics
        $averageRating = $vendor->reviews->avg('rating') ?? 0;
        $totalReviews = $vendor->reviews->count();

        // Calculate average response time
        $averageResponseTime = $this->calculateAverageResponseTime($vendor);

        // Get similar vendors (same categories, different vendor, verified)
        $similarVendors = Vendor::where('is_verified', true)
            ->where('id', '!=', $vendor->id)
            ->whereHas('services.category', function ($query) use ($vendor) {
                $categoryIds = $vendor->services->pluck('category_id')->unique();
                $query->whereIn('categories.id', $categoryIds);
            })
            ->with('services.category')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // Log activity for recommendations using PersonalizedSearchService
        if (auth()->check()) {
            \App\Services\PersonalizedSearchService::logVendorView(auth()->user(), $vendor);
        }

        // Get personalized recommendations (use PersonalizedSearchService for logged-in users)
        $recommendedVendors = auth()->check() 
            ? \App\Services\PersonalizedSearchService::getPersonalizedRecommendations(auth()->user(), ['limit' => 6])
            : RecommendationService::get(['limit' => 6]);

        // Check if user can access sensitive information (contact, reviews)
        $canAccessSensitive = auth()->check();

        return view('vendors.show', compact('vendor', 'averageRating', 'totalReviews', 'similarVendors', 'canAccessSensitive', 'recommendedVendors', 'averageResponseTime'));
    }

    /**
     * Calculate the average response time for vendor replies
     */
    private function calculateAverageResponseTime(Vendor $vendor): ?string
    {
        return \App\Models\VendorResponseTime::getAverageResponseTime($vendor->id);
    }
}
