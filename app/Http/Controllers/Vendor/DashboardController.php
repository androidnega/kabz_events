<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
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

        return view('vendor.dashboard', compact(
            'vendor',
            'stats',
            'subscriptionInfo',
            'verificationRequest',
            'recentReviews',
            'recentServices'
        ));
    }
}

