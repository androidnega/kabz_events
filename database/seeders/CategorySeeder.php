<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Photography & Videography',
                'description' => 'Professional photography and videography services for your special events',
                'icon' => 'camera',
                'display_order' => 1,
            ],
            [
                'name' => 'Catering & Food Services',
                'description' => 'Delicious catering options and food services for all types of events',
                'icon' => 'utensils',
                'display_order' => 2,
            ],
            [
                'name' => 'Decoration & Floral Design',
                'description' => 'Beautiful decorations and floral arrangements to enhance your event',
                'icon' => 'palette',
                'display_order' => 3,
            ],
            [
                'name' => 'Entertainment & DJ Services',
                'description' => 'Professional entertainment, DJs, and live music for your celebration',
                'icon' => 'music',
                'display_order' => 4,
            ],
            [
                'name' => 'Venue Rental',
                'description' => 'Perfect venues for weddings, parties, corporate events and more',
                'icon' => 'building',
                'display_order' => 5,
            ],
            [
                'name' => 'Event Planning & Coordination',
                'description' => 'Expert event planners to coordinate and manage your entire event',
                'icon' => 'clipboard-list',
                'display_order' => 6,
            ],
            [
                'name' => 'Transportation Services',
                'description' => 'Reliable transportation options for guests and wedding parties',
                'icon' => 'car',
                'display_order' => 7,
            ],
            [
                'name' => 'Hair & Makeup Artists',
                'description' => 'Professional hair styling and makeup services for any occasion',
                'icon' => 'spa',
                'display_order' => 8,
            ],
            [
                'name' => 'Cake & Dessert Designers',
                'description' => 'Custom cakes and desserts that taste as amazing as they look',
                'icon' => 'birthday-cake',
                'display_order' => 9,
            ],
            [
                'name' => 'Party Supplies & Rentals',
                'description' => 'Tables, chairs, tents, and all party supplies you need',
                'icon' => 'gift',
                'display_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
                'display_order' => $category['display_order'],
            ]);
        }

        $this->command->info('Successfully seeded 10 event service categories!');
    }
}
