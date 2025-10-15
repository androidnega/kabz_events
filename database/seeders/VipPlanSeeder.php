<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VipPlan;

class VipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'VIP Bronze',
                'price' => 50.00,
                'duration_days' => 30,
                'image_limit' => 10,
                'free_ads' => 1,
                'priority_level' => 1,
                'description' => 'Perfect for new vendors getting started with VIP features',
                'status' => true,
            ],
            [
                'name' => 'VIP Silver',
                'price' => 120.00,
                'duration_days' => 90,
                'image_limit' => 25,
                'free_ads' => 3,
                'priority_level' => 2,
                'description' => 'Great value for growing businesses - 3 months of VIP access',
                'status' => true,
            ],
            [
                'name' => 'VIP Gold',
                'price' => 400.00,
                'duration_days' => 365,
                'image_limit' => 100,
                'free_ads' => 12,
                'priority_level' => 3,
                'description' => 'Best value! Full year of VIP status with maximum visibility',
                'status' => true,
            ],
            [
                'name' => 'VIP Platinum',
                'price' => 800.00,
                'duration_days' => 365,
                'image_limit' => -1, // Unlimited
                'free_ads' => 24,
                'priority_level' => 4,
                'description' => 'Premium tier with unlimited images and priority support',
                'status' => true,
            ],
        ];

        foreach ($plans as $plan) {
            VipPlan::create($plan);
        }

        $this->command->info('✅ VIP Plans seeded successfully! Created ' . count($plans) . ' plans.');
    }
}
