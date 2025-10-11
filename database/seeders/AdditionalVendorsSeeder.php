<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Service;
use App\Models\Category;

class AdditionalVendorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 additional vendors for testing infinite scroll
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => 'Test Vendor ' . $i,
                'email' => 'vendor' . $i . '@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]);
            
            $user->assignRole('vendor');
            
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'business_name' => 'Professional Event Services ' . $i,
                'slug' => 'professional-event-services-' . $i,
                'description' => 'Professional event planning and coordination services for all occasions. We specialize in creating memorable experiences.',
                'phone' => '+233' . rand(200000000, 999999999),
                'address' => 'Accra, Ghana',
                'is_verified' => true,
                'rating_cached' => rand(35, 50) / 10,
                'sample_work_title' => 'Our Portfolio',
                'sample_work_images' => [
                    'https://images.unsplash.com/photo-1519741497674-611481863552?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=800&h=600&fit=crop'
                ]
            ]);
            
            // Create a service for each vendor
            $category = Category::inRandomOrder()->first();
            Service::create([
                'vendor_id' => $vendor->id,
                'category_id' => $category->id,
                'title' => 'Event Planning Service',
                'description' => 'Complete event planning and coordination',
                'price_min' => rand(500, 2000),
                'price_max' => rand(2000, 5000),
                'pricing_type' => 'fixed',
                'is_active' => true
            ]);
        }
        
        $this->command->info('Created 10 additional vendors for testing infinite scroll');
    }
}
