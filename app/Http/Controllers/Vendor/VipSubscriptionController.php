<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VipPlan;
use App\Models\VipSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VipSubscriptionController extends Controller
{
    /**
     * Display available VIP plans and current subscription.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $plans = VipPlan::where('status', true)
            ->orderBy('price', 'asc')
            ->get();

        $activeSubscription = $vendor->activeVipSubscription();
        
        // Get all vendor's VIP subscriptions
        $subscriptions = $vendor->vipSubscriptions()
            ->with('vipPlan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.vip-subscriptions.index', compact('plans', 'activeSubscription', 'subscriptions'));
    }

    /**
     * Show subscription checkout page.
     */
    public function subscribe($planId)
    {
        $vendor = Auth::user()->vendor;
        $plan = VipPlan::where('status', true)->findOrFail($planId);

        // Check if already has active VIP subscription
        $activeSubscription = $vendor->activeVipSubscription();
        if ($activeSubscription) {
            return redirect()->route('vendor.vip-subscriptions.index')
                ->with('info', 'You already have an active VIP subscription. Wait for it to expire before subscribing to a new plan.');
        }

        return view('vendor.vip-subscriptions.subscribe', compact('plan'));
    }

    /**
     * Process VIP subscription purchase.
     */
    public function processSubscription(Request $request, $planId)
    {
        $vendor = Auth::user()->vendor;
        $plan = VipPlan::where('status', true)->findOrFail($planId);

        // Check if already has active VIP subscription
        $activeSubscription = $vendor->activeVipSubscription();
        if ($activeSubscription) {
            return redirect()->route('vendor.vip-subscriptions.index')
                ->with('error', 'You already have an active VIP subscription.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:paystack',
            'agree_terms' => 'required|accepted',
        ]);

        // Calculate dates
        $startDate = now();
        $endDate = now()->addDays($plan->duration_days);

        // Create VIP subscription (pending payment)
        $subscription = VipSubscription::create([
            'vendor_id' => $vendor->id,
            'vip_plan_id' => $plan->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
        ]);

        // Redirect to payment page
        return redirect()->route('vendor.vip-subscriptions.payment', $subscription->id)
            ->with('success', 'VIP subscription created! Please complete payment to activate.');
    }

    /**
     * Show payment page for VIP subscription.
     */
    public function payment($id)
    {
        $vendor = Auth::user()->vendor;
        $subscription = VipSubscription::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->with('vipPlan')
            ->firstOrFail();

        if ($subscription->status === 'active') {
            return redirect()->route('vendor.vip-subscriptions.index')
                ->with('info', 'This subscription is already active.');
        }

        return view('vendor.vip-subscriptions.payment', compact('subscription'));
    }

    /**
     * Verify payment callback (from Paystack).
     */
    public function verifyPayment(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        $subscription = VipSubscription::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->firstOrFail();

        $reference = $request->input('reference');

        // TODO: Verify payment with Paystack API
        // For now, we'll just activate the subscription
        
        $subscription->update([
            'status' => 'active',
            'payment_ref' => $reference,
        ]);

        return redirect()->route('vendor.vip-subscriptions.index')
            ->with('success', 'Payment successful! Your VIP subscription is now active. 🎉');
    }

    /**
     * Cancel VIP subscription.
     */
    public function cancel($id)
    {
        $vendor = Auth::user()->vendor;
        $subscription = VipSubscription::where('vendor_id', $vendor->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($subscription->status === 'cancelled') {
            return back()->with('info', 'This subscription is already cancelled.');
        }

        $subscription->update(['status' => 'cancelled']);

        return redirect()->route('vendor.vip-subscriptions.index')
            ->with('success', 'VIP subscription cancelled successfully.');
    }
}

