<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorSubscription;
use App\Models\FeaturedAd;
use App\Notifications\SubscriptionApprovedNotification;
use App\Notifications\FeaturedAdApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionApprovalController extends Controller
{
    /**
     * Show pending subscriptions
     */
    public function pendingSubscriptions()
    {
        $subscriptions = VendorSubscription::with(['vendor.user', 'payments'])
            ->where('payment_status', 'paid')
            ->where('approval_status', 'pending')
            ->orderBy('paid_at', 'desc')
            ->paginate(20);

        return view('admin.subscriptions.pending', compact('subscriptions'));
    }

    /**
     * Show pending featured ads
     */
    public function pendingFeaturedAds()
    {
        $ads = FeaturedAd::with(['vendor.user', 'service'])
            ->where('payment_status', 'paid')
            ->where('approval_status', 'pending')
            ->orderBy('paid_at', 'desc')
            ->paginate(20);

        return view('admin.featured-ads.pending', compact('ads'));
    }

    /**
     * Approve a subscription
     */
    public function approveSubscription(Request $request, $id)
    {
        $subscription = VendorSubscription::findOrFail($id);

        $validated = $request->validate([
            'approval_note' => 'nullable|string|max:500',
        ]);

        $subscription->approve(Auth::id(), $validated['approval_note'] ?? null);

        // Deactivate any other active subscriptions for this vendor
        VendorSubscription::where('vendor_id', $subscription->vendor_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        // Send notification to vendor
        $subscription->vendor->user->notify(new SubscriptionApprovedNotification($subscription));

        return back()->with('success', 'Subscription approved successfully');
    }

    /**
     * Reject a subscription
     */
    public function rejectSubscription(Request $request, $id)
    {
        $subscription = VendorSubscription::findOrFail($id);

        $validated = $request->validate([
            'approval_note' => 'required|string|max:500',
        ]);

        $subscription->reject(Auth::id(), $validated['approval_note']);

        // Optionally refund or keep payment based on business logic
        // For now, we'll keep the payment but mark subscription as rejected

        return back()->with('success', 'Subscription rejected');
    }

    /**
     * Approve a featured ad
     */
    public function approveFeaturedAd(Request $request, $id)
    {
        $ad = FeaturedAd::findOrFail($id);

        $validated = $request->validate([
            'approval_note' => 'nullable|string|max:500',
        ]);

        $ad->approve(Auth::id(), $validated['approval_note'] ?? null);

        // Send notification to vendor
        $ad->vendor->user->notify(new FeaturedAdApprovedNotification($ad));

        return back()->with('success', 'Featured ad approved successfully');
    }

    /**
     * Reject a featured ad
     */
    public function rejectFeaturedAd(Request $request, $id)
    {
        $ad = FeaturedAd::findOrFail($id);

        $validated = $request->validate([
            'approval_note' => 'required|string|max:500',
        ]);

        $ad->reject(Auth::id(), $validated['approval_note']);

        return back()->with('success', 'Featured ad rejected');
    }
}
