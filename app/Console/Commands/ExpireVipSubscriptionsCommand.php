<?php

namespace App\Console\Commands;

use App\Models\VipSubscription;
use Illuminate\Console\Command;

class ExpireVipSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vip-subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire VIP subscriptions that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired VIP subscriptions...');

        $expiredSubscriptions = VipSubscription::where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired VIP subscriptions found.');
            return 0;
        }

        $count = 0;
        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            $count++;
            $this->line("Expired: VIP Subscription #{$subscription->id} for Vendor #{$subscription->vendor_id}");
        }

        $this->info("Successfully expired {$count} VIP subscription(s).");
        return 0;
    }
}

