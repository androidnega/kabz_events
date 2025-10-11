<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSampleWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first vendor and add sample work images
        $vendor = Vendor::first();
        
        if ($vendor) {
            $vendor->update([
                'sample_work_title' => 'Our Portfolio',
                'sample_work_images' => [
                    'https://images.unsplash.com/photo-1519741497674-611481863552?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1520854221256-17451cc331bf?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1519167758481-83f1426cc77a?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800&h=600&fit=crop'
                ]
            ]);
            
            $this->command->info("Sample work images added to vendor: {$vendor->business_name}");
        } else {
            $this->command->warn("No vendors found to add sample work images to.");
        }
    }
}