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

        // Get featured vendors (verified, ordered by rating)
        $featuredVendors = Vendor::where('is_verified', true)
            ->orderBy('rating_cached', 'desc')
            ->take(6)
            ->with('services.category') // Eager load services and their categories
            ->get();

        // If no featured vendors, get latest verified vendors
        if ($featuredVendors->isEmpty()) {
            $featuredVendors = Vendor::where('is_verified', true)
                ->latest()
                ->take(6)
                ->with('services.category')
                ->get();
        }

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

        $vendors = Vendor::where('is_verified', true)
            ->orderBy('rating_cached', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->with('services.category')
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
