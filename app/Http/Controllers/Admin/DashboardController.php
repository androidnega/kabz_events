<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSubscription;
use App\Models\VerificationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin dashboard with analytics and statistics.
     */
    public function index(): View
    {
        // Core statistics
        $stats = [
            'total_vendors' => Vendor::count(),
            'verified_vendors' => Vendor::where('is_verified', true)->count(),
            'unverified_vendors' => Vendor::where('is_verified', false)->count(),
            'total_clients' => User::role('client')->count(),
            'pending_verifications' => VerificationRequest::where('status', 'pending')->count(),
            'total_services' => Service::count(),
            'active_services' => Service::where('is_active', true)->count(),
            'total_reviews' => Review::count(),
            'pending_reviews' => Review::where('approved', false)->count(),
            'active_subscriptions' => VendorSubscription::where('status', 'active')->count(),
            'total_revenue' => VendorSubscription::where('status', 'active')
                ->where('plan', '!=', 'Free')
                ->sum('price_amount'),
        ];

        // Monthly growth analytics (last 12 months)
        $monthlyStats = [
            'vendors' => Vendor::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray(),
            'clients' => User::role('client')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray(),
        ];

        // Fill in missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($monthlyStats['vendors'][$i])) {
                $monthlyStats['vendors'][$i] = 0;
            }
            if (!isset($monthlyStats['clients'][$i])) {
                $monthlyStats['clients'][$i] = 0;
            }
        }

        ksort($monthlyStats['vendors']);
        ksort($monthlyStats['clients']);

        // Recent verification requests
        $pendingVerifications = VerificationRequest::with('vendor')
            ->where('status', 'pending')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // Recent vendors
        $recentVendors = Vendor::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Top rated vendors
        $topRatedVendors = Vendor::where('is_verified', true)
            ->where('rating_cached', '>', 0)
            ->orderByDesc('rating_cached')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'monthlyStats', 'pendingVerifications', 'recentVendors', 'topRatedVendors'));
    }
}

