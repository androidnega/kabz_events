<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VendorDashboardController extends Controller
{
    /**
     * Display the vendor dashboard.
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

        // Calculate statistics
        $totalServices = $vendor->services()->count();
        
        // Calculate average rating from reviews
        $averageRating = $vendor->reviews()
            ->where('approved', true)
            ->avg('rating') ?? 0;
        
        $totalReviews = $vendor->reviews()
            ->where('approved', true)
            ->count();

        // Get verification status
        $isVerified = $vendor->is_verified;
        $verificationStatus = $isVerified ? 'Verified' : 'Pending';

        // Subscription status (stub for future)
        $subscriptionStatus = 'Free Plan'; // Will be dynamic in future

        return view('vendor.dashboard', compact(
            'vendor',
            'totalServices',
            'averageRating',
            'totalReviews',
            'verificationStatus',
            'subscriptionStatus'
        ));
    }
}
