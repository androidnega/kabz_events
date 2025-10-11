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
        // Get vendor's user ID
        $vendorUserId = $vendor->user_id;
        
        if (!$vendorUserId) {
            return null;
        }

        // Get all messages for this vendor ordered by time
        $messages = \App\Models\Message::where('vendor_id', $vendor->id)
            ->orderBy('created_at')
            ->get(['sender_id', 'created_at', 'from_vendor']);

        if ($messages->count() < 2) {
            return null; // Not enough data
        }

        $responseTimes = [];
        
        // Loop through messages to find vendor replies to client messages
        for ($i = 0; $i < $messages->count() - 1; $i++) {
            $currentMessage = $messages[$i];
            $nextMessage = $messages[$i + 1];
            
            // If current message is from client and next is from vendor
            if (!$currentMessage->from_vendor && $nextMessage->from_vendor) {
                $timeDiff = $nextMessage->created_at->diffInMinutes($currentMessage->created_at);
                if ($timeDiff > 0 && $timeDiff < 10080) { // Less than a week
                    $responseTimes[] = $timeDiff;
                }
            }
        }

        if (empty($responseTimes)) {
            return null;
        }

        $avgMinutes = array_sum($responseTimes) / count($responseTimes);

        // Format the response time
        if ($avgMinutes < 5) {
            return 'a few minutes';
        } elseif ($avgMinutes < 60) {
            return round($avgMinutes) . ' minutes';
        } elseif ($avgMinutes < 120) {
            return 'an hour';
        } elseif ($avgMinutes < 1440) {
            $hours = round($avgMinutes / 60);
            return $hours . ' hours';
        } else {
            $days = round($avgMinutes / 1440);
            return $days . ' ' . ($days == 1 ? 'day' : 'days');
        }
    }
}
