<?php

namespace App\Jobs;

use App\Models\VendorSubscription;
use App\Models\FeaturedAd;
use App\Models\AdminSetting;
use App\Notifications\SubscriptionApprovedNotification;
use App\Notifications\FeaturedAdApprovedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoApproveSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if auto-approval is enabled for subscriptions
        $subscriptionAutoApprovalEnabled = AdminSetting::getValue('subscription_auto_approval_enabled', true);
        
        if ($subscriptionAutoApprovalEnabled) {
            $this->autoApproveSubscriptions();
        }

        // Check if auto-approval is enabled for featured ads
        $adAutoApprovalEnabled = AdminSetting::getValue('featured_ad_auto_approval_enabled', true);
        
        if ($adAutoApprovalEnabled) {
            $this->autoApproveFeaturedAds();
        }
    }

    /**
     * Auto-approve pending subscriptions
     */
    protected function autoApproveSubscriptions()
    {
        $subscriptions = VendorSubscription::where('payment_status', 'paid')
            ->where('approval_status', 'pending')
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', now())
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $subscription->approve(null, 'Auto-approved after 24 hours');
                
                // Send notification to vendor
                $subscription->vendor->user->notify(new SubscriptionApprovedNotification($subscription));
                
                Log::info('Subscription auto-approved', [
                    'subscription_id' => $subscription->id,
                    'vendor_id' => $subscription->vendor_id,
                    'plan' => $subscription->plan,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to auto-approve subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Auto-approve pending featured ads
     */
    protected function autoApproveFeaturedAds()
    {
        $ads = FeaturedAd::where('payment_status', 'paid')
            ->where('approval_status', 'pending')
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', now())
            ->get();

        foreach ($ads as $ad) {
            try {
                $ad->approve(null, 'Auto-approved after 24 hours');
                
                // Send notification to vendor
                $ad->vendor->user->notify(new FeaturedAdApprovedNotification($ad));
                
                Log::info('Featured ad auto-approved', [
                    'ad_id' => $ad->id,
                    'vendor_id' => $ad->vendor_id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to auto-approve featured ad', [
                    'ad_id' => $ad->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
