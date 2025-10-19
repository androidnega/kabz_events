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

        // Get vendor counts by location for the location modal
        $locationCounts = $this->getVendorLocationCounts();

        return view('home', compact('categories', 'featuredVendors', 'featuredAds', 'appearance', 'locationCounts'));
    }

    /**
     * Get vendor counts grouped by region and district/town.
     */
    private function getVendorLocationCounts(): array
    {
        $vendors = Vendor::where('is_verified', true)
            ->select('address', 'district')
            ->get();

        $locationCounts = [
            'regions' => [],
            'towns' => [],
        ];

        // Ghana regions mapping
        $regionKeywords = [
            'Greater Accra' => ['accra', 'tema', 'ga east', 'ga west', 'ga south', 'adenta', 'ashiaman', 'dome', 'madina', 'kasoa', 'spintex', 'east legon', 'osu', 'labone'],
            'Ashanti' => ['kumasi', 'obuasi', 'ejisu', 'mampong', 'asokore', 'bantama', 'suame', 'adum', 'kejetia', 'asafo'],
            'Western' => ['sekondi', 'takoradi', 'tarkwa', 'prestea', 'axim', 'effiakuma'],
            'Central' => ['cape coast', 'winneba', 'agona swedru', 'elmina', 'mankessim'],
            'Northern' => ['tamale', 'yendi', 'savelugu', 'gumani', 'tolon', 'kpandai'],
            'Eastern' => ['koforidua', 'new juaben', 'akropong', 'nsawam', 'suhum', 'akim oda', 'mpraeso'],
            'Volta' => ['ho', 'hohoe', 'keta', 'aflao', 'sogakope', 'denu', 'kpando'],
            'Upper East' => ['bolgatanga', 'bongo', 'navrongo', 'bawku', 'paga'],
            'Upper West' => ['wa', 'wechiau', 'lawra', 'jirapa', 'tumu'],
            'Bono' => ['sunyani', 'berekum', 'techiman', 'wenchi', 'dormaa ahenkro'],
        ];

        foreach ($vendors as $vendor) {
            $address = strtolower($vendor->address ?? '');
            $district = strtolower($vendor->district ?? '');

            foreach ($regionKeywords as $region => $keywords) {
                foreach ($keywords as $keyword) {
                    if (stripos($address, $keyword) !== false || stripos($district, $keyword) !== false) {
                        // Count for region
                        if (!isset($locationCounts['regions'][$region])) {
                            $locationCounts['regions'][$region] = 0;
                        }
                        $locationCounts['regions'][$region]++;

                        // Count for town (use district or first keyword match)
                        $town = ucwords($keyword);
                        $townKey = $region . '|' . $town;
                        if (!isset($locationCounts['towns'][$townKey])) {
                            $locationCounts['towns'][$townKey] = 0;
                        }
                        $locationCounts['towns'][$townKey]++;
                        
                        break 2; // Found match, move to next vendor
                    }
                }
            }
        }

        return $locationCounts;
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
