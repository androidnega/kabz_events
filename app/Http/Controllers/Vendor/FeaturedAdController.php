<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\FeaturedAd;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FeaturedAdController extends Controller
{
    /**
     * Display vendor's featured ads.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $featuredAds = $vendor->featuredAds()
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $vipSubscription = $vendor->activeVipSubscription();
        $freeAdsRemaining = $vipSubscription ? $vipSubscription->getRemainingFreeAds() : 0;

        return view('vendor.featured-ads.index', compact('featuredAds', 'freeAdsRemaining'));
    }

    /**
     * Show the form for creating a new featured ad.
     */
    public function create()
    {
        $vendor = Auth::user()->vendor;
        $services = $vendor->services()->where('is_active', true)->get();
        
        if ($services->isEmpty()) {
            return redirect()->route('vendor.featured-ads.index')
                ->with('error', 'You need to create at least one active service before creating a featured ad.');
        }

        $vipSubscription = $vendor->activeVipSubscription();
        $freeAdsRemaining = $vipSubscription ? $vipSubscription->getRemainingFreeAds() : 0;

        // Pricing rates per day
        $pricing = [
            'homepage' => 30,
            'category' => 20,
            'search' => 15,
        ];

        return view('vendor.featured-ads.create', compact('services', 'freeAdsRemaining', 'pricing'));
    }

    /**
     * Store a newly created featured ad in storage.
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'placement' => 'required|in:homepage,category,search',
            'duration' => 'required|integer|in:3,7,14,30',
            'start_date' => 'required|date|after_or_equal:today',
            'headline' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'use_free_ad' => 'nullable|boolean',
        ]);

        // Verify service belongs to vendor
        $service = Service::where('id', $validated['service_id'])
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        // Calculate price
        $pricePerDay = [
            'homepage' => 30,
            'category' => 20,
            'search' => 15,
        ];
        $totalPrice = $pricePerDay[$validated['placement']] * $validated['duration'];

        // Handle free ad usage
        $vipSubscription = $vendor->activeVipSubscription();
        $useFreeAd = $request->boolean('use_free_ad') && $vipSubscription && $vipSubscription->hasFreeAds();

        if ($useFreeAd) {
            $totalPrice = 0;
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('featured-ads', 'public');
        }

        // Calculate end date
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = $startDate->copy()->addDays($validated['duration']);

        // Create featured ad
        $featuredAd = FeaturedAd::create([
            'vendor_id' => $vendor->id,
            'service_id' => $validated['service_id'],
            'placement' => $validated['placement'],
            'headline' => $validated['headline'],
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $totalPrice > 0 ? 'pending' : 'active', // Free ads are auto-approved
            'price' => $totalPrice,
        ]);

        // If using free ad, mark it as used
        if ($useFreeAd) {
            $vipSubscription->useFreeAd();
        }

        // If price is 0 (free ad), activate immediately
        if ($totalPrice == 0) {
            return redirect()->route('vendor.featured-ads.index')
                ->with('success', 'Featured ad created and activated successfully!');
        }

        // Otherwise redirect to payment
        return redirect()->route('vendor.featured-ads.payment', $featuredAd->id)
            ->with('success', 'Featured ad created! Please complete payment to activate.');
    }

    /**
     * Show payment page for featured ad.
     */
    public function payment($id)
    {
        $vendor = Auth::user()->vendor;
        $featuredAd = FeaturedAd::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->with('service')
            ->firstOrFail();

        if ($featuredAd->status !== 'pending' || $featuredAd->price <= 0) {
            return redirect()->route('vendor.featured-ads.index')
                ->with('info', 'This featured ad does not require payment.');
        }

        return view('vendor.featured-ads.payment', compact('featuredAd'));
    }

    /**
     * Display the specified featured ad.
     */
    public function show($id)
    {
        $vendor = Auth::user()->vendor;
        $featuredAd = FeaturedAd::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->with('service')
            ->firstOrFail();

        return view('vendor.featured-ads.show', compact('featuredAd'));
    }

    /**
     * Show the form for editing the specified featured ad.
     */
    public function edit($id)
    {
        $vendor = Auth::user()->vendor;
        $featuredAd = FeaturedAd::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->firstOrFail();

        // Can only edit pending ads
        if ($featuredAd->status !== 'pending') {
            return redirect()->route('vendor.featured-ads.index')
                ->with('error', 'Only pending ads can be edited.');
        }

        $services = $vendor->services()->where('is_active', true)->get();

        return view('vendor.featured-ads.edit', compact('featuredAd', 'services'));
    }

    /**
     * Update the specified featured ad in storage.
     */
    public function update(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        $featuredAd = FeaturedAd::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->firstOrFail();

        // Can only edit pending ads
        if ($featuredAd->status !== 'pending') {
            return redirect()->route('vendor.featured-ads.index')
                ->with('error', 'Only pending ads can be edited.');
        }

        $validated = $request->validate([
            'headline' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($featuredAd->image_path) {
                Storage::disk('public')->delete($featuredAd->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('featured-ads', 'public');
        }

        $featuredAd->update($validated);

        return redirect()->route('vendor.featured-ads.index')
            ->with('success', 'Featured ad updated successfully!');
    }

    /**
     * Remove the specified featured ad from storage.
     */
    public function destroy($id)
    {
        $vendor = Auth::user()->vendor;
        $featuredAd = FeaturedAd::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->firstOrFail();

        // Can only delete pending or expired ads
        if (!in_array($featuredAd->status, ['pending', 'expired'])) {
            return back()->with('error', 'Only pending or expired ads can be deleted.');
        }

        // Delete image
        if ($featuredAd->image_path) {
            Storage::disk('public')->delete($featuredAd->image_path);
        }

        $featuredAd->delete();

        return redirect()->route('vendor.featured-ads.index')
            ->with('success', 'Featured ad deleted successfully.');
    }

    /**
     * Verify payment callback (from Paystack).
     */
    public function verifyPayment(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        $featuredAd = FeaturedAd::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->firstOrFail();

        $reference = $request->input('reference');

        // TODO: Verify payment with Paystack API
        // For now, we'll just activate the ad
        
        $featuredAd->update([
            'status' => 'active',
            'payment_ref' => $reference,
        ]);

        return redirect()->route('vendor.featured-ads.index')
            ->with('success', 'Payment successful! Your featured ad is now active.');
    }
}

