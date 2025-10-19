<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Region;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSubscription;
use App\Models\VipPlan;
use App\Models\VipSubscription;
use App\Models\Service;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PremiumVendorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates 30 vendors with different subscription types:
     * - 6 VIP Platinum (Priority 4)
     * - 8 VIP Gold (Priority 3)
     * - 8 VIP Silver (Priority 2)
     * - 8 VIP Bronze (Priority 1)
     * 
     * Mix of verified and non-verified vendors to showcase ranking system.
     */
    public function run(): void
    {
        $categories = Category::all();
        $regions = Region::with('districts.towns')->get();
        $vipPlans = VipPlan::all();

        if ($regions->isEmpty()) {
            $this->command->error('❌ No regions found! Run GhanaLocationsSeeder first.');
            return;
        }

        if ($categories->isEmpty()) {
            $this->command->error('❌ No categories found! Run CategorySeeder first.');
            return;
        }

        if ($vipPlans->isEmpty()) {
            $this->command->error('❌ No VIP plans found! Run VipPlanSeeder first.');
            return;
        }

        $this->command->info('🚀 Creating 30 premium vendors with different subscription tiers...');

        // Get VIP plans
        $platinumPlan = $vipPlans->where('name', 'VIP Platinum')->first();
        $goldPlan = $vipPlans->where('name', 'VIP Gold')->first();
        $silverPlan = $vipPlans->where('name', 'VIP Silver')->first();
        $bronzePlan = $vipPlans->where('name', 'VIP Bronze')->first();

        // Business name prefixes and suffixes
        $prefixes = [
            'Royal', 'Golden', 'Premium', 'Elite', 'Star', 'Crown', 'Diamond', 'Perfect',
            'Ultimate', 'Elegant', 'Divine', 'Supreme', 'Majestic', 'Grand', 'Excellence',
            'Quality', 'Professional', 'Exquisite', 'Blissful', 'Splendid', 'Deluxe',
            'Graceful', 'Radiant', 'Glorious', 'Vibrant', 'Stunning', 'Brilliant',
            'Exceptional', 'Spectacular', 'Luxe'
        ];

        $suffixes = [
            'Events', 'Productions', 'Services', 'Studios', 'Concepts', 'Creations',
            'Solutions', 'Group', 'Company', 'Enterprise', 'Team', 'Hub', 'Centre',
            'World', 'Palace', 'Haven', 'Touch', 'Affairs', 'Dreams', 'Gallery',
            'Professionals', 'Experts', 'Masters'
        ];

        $vendorData = [
            // ===== 6 PLATINUM VIP VENDORS (Priority 4 - Highest) =====
            ['plan' => $platinumPlan, 'verified' => true, 'rating' => 5.0],   // Best: Platinum + Verified + 5 stars
            ['plan' => $platinumPlan, 'verified' => true, 'rating' => 4.9],
            ['plan' => $platinumPlan, 'verified' => true, 'rating' => 4.8],
            ['plan' => $platinumPlan, 'verified' => false, 'rating' => 4.9], // Platinum without verification
            ['plan' => $platinumPlan, 'verified' => false, 'rating' => 4.7],
            ['plan' => $platinumPlan, 'verified' => false, 'rating' => 4.6],

            // ===== 8 GOLD VIP VENDORS (Priority 3) =====
            ['plan' => $goldPlan, 'verified' => true, 'rating' => 4.9],
            ['plan' => $goldPlan, 'verified' => true, 'rating' => 4.8],
            ['plan' => $goldPlan, 'verified' => true, 'rating' => 4.7],
            ['plan' => $goldPlan, 'verified' => false, 'rating' => 4.8],
            ['plan' => $goldPlan, 'verified' => false, 'rating' => 4.6],
            ['plan' => $goldPlan, 'verified' => false, 'rating' => 4.5],
            ['plan' => $goldPlan, 'verified' => false, 'rating' => 4.4],
            ['plan' => $goldPlan, 'verified' => false, 'rating' => 4.3],

            // ===== 8 SILVER VIP VENDORS (Priority 2) =====
            ['plan' => $silverPlan, 'verified' => true, 'rating' => 4.8],
            ['plan' => $silverPlan, 'verified' => true, 'rating' => 4.7],
            ['plan' => $silverPlan, 'verified' => true, 'rating' => 4.5],
            ['plan' => $silverPlan, 'verified' => false, 'rating' => 4.7],
            ['plan' => $silverPlan, 'verified' => false, 'rating' => 4.5],
            ['plan' => $silverPlan, 'verified' => false, 'rating' => 4.3],
            ['plan' => $silverPlan, 'verified' => false, 'rating' => 4.2],
            ['plan' => $silverPlan, 'verified' => false, 'rating' => 4.0],

            // ===== 8 BRONZE VIP VENDORS (Priority 1) =====
            ['plan' => $bronzePlan, 'verified' => true, 'rating' => 4.6],
            ['plan' => $bronzePlan, 'verified' => true, 'rating' => 4.5],
            ['plan' => $bronzePlan, 'verified' => false, 'rating' => 4.5],
            ['plan' => $bronzePlan, 'verified' => false, 'rating' => 4.3],
            ['plan' => $bronzePlan, 'verified' => false, 'rating' => 4.2],
            ['plan' => $bronzePlan, 'verified' => false, 'rating' => 4.0],
            ['plan' => $bronzePlan, 'verified' => false, 'rating' => 3.9],
            ['plan' => $bronzePlan, 'verified' => false, 'rating' => 3.8],
        ];

        $vendorCount = 0;
        foreach ($vendorData as $index => $data) {
            $category = $categories->random();
            $region = $regions->random();
            $district = $region->districts->random();
            $town = $district->towns->random();

            // Generate unique business name
            $prefix = $prefixes[array_rand($prefixes)];
            $suffix = $suffixes[array_rand($suffixes)];
            $businessName = "{$prefix} {$category->name} {$suffix}";
            
            // Make it unique
            $uniqueSuffix = rand(1000, 9999);
            $email = "premium.vendor.{$uniqueSuffix}@kabzsevent.com";

            // Create user
            $user = User::create([
                'name' => str_replace(['&', ' '], ['and', '_'], Str::slug($businessName, ' ')),
                'email' => $email,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Assign vendor role
            $user->assignRole('vendor');

            // Create vendor with consistent images (using seed instead of random)
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'business_name' => $businessName,
                'description' => "Professional {$category->name} service provider based in {$town->name}, {$region->name}. We specialize in creating unforgettable experiences for your special events. With years of expertise and a commitment to excellence, we deliver top-quality services tailored to your needs.",
                'phone' => '+233' . rand(20, 59) . rand(1000000, 9999999),
                'whatsapp' => '+233' . rand(20, 59) . rand(1000000, 9999999),
                'address' => "{$town->name}, {$district->name}, {$region->name}",
                'region_id' => $region->id,
                'district_id' => $district->id,
                'town_id' => $town->id,
                'is_verified' => $data['verified'],
                'verified_at' => $data['verified'] ? now()->subDays(rand(30, 180)) : null,
                'rating_cached' => $data['rating'],
                'sample_work_title' => 'Our Portfolio',
                'sample_work_images' => [
                    "https://picsum.photos/seed/vendor{$uniqueSuffix}-1/800/600",
                    "https://picsum.photos/seed/vendor{$uniqueSuffix}-2/800/600",
                    "https://picsum.photos/seed/vendor{$uniqueSuffix}-3/800/600",
                    "https://picsum.photos/seed/vendor{$uniqueSuffix}-4/800/600",
                ],
                'preview_image' => "https://picsum.photos/seed/vendor{$uniqueSuffix}-preview/800/600",
                'tour_completed' => true,
            ]);

            // Create VIP Subscription
            VipSubscription::create([
                'vendor_id' => $vendor->id,
                'vip_plan_id' => $data['plan']->id,
                'start_date' => now()->subDays(rand(1, 30)),
                'end_date' => now()->addDays($data['plan']->duration_days - rand(1, 30)),
                'status' => 'active',
                'payment_ref' => 'PAY-' . strtoupper(Str::random(10)),
                'ads_used' => rand(0, 2),
            ]);

            // Create a service for this vendor
            $serviceTitles = [
                'Photography & Videography' => ['Wedding Photography', 'Event Photography', 'Professional Videography', 'Drone Photography'],
                'Catering & Food Services' => ['Full Catering Services', 'Buffet Catering', 'Corporate Catering', 'Traditional Dishes'],
                'Decoration & Floral Design' => ['Wedding Decoration', 'Event Styling', 'Floral Arrangements', 'Balloon Decorations'],
                'Entertainment & DJ Services' => ['Professional DJ Services', 'Live Band Performance', 'MC Services', 'Entertainment Package'],
                'Venue Rental' => ['Garden Venue', 'Hall Rental', 'Outdoor Space', 'Conference Venue'],
                'Event Planning & Coordination' => ['Full Event Planning', 'Wedding Coordination', 'Corporate Events', 'Birthday Planning'],
                'Transportation Services' => ['Wedding Cars', 'Guest Transportation', 'Luxury Vehicle Rental', 'Bus Services'],
                'Hair & Makeup Artists' => ['Bridal Makeup', 'Event Styling', 'Hair & Makeup Package', 'Traditional Styling'],
                'Cake & Dessert Designers' => ['Wedding Cakes', 'Birthday Cakes', 'Dessert Tables', 'Custom Cakes'],
                'Party Supplies & Rentals' => ['Chairs & Tables', 'Tent Rental', 'Party Equipment', 'Event Supplies'],
            ];

            $categoryServiceTitles = $serviceTitles[$category->name] ?? ['Premium Service', 'Deluxe Package', 'Standard Service', 'Custom Package'];
            $serviceTitle = $categoryServiceTitles[array_rand($categoryServiceTitles)];

            Service::create([
                'vendor_id' => $vendor->id,
                'category_id' => $category->id,
                'title' => $serviceTitle,
                'description' => "High-quality {$serviceTitle} for your special occasions. We provide professional services with attention to detail and customer satisfaction.",
                'price_min' => rand(500, 2000),
                'price_max' => rand(2500, 8000),
                'pricing_type' => 'package',
                'is_active' => true,
            ]);

            // Create some reviews for higher-rated vendors
            if ($data['rating'] >= 4.5) {
                $reviewCount = rand(3, 8);
                for ($r = 0; $r < $reviewCount; $r++) {
                    Review::create([
                        'vendor_id' => $vendor->id,
                        'user_id' => User::where('email', 'NOT LIKE', '%vendor%')->inRandomOrder()->first()?->id ?? $user->id,
                        'rating' => rand(4, 5),
                        'comment' => $this->getRandomReview(),
                        'created_at' => now()->subDays(rand(1, 90)),
                    ]);
                }
            }

            $vendorCount++;
            $tierName = $data['plan']->name;
            $verifiedStatus = $data['verified'] ? '✓ Verified' : 'Not Verified';
            $this->command->info("  ✓ Created vendor {$vendorCount}/30: {$businessName} [{$tierName}] [{$verifiedStatus}] [⭐ {$data['rating']}]");
        }

        $this->command->info("\n✅ Successfully created {$vendorCount} premium vendors!");
        $this->command->info("📊 Distribution:");
        $this->command->info("   • 6 VIP Platinum vendors (Priority 4 - Highest)");
        $this->command->info("   • 8 VIP Gold vendors (Priority 3)");
        $this->command->info("   • 8 VIP Silver vendors (Priority 2)");
        $this->command->info("   • 8 VIP Bronze vendors (Priority 1)");
        $this->command->info("\n💡 These vendors will be ranked on the homepage based on:");
        $this->command->info("   1. VIP Priority Level (40% weight)");
        $this->command->info("   2. Verification Status (30% weight)");
        $this->command->info("   3. Rating (20% weight)");
        $this->command->info("   4. Number of Reviews (10% weight)");
    }

    /**
     * Get a random positive review comment
     */
    private function getRandomReview(): string
    {
        $reviews = [
            'Excellent service! Very professional and delivered beyond expectations.',
            'Amazing work! Would definitely recommend to anyone planning an event.',
            'Top-notch quality and great attention to detail. Highly satisfied!',
            'Outstanding service from start to finish. Made our event truly special.',
            'Very professional team. Everything was perfect!',
            'Exceptional quality and great customer service. Will use again!',
            'Fantastic experience! They made our day memorable.',
            'Highly recommended! Professional, reliable, and affordable.',
            'Best service provider we have worked with. Excellent results!',
            'Perfect execution! Every detail was handled with care.',
            'Great value for money. Professional and efficient service.',
            'Wonderful experience! They exceeded all our expectations.',
            'Very impressed with the quality and professionalism.',
            'Absolutely fantastic! Made our event stress-free and beautiful.',
            'Outstanding work! Would definitely hire again for future events.',
        ];

        return $reviews[array_rand($reviews)];
    }
}

