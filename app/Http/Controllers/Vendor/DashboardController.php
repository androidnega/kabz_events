<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Vendor dashboard with business statistics.
     */
    public function index(): View|RedirectResponse
    {
        // Get the authenticated user's vendor profile
        $vendor = Auth::user()->vendor;

        // If user doesn't have a vendor profile, redirect to registration
        if (!$vendor) {
            return redirect()
                ->route('vendor.register')
                ->with('info', 'Please create your vendor profile first.');
        }

        // Business statistics
        $stats = [
            'total_services' => $vendor->services()->count(),
            'active_services' => $vendor->services()->where('is_active', true)->count(),
            'total_reviews' => $vendor->reviews()->where('approved', true)->count(),
            'average_rating' => round($vendor->rating_cached ?? 0, 1),
            'is_verified' => $vendor->is_verified,
            'verification_status' => $vendor->is_verified ? 'Verified ✓' : 'Not Verified',
        ];

        // Subscription information
        $activeSubscription = $vendor->activeSubscription();
        $subscriptionInfo = null;

        if ($activeSubscription) {
            $subscriptionInfo = [
                'plan' => $activeSubscription->plan,
                'status' => $activeSubscription->status,
                'started_at' => $activeSubscription->started_at,
                'ends_at' => $activeSubscription->ends_at,
                'is_active' => $activeSubscription->isActive(),
                'days_remaining' => $activeSubscription->ends_at ? now()->diffInDays($activeSubscription->ends_at, false) : null,
            ];
        }

        // Verification request status
        $verificationRequest = $vendor->verificationRequest;

        // Recent reviews
        $recentReviews = $vendor->reviews()
            ->with('user')
            ->where('approved', true)
            ->latest()
            ->take(5)
            ->get();

        // Recent services
        $recentServices = $vendor->services()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        // Check if vendor needs tour (first time)
        $showTour = !$vendor->tour_completed;

        return view('vendor.dashboard', compact(
            'vendor',
            'stats',
            'subscriptionInfo',
            'verificationRequest',
            'recentReviews',
            'recentServices',
            'showTour'
        ));
    }

    /**
     * Mark the vendor tour as completed
     */
    public function completeTour(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        if ($vendor) {
            $vendor->update(['tour_completed' => true]);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }

    /**
     * Restart the vendor tour (from settings)
     */
    public function restartTour(): RedirectResponse
    {
        $vendor = Auth::user()->vendor;
        
        if ($vendor) {
            $vendor->update(['tour_completed' => false]);
            return redirect()
                ->route('vendor.dashboard')
                ->with('success', 'Tutorial will start now!');
        }
        
        return redirect()->back()->with('error', 'Unable to restart tutorial.');
    }
}

