<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Client dashboard with activity and recommendations.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Client statistics
        $stats = [
            'total_reviews' => Review::where('user_id', $user->id)->count(),
            'pending_reviews' => Review::where('user_id', $user->id)
                ->where('approved', false)
                ->count(),
            'approved_reviews' => Review::where('user_id', $user->id)
                ->where('approved', true)
                ->count(),
        ];

        // Recent reviews by this client
        $myReviews = Review::where('user_id', $user->id)
            ->with('vendor')
            ->latest()
            ->take(5)
            ->get();

        // Recommended vendors (top rated, verified)
        $recommendedVendors = Vendor::where('is_verified', true)
            ->where('rating_cached', '>', 4)
            ->orderByDesc('rating_cached')
            ->take(6)
            ->get();

        // Recently reviewed vendors
        $reviewedVendors = Vendor::whereHas('reviews', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['reviews' => function ($query) use ($user) {
                $query->where('user_id', $user->id)->latest();
            }])
            ->take(5)
            ->get();

        return view('client.dashboard', compact('stats', 'myReviews', 'recommendedVendors', 'reviewedVendors'));
    }
}
