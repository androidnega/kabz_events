<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Region;
use App\Models\Town;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GhanaLocationsSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Greater Accra' => [
                'Accra Metropolitan' => ['Accra Central', 'Osu', 'Dansoman', 'Kaneshie'],
                'Tema Metropolitan' => ['Tema', 'Community 1'],
                'Ga East' => ['Dome', 'Madina'],
            ],
            'Ashanti' => [
                'Kumasi Metropolitan' => ['Adum', 'Asafo', 'Bantama', 'Suame'],
                'Obuasi Municipal' => ['Obuasi'],
            ],
            'Western' => [
                'Sekondi-Takoradi' => ['Sekondi', 'Takoradi', 'Effiakuma'],
            ],
            'Central' => [
                'Cape Coast Metropolitan' => ['Cape Coast'],
            ],
            'Northern' => [
                'Tamale Metropolitan' => ['Tamale'],
            ],
            'Eastern' => [
                'Koforidua' => ['Koforidua', 'New Juaben'],
            ],
            'Volta' => [
                'Ho Municipal' => ['Ho', 'Hohoe'],
            ],
            'Upper East' => [
                'Bolgatanga Municipal' => ['Bolgatanga', 'Bongo'],
            ],
            'Upper West' => [
                'Wa Municipal' => ['Wa'],
            ],
            'Bono' => [
                'Sunyani Municipal' => ['Sunyani', 'Berekum'],
            ],
        ];

        foreach ($locations as $regionName => $districts) {
            $region = Region::create([
                'name' => $regionName,
                'slug' => Str::slug($regionName),
            ]);

            foreach ($districts as $districtName => $towns) {
                $district = District::create([
                    'region_id' => $region->id,
                    'name' => $districtName,
                    'slug' => Str::slug($districtName),
                ]);

                foreach ($towns as $townName) {
                    Town::create([
                        'district_id' => $district->id,
                        'name' => $townName,
                        'slug' => Str::slug($townName),
                    ]);
                }
            }
        }

        $this->command->info('✅ Ghana locations seeded: ' . Region::count() . ' regions, ' . District::count() . ' districts, ' . Town::count() . ' towns');
    }
}
