<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorSubscription;
use App\Models\SubscriptionPayment;
use App\Models\AdminSetting;
use App\Models\User;
use App\Services\PaystackService;
use App\Notifications\NewSubscriptionPendingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionPaymentController extends Controller
{
    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Initiate subscription payment
     */
    public function initiatePayment(Request $request, $plan)
    {
        $vendor = Auth::user()->vendor;
        
        // Check if vendor already has an active subscription
        $activeSubscription = VendorSubscription::where('vendor_id', $vendor->id)
            ->where('status', 'active')
            ->first();

        // Define subscription plans
        $plans = [
            'Free' => ['price' => 0, 'duration' => null],
            'Premium' => ['price' => 99, 'duration' => 30],
            'Gold' => ['price' => 249, 'duration' => 90],
        ];

        if (!isset($plans[$plan])) {
            return back()->with('error', 'Invalid subscription plan');
        }

        $planData = $plans[$plan];

        // Free plan - no payment needed
        if ($plan === 'Free') {
            return back()->with('info', 'You are already on the Free plan');
        }

        // Create subscription record
        $subscription = VendorSubscription::create([
            'vendor_id' => $vendor->id,
            'plan' => $plan,
            'price_amount' => $planData['price'],
            'currency' => 'GHS',
            'status' => 'pending', // Will be activated after approval
            'payment_status' => 'pending',
            'approval_status' => 'pending',
            'started_at' => now(),
            'ends_at' => $planData['duration'] ? now()->addDays($planData['duration']) : null,
        ]);

        // Generate unique payment reference
        $reference = PaystackService::generateReference('SUB');
        $subscription->update(['paystack_reference' => $reference]);

        // Create payment record
        SubscriptionPayment::create([
            'vendor_subscription_id' => $subscription->id,
            'paystack_reference' => $reference,
            'payment_status' => 'pending',
            'amount' => $planData['price'],
            'currency' => 'GHS',
            'customer_email' => Auth::user()->email,
        ]);

        // Initialize Paystack payment
        $paymentData = $this->paystackService->initializePayment(
            Auth::user()->email,
            $planData['price'],
            $reference,
            [
                'vendor_id' => $vendor->id,
                'subscription_id' => $subscription->id,
                'plan' => $plan,
                'business_name' => $vendor->business_name,
            ]
        );

        if ($paymentData['success']) {
            return redirect($paymentData['authorization_url']);
        }

        return back()->with('error', $paymentData['message'] ?? 'Payment initialization failed');
    }

    /**
     * Show subscription payment status
     */
    public function status($id)
    {
        $subscription = VendorSubscription::with('vendor', 'payments')
            ->where('vendor_id', Auth::user()->vendor->id)
            ->findOrFail($id);

        return view('vendor.subscriptions.status', compact('subscription'));
    }
}
