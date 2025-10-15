<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Recompute vendor recommendations daily at 3 AM
        $schedule->command('recs:recompute')
            ->dailyAt('03:00')
            ->withoutOverlapping()
            ->runInBackground();
        
        // Set inactive users offline every minute
        $schedule->command('users:set-offline')
            ->everyMinute()
            ->withoutOverlapping();
        
        // Expire featured ads daily at 1 AM
        $schedule->command('featured-ads:expire')
            ->dailyAt('01:00')
            ->withoutOverlapping();
        
        // Expire all subscriptions (VIP + Vendor) daily at 2 AM - Dynamic expiration
        $schedule->command('subscriptions:expire')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
