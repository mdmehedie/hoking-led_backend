<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
                'code' => 'us',
                'name' => 'United States',
                'currency' => 'USD',
                'timezone' => 'America/New_York',
                'language' => 'en-US',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'uk',
                'name' => 'United Kingdom',
                'currency' => 'GBP',
                'timezone' => 'Europe/London',
                'language' => 'en-GB',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'code' => 'eu',
                'name' => 'European Union',
                'currency' => 'EUR',
                'timezone' => 'Europe/Brussels',
                'language' => 'en',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'code' => 'ca',
                'name' => 'Canada',
                'currency' => 'CAD',
                'timezone' => 'America/Toronto',
                'language' => 'en-CA',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 4,
            ],
            [
                'code' => 'au',
                'name' => 'Australia',
                'currency' => 'AUD',
                'timezone' => 'Australia/Sydney',
                'language' => 'en-AU',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
