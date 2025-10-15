<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /**
     * Display available subscription plans.
     */
    public function index()
    {
        $plans = [
            [
                'plan' => 'Free',
                'price_amount' => 0,
                'duration' => 'Lifetime',
                'features' => [
                    'Basic vendor listing',
                    'Up to 5 services',
                    'Basic profile page',
                    'Standard search visibility',
                ],
            ],
            [
                'plan' => 'Premium',
                'price_amount' => 99,
                'duration' => '30 Days',
                'features' => [
                    'Highlighted vendor card',
                    'Top of category listings',
                    'Up to 15 services',
                    'Priority in search results',
                    'Premium Vendor badge',
                ],
            ],
            [
                'plan' => 'Gold',
                'price_amount' => 249,
                'duration' => '90 Days',
                'features' => [
                    'Featured on homepage',
                    'Top position in all listings',
                    'Unlimited services',
                    'Verified badge included',
                    'Analytics dashboard',
                    'Gold Vendor ⭐ badge',
                    'Priority support',
                ],
            ],
        ];

        $vendor = Auth::user()->vendor;
        $active = $vendor->activeSubscription();

        return view('vendor.subscriptions.index', compact('plans', 'active'));
    }

    /**
     * Subscribe to a plan (Redirect to payment flow).
     */
    public function subscribe(Request $request, $plan)
    {
        // Redirect to new payment flow
        if ($plan === 'Free') {
            return back()->with('info', 'You are already on the Free plan');
        }

        // Redirect to payment initiation
        return app(\App\Http\Controllers\Vendor\SubscriptionPaymentController::class)
            ->initiatePayment($request, $plan);
    }
}

