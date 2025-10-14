<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Category;

class UpdateVendorImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample images available in storage/app/public/vendors/samples/
        $sampleImages = [
            'event-planning' => [
                'vendors/samples/eventplanning.jpg',
                'vendors/samples/eventplaning2.jpg',
                'vendors/samples/eventplaning3.jpg',
                'vendors/samples/events.jpg',
            ],
            'photography' => [
                'vendors/samples/wedding.jpg',
                'vendors/samples/img1.jpeg',
            ],
            'catering' => [
                'vendors/samples/catering.jpg',
                'vendors/samples/catering2.jpg',
                'vendors/samples/catering4.jpg',
            ],
            'cake-dessert' => [
                'vendors/samples/cakes.jpg',
                'vendors/samples/cakes2.jpg',
                'vendors/samples/cakes5.jpg',
                'vendors/samples/pastreis.jpg',
            ],
            'makeup' => [
                'vendors/samples/makeup1.jpg',
                'vendors/samples/makup3.jpeg',
            ],
            'dj' => [
                'vendors/samples/dj.jpg',
                'vendors/samples/dj2.jpg',
                'vendors/samples/dj3.jpg',
                'vendors/samples/djs.jpg',
            ],
            'transportation' => [
                'vendors/samples/transportaionservice.jpg',
            ],
            'venue' => [
                'vendors/samples/venuerentals1.webp',
            ],
            'party-supplies' => [
                'vendors/samples/kdd.jpg',
            ],
        ];

        // Get all vendors
        $vendors = Vendor::with('services.category')->get();

        $this->command->info("Updating images for {$vendors->count()} vendors...");

        foreach ($vendors as $vendor) {
            // Get vendor's category
            $category = $vendor->services->first()?->category;
            
            if (!$category) {
                continue;
            }

            // Find matching images for the category
            $categorySlug = $category->slug;
            $images = [];

            // Match category to image set
            if (str_contains($categorySlug, 'event-planning') || str_contains($categorySlug, 'coordination')) {
                $images = $sampleImages['event-planning'];
            } elseif (str_contains($categorySlug, 'photography') || str_contains($categorySlug, 'videography')) {
                $images = $sampleImages['photography'];
            } elseif (str_contains($categorySlug, 'catering') || str_contains($categorySlug, 'food')) {
                $images = $sampleImages['catering'];
            } elseif (str_contains($categorySlug, 'cake') || str_contains($categorySlug, 'dessert')) {
                $images = $sampleImages['cake-dessert'];
            } elseif (str_contains($categorySlug, 'makeup') || str_contains($categorySlug, 'hair')) {
                $images = $sampleImages['makeup'];
            } elseif (str_contains($categorySlug, 'dj') || str_contains($categorySlug, 'entertainment')) {
                $images = $sampleImages['dj'];
            } elseif (str_contains($categorySlug, 'transportation')) {
                $images = $sampleImages['transportation'];
            } elseif (str_contains($categorySlug, 'venue')) {
                $images = $sampleImages['venue'];
            } elseif (str_contains($categorySlug, 'party') || str_contains($categorySlug, 'supplies')) {
                $images = $sampleImages['party-supplies'];
            } else {
                // Default to event planning images
                $images = $sampleImages['event-planning'];
            }

            // Update vendor with sample images and preview
            if (!empty($images)) {
                $vendor->update([
                    'sample_work_images' => $images,
                    'preview_image' => $images[0], // Use first image as preview
                ]);
                
                $this->command->line("✓ Updated {$vendor->business_name} with " . count($images) . " images");
            }
        }

        $this->command->info("✓ Successfully updated vendor images!");
    }
}
