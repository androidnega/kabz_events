<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Note: This seeder is for reference purposes.
     * Subscription plans are currently hardcoded in the SubscriptionController.
     * If you later create a separate 'subscription_plans' table, you can use this seeder.
     */
    public function run(): void
    {
        // Ghana Subscription Plans (GHS - Ghana Cedis)
        $plans = [
            [
                'name' => 'Free',
                'price' => 0.00,
                'currency' => 'GHS',
                'duration_days' => null, // Lifetime
                'features' => [
                    'Basic vendor listing',
                    'Up to 5 services',
                    'Basic profile page',
                    'Standard search visibility',
                ],
                'description' => 'Perfect for getting started',
            ],
            [
                'name' => 'Premium',
                'price' => 99.00,
                'currency' => 'GHS',
                'duration_days' => 30,
                'features' => [
                    'Highlighted vendor card',
                    'Top of category listings',
                    'Up to 15 services',
                    'Priority in search results',
                    'Premium Vendor badge',
                ],
                'description' => 'Best for growing businesses',
            ],
            [
                'name' => 'Gold',
                'price' => 249.00,
                'currency' => 'GHS',
                'duration_days' => 90,
                'features' => [
                    'Featured on homepage',
                    'Top position in all listings',
                    'Unlimited services',
                    'Verified badge included',
                    'Analytics dashboard',
                    'Gold Vendor ⭐ badge',
                    'Priority support',
                ],
                'description' => 'Maximum visibility and features',
            ],
        ];

        // If you create a 'subscription_plans' table, uncomment below:
        // DB::table('subscription_plans')->insert($plans);

        // For now, plans are defined in:
        // app/Http/Controllers/Vendor/SubscriptionController.php

        $this->command->info('✅ Subscription plans reference data loaded');
        $this->command->info('📋 Plans are currently hardcoded in SubscriptionController');
        $this->command->warn('💡 Create a separate plans table if you need database-driven plan management');
    }
}
