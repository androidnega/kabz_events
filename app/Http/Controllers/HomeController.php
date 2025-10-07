<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Vendor;
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
}
