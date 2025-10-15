<?php

namespace App\Http\Controllers;

use App\Models\VendorSubscription;
use App\Models\SubscriptionPayment;
use App\Models\FeaturedAd;
use App\Models\AdminSetting;
use App\Models\User;
use App\Services\PaystackService;
use App\Notifications\NewSubscriptionPendingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackCallbackController extends Controller
{
    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Handle Paystack payment callback
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment reference');
        }

        // Verify payment with Paystack
        $verificationResult = $this->paystackService->verifyPayment($reference);

        if (!$verificationResult['success']) {
            return redirect()->route('dashboard')->with('error', 'Payment verification failed');
        }

        $paymentData = $verificationResult['data'];

        // Check if payment was successful
        if ($paymentData['status'] !== 'success') {
            return redirect()->route('dashboard')->with('error', 'Payment was not successful');
        }

        // Determine if this is a subscription or featured ad payment
        $metadata = $paymentData['metadata'] ?? [];
        
        if (isset($metadata['subscription_id'])) {
            return $this->handleSubscriptionPayment($reference, $paymentData, $metadata);
        } elseif (isset($metadata['ad_id'])) {
            return $this->handleFeaturedAdPayment($reference, $paymentData, $metadata);
        }

        return redirect()->route('dashboard')->with('error', 'Unable to process payment');
    }

    /**
     * Handle subscription payment
     */
    protected function handleSubscriptionPayment($reference, $paymentData, $metadata)
    {
        $subscription = VendorSubscription::where('id', $metadata['subscription_id'])
            ->where('paystack_reference', $reference)
            ->first();

        if (!$subscription) {
            Log::error('Subscription not found for payment', ['reference' => $reference]);
            return redirect()->route('dashboard')->with('error', 'Subscription not found');
        }

        // Update subscription payment status
        $subscription->update([
            'payment_status' => 'paid',
            'payment_method' => 'paystack',
            'paid_at' => now(),
            'payment_expires_at' => now()->addHours(AdminSetting::getValue('subscription_auto_approval_hours', 24)),
        ]);

        // Update payment record
        $payment = SubscriptionPayment::where('paystack_reference', $reference)->first();
        if ($payment) {
            $payment->markAsPaid($paymentData['channel'] ?? null);
            $payment->update([
                'metadata' => json_encode($paymentData),
            ]);
        }

        // Notify all admins
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new NewSubscriptionPendingNotification($subscription));
        }

        Log::info('Subscription payment successful', [
            'subscription_id' => $subscription->id,
            'reference' => $reference,
            'amount' => $paymentData['amount'] / 100,
        ]);

        return redirect()->route('vendor.subscriptions.status', $subscription->id)
            ->with('success', 'Payment successful! Your subscription is under review and will be activated within 24 hours.');
    }

    /**
     * Handle featured ad payment
     */
    protected function handleFeaturedAdPayment($reference, $paymentData, $metadata)
    {
        $ad = FeaturedAd::where('id', $metadata['ad_id'])
            ->where('paystack_reference', $reference)
            ->first();

        if (!$ad) {
            Log::error('Featured ad not found for payment', ['reference' => $reference]);
            return redirect()->route('dashboard')->with('error', 'Featured ad not found');
        }

        // Update ad payment status
        $ad->update([
            'payment_status' => 'paid',
            'payment_method' => 'paystack',
            'paid_at' => now(),
            'payment_expires_at' => now()->addHours(AdminSetting::getValue('featured_ad_auto_approval_hours', 24)),
        ]);

        // Notify all admins (similar implementation)
        $admins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->get();

        foreach ($admins as $admin) {
            // Create similar notification for featured ads
            // $admin->notify(new NewFeaturedAdPendingNotification($ad));
        }

        Log::info('Featured ad payment successful', [
            'ad_id' => $ad->id,
            'reference' => $reference,
            'amount' => $paymentData['amount'] / 100,
        ]);

        return redirect()->route('vendor.featured-ads.status', $ad->id)
            ->with('success', 'Payment successful! Your featured ad is under review and will be activated within 24 hours.');
    }
}
