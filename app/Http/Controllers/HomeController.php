<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function index(): View
    {
        // Get active featured ads for homepage
        $featuredAds = \App\Models\FeaturedAd::with(['vendor', 'service'])
            ->where('status', 'active')
            ->where('placement', 'homepage')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get top 10 categories ordered by display_order
        $categories = Category::orderBy('display_order')
            ->take(10)
            ->get();

        // Get featured vendors with VIP priority (VIP > Subscribed > Verified > Unverified)
        $featuredVendors = Vendor::with(['services.category', 'subscriptions', 'vipSubscriptions.vipPlan'])
            ->select('vendors.*')
            ->leftJoin('vendor_subscriptions', function ($join) {
                $join->on('vendors.id', '=', 'vendor_subscriptions.vendor_id')
                     ->where('vendor_subscriptions.status', '=', 'active')
                     ->where(function ($q) {
                         $q->whereNull('vendor_subscriptions.ends_at')
                           ->orWhere('vendor_subscriptions.ends_at', '>=', now());
                     });
            })
            ->leftJoin('vip_subscriptions', function ($join) {
                $join->on('vendors.id', '=', 'vip_subscriptions.vendor_id')
                     ->where('vip_subscriptions.status', '=', 'active')
                     ->where('vip_subscriptions.start_date', '<=', now())
                     ->where('vip_subscriptions.end_date', '>=', now());
            })
            ->leftJoin('vip_plans', 'vip_subscriptions.vip_plan_id', '=', 'vip_plans.id')
            ->selectRaw('vendors.*, 
                CASE 
                    WHEN vip_plans.priority_level IS NOT NULL THEN (10 + vip_plans.priority_level)
                    WHEN vendor_subscriptions.id IS NOT NULL THEN 3
                    WHEN vendors.is_verified = 1 THEN 2
                    ELSE 1
                END as priority_score')
            ->orderByDesc('priority_score')
            ->orderByDesc('rating_cached')
            ->take(6)
            ->get();

        return view('home', compact('categories', 'featuredVendors', 'featuredAds'));
    }

    /**
     * Load more vendors for infinite scroll.
     */
    public function loadMoreVendors(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        // Show ALL vendors with VIP priority sorting
        $vendors = Vendor::with(['services.category', 'subscriptions', 'vipSubscriptions.vipPlan'])
            ->select('vendors.*')
            ->leftJoin('vendor_subscriptions', function ($join) {
                $join->on('vendors.id', '=', 'vendor_subscriptions.vendor_id')
                     ->where('vendor_subscriptions.status', '=', 'active')
                     ->where(function ($q) {
                         $q->whereNull('vendor_subscriptions.ends_at')
                           ->orWhere('vendor_subscriptions.ends_at', '>=', now());
                     });
            })
            ->leftJoin('vip_subscriptions', function ($join) {
                $join->on('vendors.id', '=', 'vip_subscriptions.vendor_id')
                     ->where('vip_subscriptions.status', '=', 'active')
                     ->where('vip_subscriptions.start_date', '<=', now())
                     ->where('vip_subscriptions.end_date', '>=', now());
            })
            ->leftJoin('vip_plans', 'vip_subscriptions.vip_plan_id', '=', 'vip_plans.id')
            ->selectRaw('vendors.*, 
                CASE 
                    WHEN vip_plans.priority_level IS NOT NULL THEN (10 + vip_plans.priority_level)
                    WHEN vendor_subscriptions.id IS NOT NULL THEN 3
                    WHEN vendors.is_verified = 1 THEN 2
                    ELSE 1
                END as priority_score')
            ->orderByDesc('priority_score')
            ->orderByDesc('rating_cached')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $html = '';
        foreach ($vendors as $vendor) {
            $html .= view('components.vendor-card-infinite', compact('vendor'))->render();
        }

        return response()->json([
            'html' => $html,
            'hasMore' => $vendors->count() === $perPage,
            'page' => $page + 1
        ]);
    }
}
