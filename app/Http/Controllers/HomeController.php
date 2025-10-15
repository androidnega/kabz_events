<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Vendor;
use App\Services\SettingsService;
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
        // Get appearance settings
        $appearance = [
            'hero_title' => SettingsService::get('hero_title', 'Find Perfect Event Vendors in Ghana'),
            'hero_subtitle' => SettingsService::get('hero_subtitle', 'Connect with verified service providers'),
            'hero_bg_type' => SettingsService::get('hero_bg_type', 'gradient'),
            'hero_bg_image' => SettingsService::get('hero_bg_image'),
            'primary_color' => SettingsService::get('primary_color', '#9333ea'),
            'secondary_color' => SettingsService::get('secondary_color', '#a855f7'),
        ];

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

        // Get featured vendors with VIP priority (dynamic ranking via service)
        $featuredVendors = Vendor::with(['services.category', 'subscriptions', 'vipSubscriptions.vipPlan'])
            ->rankedWithSort()
            ->take(6)
            ->get();

        return view('home', compact('categories', 'featuredVendors', 'featuredAds', 'appearance'));
    }

    /**
     * Load more vendors for infinite scroll.
     */
    public function loadMoreVendors(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = 6;
        $offset = ($page - 1) * $perPage;

        // Show ALL vendors with VIP priority sorting (dynamic via service)
        $vendors = Vendor::with(['services.category', 'subscriptions', 'vipSubscriptions.vipPlan'])
            ->rankedWithSort()
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
