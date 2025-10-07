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
     * Subscribe to a plan.
     */
    public function subscribe($plan)
    {
        $vendor = Auth::user()->vendor;

        // Define plan details
        $planDetails = [
            'Free' => ['price' => 0, 'days' => null],
            'Premium' => ['price' => 99, 'days' => 30],
            'Gold' => ['price' => 249, 'days' => 90],
        ];

        // Validate plan
        if (!isset($planDetails[$plan])) {
            return back()->with('error', 'Invalid subscription plan selected.');
        }

        $details = $planDetails[$plan];

        // Calculate end date
        $endsAt = $details['days'] !== null ? now()->addDays($details['days']) : null;

        // Create subscription
        VendorSubscription::create([
            'vendor_id' => $vendor->id,
            'plan' => $plan,
            'price_amount' => $details['price'],
            'currency' => 'GHS',
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => $endsAt,
            'payment_reference' => 'TEST_' . Str::uuid(),
        ]);

        return back()->with('success', $plan . ' plan activated successfully! (Test Mode)');
    }
}
