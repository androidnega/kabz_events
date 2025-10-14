<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;

class CreateVendorProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Creating vendor profile for vendor user...\n";

        $vendorUser = User::where('email', 'vendor@kabzsevent.com')->first();
        
        if ($vendorUser) {
            echo "Vendor user found: {$vendorUser->email}\n";
            
            $vendor = $vendorUser->vendor;
            if ($vendor) {
                echo "Vendor profile already exists: {$vendor->business_name}\n";
            } else {
                echo "No vendor profile found. Creating one...\n";
                
                $vendor = Vendor::create([
                    'user_id' => $vendorUser->id,
                    'business_name' => 'Test Vendor Business',
                    'description' => 'This is a test vendor business for demonstration purposes.',
                    'address' => 'Accra, Ghana',
                    'phone' => '+233123456789',
                    'email' => 'vendor@kabzsevent.com',
                    'is_verified' => false,
                    'rating_cached' => 0,
                    'total_reviews' => 0,
                    'slug' => 'test-vendor-business'
                ]);
                
                echo "Vendor profile created: {$vendor->business_name}\n";
            }
        } else {
            echo "Vendor user not found!\n";
        }
    }
}
