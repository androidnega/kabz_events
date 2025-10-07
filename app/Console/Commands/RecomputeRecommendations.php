<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorRecommendation;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\DB;

class RecomputeRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recs:recompute {--user=* : Specific user IDs to recompute} {--limit=20 : Number of recommendations per user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recompute vendor recommendations for users based on their activity and preferences.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting recommendation recomputation...');

        $userIds = $this->option('user');
        $limit = (int) $this->option('limit');

        // Get users to process
        if (!empty($userIds)) {
            $users = User::whereIn('id', $userIds)->get();
        } else {
            $users = User::whereNotNull('email')->get();
        }

        if ($users->isEmpty()) {
            $this->warn('No users found to process.');
            return 0;
        }

        $this->info("Processing {$users->count()} users...");
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            try {
                // Get recommendations for this user
                $recs = RecommendationService::get([
                    'user_id' => $user->id,
                    'limit' => $limit,
                ]);

                // Delete old recommendations for this user
                VendorRecommendation::where('user_id', $user->id)->delete();

                // Write new recommendations
                foreach ($recs as $vendor) {
                    VendorRecommendation::create([
                        'user_id' => $user->id,
                        'vendor_id' => $vendor->id,
                        'score' => $vendor->computed_score ?? 0,
                    ]);
                }

                // Clear user's recommendation cache
                RecommendationService::clearCache($user->id);

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nError processing user {$user->id}: {$e->getMessage()}");
                continue;
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✓ Recommendations recomputed successfully!');
        
        // Show stats
        $totalRecs = VendorRecommendation::count();
        $this->info("Total recommendations in database: {$totalRecs}");

        return 0;
    }
}
