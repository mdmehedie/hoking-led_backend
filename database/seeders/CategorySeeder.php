<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Locale;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocale = Locale::defaultCode() ?? 'en';

        $categories = [
            [
                'slug' => 'electronics',
                'name' => 'Electronics',
                'description' => 'Consumer electronics and gadgets for modern living.',
                'is_visible' => true,
                'meta_title' => 'Electronics Category',
                'meta_description' => 'Browse our electronics collection.',
            ],
            [
                'slug' => 'software',
                'name' => 'Software',
                'description' => 'Professional and consumer software solutions.',
                'is_visible' => true,
                'meta_title' => 'Software Category',
                'meta_description' => 'Explore our software offerings.',
            ],
            [
                'slug' => 'hardware',
                'name' => 'Hardware',
                'description' => 'High-performance hardware and components.',
                'is_visible' => true,
                'meta_title' => 'Hardware Category',
                'meta_description' => 'Quality hardware products.',
            ],
            [
                'slug' => 'accessories',
                'name' => 'Accessories',
                'description' => 'Essential accessories and add-ons.',
                'is_visible' => true,
                'meta_title' => 'Accessories Category',
                'meta_description' => 'Complete your setup with our accessories.',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            // Set translatable attributes
            foreach (['name', 'description'] as $field) {
                if (isset($categoryData[$field])) {
                    $category->setTranslation($field, $defaultLocale, $categoryData[$field]);
                }
            }
            $category->save();
        }

        $this->command->info('Categories seeded successfully!');
    }
}
