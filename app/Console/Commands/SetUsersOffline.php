<?php

namespace App\Console\Commands;

use App\Models\UserOnlineStatus;
use Illuminate\Console\Command;

class SetUsersOffline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:set-offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set users offline if they have been inactive for more than 5 minutes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Set users offline if last activity was more than 5 minutes ago
        $fiveMinutesAgo = now()->subMinutes(5);
        
        $updated = UserOnlineStatus::where('is_online', true)
            ->where('last_seen_at', '<', $fiveMinutesAgo)
            ->update(['is_online' => false]);

        $this->info("Set {$updated} users offline.");

        return 0;
    }
}

