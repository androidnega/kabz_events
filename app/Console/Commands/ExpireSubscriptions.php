<?php

namespace App\Console\Commands;

use App\Models\VendorSubscription;
use App\Models\VipSubscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire vendor subscriptions and VIP subscriptions that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for expired subscriptions...');
        
        // 1. Expire Vendor Subscriptions (legacy system)
        $expiredVendorSubs = VendorSubscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->get();

        $vendorCount = 0;
        foreach ($expiredVendorSubs as $subscription) {
            $subscription->update(['status' => 'expired']);
            $vendorCount++;
            $this->line("  ✓ Expired vendor subscription ID {$subscription->id} ({$subscription->plan})");
        }

        // 2. Expire VIP Subscriptions (new system)
        $expiredVipSubs = VipSubscription::where('status', 'active')
            ->where('end_date', '<', now())
            ->with('vendor', 'vipPlan')
            ->get();

        $vipCount = 0;
        foreach ($expiredVipSubs as $subscription) {
            $subscription->update(['status' => 'expired']);
            $vipCount++;
            
            $vendorName = $subscription->vendor?->business_name ?? 'Unknown';
            $planName = $subscription->vipPlan?->name ?? 'Unknown';
            
            $this->line("  ✓ Expired VIP subscription: {$vendorName} ({$planName})");
            
            // Optional: Send notification to vendor about expiration
            // $this->notifyVendor($subscription);
        }

        // 3. Summary
        $this->newLine();
        if ($vendorCount > 0 || $vipCount > 0) {
            $this->info("✅ Expiration Complete:");
            $this->info("   - Vendor Subscriptions: {$vendorCount} expired");
            $this->info("   - VIP Subscriptions: {$vipCount} expired");
        } else {
            $this->info("✅ No subscriptions to expire. All active subscriptions are still valid.");
        }

        return self::SUCCESS;
    }

    /**
     * Optional: Notify vendor about subscription expiration
     */
    private function notifyVendor(VipSubscription $subscription): void
    {
        // Can add email/SMS notification here
        // Example:
        // $subscription->vendor->user->notify(new VipSubscriptionExpiredNotification($subscription));
    }
}
