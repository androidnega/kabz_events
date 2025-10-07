<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
        $query = Vendor::where('is_verified', true)
            ->with(['services.category', 'reviews' => function ($query) {
                $query->where('approved', true);
            }]);

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
                  ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }

        $vendors = $query->orderBy('rating_cached', 'desc')
            ->paginate(9);

        $categories = Category::orderBy('name')->get();

        return view('vendors.index', compact('vendors', 'categories'));
    }

    /**
     * Display the specified vendor's profile.
     */
    public function show(string $slug): View
    {
        $vendor = Vendor::where('slug', $slug)
            ->where('is_verified', true)
            ->with([
                'services' => function ($query) {
                    $query->where('is_active', true)->with('category');
                },
                'reviews' => function ($query) {
                    $query->where('approved', true)->with('user')->latest();
                },
                'user'
            ])
            ->firstOrFail();

        // Calculate statistics
        $averageRating = $vendor->reviews->avg('rating') ?? 0;
        $totalReviews = $vendor->reviews->count();

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

        // Log activity for recommendations
        if (auth()->check()) {
            RecommendationService::logActivity(
                'viewed_vendor',
                $vendor->id,
                $vendor->services->first()?->category->name
            );
        }

        // Get personalized recommendations
        $recommendedVendors = RecommendationService::getRecommendations(6);

        // Check if user can access sensitive information (contact, reviews)
        $canAccessSensitive = auth()->check();

        return view('vendors.show', compact('vendor', 'averageRating', 'totalReviews', 'similarVendors', 'canAccessSensitive', 'recommendedVendors'));
    }
}
