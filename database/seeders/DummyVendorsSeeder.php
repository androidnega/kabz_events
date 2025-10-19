<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Region;
use App\Models\Town;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyVendorsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $regions = Region::with('districts.towns')->get();

        if ($regions->isEmpty()) {
            $this->command->error('No regions found! Run GhanaLocationsSeeder first.');
            return;
        }

        if ($categories->isEmpty()) {
            $this->command->error('No categories found! Run CategorySeeder first.');
            return;
        }

        // Realistic Ghanaian business name prefixes
        $businessPrefixes = [
            'Royal', 'Golden', 'Premium', 'Classic', 'Elite', 'Star', 'Crown', 'Diamond',
            'Perfect', 'Ultimate', 'Elegant', 'Divine', 'Supreme', 'Majestic', 'Grand',
            'Excellence', 'Quality', 'Professional', 'Exquisite', 'Blissful', 'Splendid',
            'Deluxe', 'Graceful', 'Radiant', 'Glorious', 'Vibrant', 'Stunning', 'Brilliant',
            'Exceptional', 'Spectacular'
        ];

        $businessSuffixes = [
            'Events', 'Productions', 'Services', 'Studios', 'Concepts', 'Creations',
            'Solutions', 'Group', 'Company', 'Enterprise', 'Team', 'Hub', 'Centre',
            'World', 'Palace', 'Haven', 'Touch', 'Affairs', 'Dreams', 'Gallery'
        ];

        $vendorCount = 30; // Start with 30 for testing
        $plans = ['Free', 'Premium', 'Gold'];

        for ($i = 1; $i <= $vendorCount; $i++) {
            $category = $categories->random();
            $region = $regions->random();
            $district = $region->districts->random();
            $town = $district->towns->random();

            // Generate realistic Ghanaian business name
            $prefix = $businessPrefixes[array_rand($businessPrefixes)];
            $suffix = $businessSuffixes[array_rand($businessSuffixes)];
            $businessName = "{$prefix} {$category->name} {$suffix}";

            // Create user first
            $user = User::create([
                'name' => str_replace(['&', ' '], ['and', '_'], Str::slug($businessName, ' ')),
                'email' => "vendor{$i}@kabzsevent.com",
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Assign vendor role
            $user->assignRole('vendor');

            // Create vendor
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'business_name' => $businessName,
                'slug' => Str::slug($businessName) . '-' . $i,
                'description' => "Professional {$category->name} service provider based in {$town->name}, {$region->name}. We offer quality and affordable services for your special events.",
                'phone' => '+233' . rand(20, 59) . rand(1000000, 9999999),
                'whatsapp' => '+233' . rand(20, 59) . rand(1000000, 9999999),
                'address' => "{$town->name}, {$district->name}, {$region->name}",
                'region_id' => $region->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'profile_photo' => "https://picsum.photos/seed/dummy-vendor-{$i}/600/400",
                'work_samples' => json_encode([
                    "https://picsum.photos/seed/dummy-vendor-{$i}-1/600/400",
                    "https://picsum.photos/seed/dummy-vendor-{$i}-2/600/400",
                    "https://picsum.photos/seed/dummy-vendor-{$i}-3/600/400",
                ]),
                'social_links' => json_encode([
                    'facebook' => "https://facebook.com/vendor{$i}",
                    'instagram' => "https://instagram.com/vendor{$i}",
                ]),
                'is_verified' => rand(0, 1) === 1,
                'verified_at' => rand(0, 1) === 1 ? now() : null,
                'rating_cached' => rand(35, 50) / 10, // 3.5 to 5.0
            ]);

            // Create subscription
            $plan = $plans[array_rand($plans)];
            VendorSubscription::create([
                'vendor_id' => $vendor->id,
                'plan' => $plan,
                'price_amount' => $plan === 'Free' ? 0 : ($plan === 'Premium' ? 99 : 249),
                'currency' => 'GHS',
                'status' => 'active',
                'started_at' => now(),
                'ends_at' => $plan === 'Free' ? null : now()->addDays($plan === 'Premium' ? 30 : 90),
            ]);
        }

        $this->command->info("✅ Created {$vendorCount} vendors across Ghana!");
    }
}
