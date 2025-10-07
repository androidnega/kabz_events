<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSubscription;
use App\Models\VerificationRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Super Admin dashboard with system-wide statistics.
     */
    public function index(): View
    {
        // System-wide statistics
        $stats = [
            'total_users' => User::count(),
            'total_vendors' => Vendor::count(),
            'verified_vendors' => Vendor::where('is_verified', true)->count(),
            'pending_verifications' => VerificationRequest::where('status', 'pending')->count(),
            'total_services' => Service::count(),
            'active_services' => Service::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'total_reviews' => Review::where('approved', true)->count(),
            'pending_reviews' => Review::where('approved', false)->count(),
            'total_subscriptions' => VendorSubscription::where('status', 'active')->count(),
            'subscription_revenue' => VendorSubscription::where('status', 'active')
                ->where('plan', '!=', 'Free')
                ->sum('price_amount'),
        ];

        // Recent activity
        $recentVendors = Vendor::with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentVerifications = VerificationRequest::with('vendor')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Subscription breakdown
        $subscriptionBreakdown = VendorSubscription::where('status', 'active')
            ->selectRaw('plan, COUNT(*) as count, SUM(price_amount) as revenue')
            ->groupBy('plan')
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentVendors', 'recentVerifications', 'subscriptionBreakdown'));
    }
}
