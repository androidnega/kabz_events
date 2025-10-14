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
        // Get top 10 categories ordered by display_order
        $categories = Category::orderBy('display_order')
            ->take(10)
            ->get();

        // Get featured vendors (ALL vendors with priority: Subscribed > Verified > Unverified)
        $featuredVendors = Vendor::with(['services.category', 'subscriptions'])
            ->select('vendors.*')
            ->leftJoin('vendor_subscriptions', function ($join) {
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
                END as priority_score')
            ->orderByDesc('priority_score')
            ->orderByDesc('rating_cached')
            ->take(6)
            ->get();

        return view('home', compact('categories', 'featuredVendors'));
    }

    /**
     * Load more vendors for infinite scroll.
     */
    public function loadMoreVendors(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        // Show ALL vendors with priority sorting
        $vendors = Vendor::with(['services.category', 'subscriptions'])
            ->select('vendors.*')
            ->leftJoin('vendor_subscriptions', function ($join) {
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
