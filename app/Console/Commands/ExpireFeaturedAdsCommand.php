<?php

namespace App\Console\Commands;

use App\Models\FeaturedAd;
use Illuminate\Console\Command;

class ExpireFeaturedAdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'featured-ads:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire featured ads that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired featured ads...');

        $expiredAds = FeaturedAd::where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        if ($expiredAds->isEmpty()) {
            $this->info('No expired featured ads found.');
            return 0;
        }

        $count = 0;
        foreach ($expiredAds as $ad) {
            $ad->update(['status' => 'expired']);
            $count++;
            $this->line("Expired: Featured Ad #{$ad->id} - {$ad->headline}");
        }

        $this->info("Successfully expired {$count} featured ad(s).");
        return 0;
    }
}

