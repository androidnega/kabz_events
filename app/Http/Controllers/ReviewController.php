<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admin can view all reviews (future phase)
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not needed - form is on vendor profile page
        //
    }

    /**
     * Store a newly created review for a vendor.
     */
    public function store(Request $request, Vendor $vendor): RedirectResponse
    {
        // Check if user already reviewed this vendor
        $existingReview = Review::where('vendor_id', $vendor->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this vendor.');
        }

        // Validate the review
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|max:2000',
            'event_date' => 'nullable|date',
        ]);

        // Create the review
        $review = Review::create([
            'vendor_id' => $vendor->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'event_date' => $validated['event_date'] ?? null,
            'approved' => false, // Requires admin approval
        ]);

        // Update vendor's cached rating
        $this->updateVendorRating($vendor);

        return back()->with('success', 'Thank you! Your review has been submitted and is pending approval.');
    }

    /**
     * Update vendor's cached rating.
     */
    private function updateVendorRating(Vendor $vendor): void
    {
        $averageRating = $vendor->reviews()
            ->where('approved', true)
            ->avg('rating') ?? 0;

        $vendor->update(['rating_cached' => $averageRating]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        //
    }
}
