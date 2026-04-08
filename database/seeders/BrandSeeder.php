<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'TechCorp',
                'logo' => null,
                'website_url' => 'https://techcorp.example.com',
                'description' => 'Leading technology solutions provider.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'InnovateTech',
                'logo' => null,
                'website_url' => 'https://innovatetech.example.com',
                'description' => 'Innovation-driven software company.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'GlobalRetail',
                'logo' => null,
                'website_url' => 'https://globalretail.example.com',
                'description' => 'International retail chain.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'StartupHub',
                'logo' => null,
                'website_url' => 'https://startuphub.example.com',
                'description' => 'Startup accelerator and incubator.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'CloudFirst',
                'logo' => null,
                'website_url' => 'https://cloudfirst.example.com',
                'description' => 'Cloud infrastructure and services.',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'DataDriven',
                'logo' => null,
                'website_url' => 'https://datadriven.example.com',
                'description' => 'Analytics and data intelligence.',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::firstOrCreate(
                ['name' => $brandData['name']],
                $brandData
            );
        }

        $this->command->info('Brands seeded successfully!');
    }
}
