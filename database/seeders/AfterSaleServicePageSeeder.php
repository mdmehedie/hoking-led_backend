<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class AfterSaleServicePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only delete the after-sale-service page if it exists
        Page::where('slug', 'after-sale-service')->delete();

        $defaultLocale = Locale::defaultCode() ?? 'en';
        $authorId = User::where('email', 'admin@example.com')->first()?->id ?? 1;

        $afterSaleContent = [
            'hero_bg' => 'pages/after-sale-service/hero-bg.jpg',
            'hero_title' => 'Commitment Beyond the Sale',
            'hero_description' => 'We believe in building lasting relationships with our clients through exceptional after-sales support and technical service.',
            'services' => [
                [
                    'icon' => 'pages/after-sale-service/support-icon.png',
                    'title' => '24/7 Technical Support',
                    'description' => 'Our expert technicians are available around the clock to assist you with any technical inquiries or troubleshooting.'
                ],
                [
                    'icon' => 'pages/after-sale-service/maintenance-icon.png',
                    'title' => 'On-Site Maintenance',
                    'description' => 'We provide comprehensive on-site maintenance services to ensure your LED installations continue to perform at their best.'
                ],
                [
                    'icon' => 'pages/after-sale-service/spare-parts-icon.png',
                    'title' => 'Spare Parts Availability',
                    'description' => 'We maintain a robust inventory of original spare parts to ensure minimal downtime for your lighting systems.'
                ]
            ],
            'quality_warranty_policy' => [
                '5-Year standard warranty on all premium LED modules.',
                'Free technical consultation for the entire lifespan of the product.',
                'Guaranteed response time of within 24 hours for all service requests.',
                'Global network of certified service partners for localized support.'
            ],
        ];

        $pageData = [
            'slug' => 'after-sale-service',
            'title' => [$defaultLocale => 'After-Sale Service'],
            'excerpt' => [$defaultLocale => 'Discover our commitment to long-term support and our comprehensive warranty policies.'],
            'content' => [$defaultLocale => $afterSaleContent],
            'status' => 'published',
            'author_id' => $authorId,
            'published_at' => now(),
            'meta_title' => 'After-Sale Service - Support & Warranty',
            'meta_description' => 'Learn about our 24/7 technical support, on-site maintenance, and our quality warranty policies for LED solutions.',
        ];

        Page::create($pageData);

        $this->command->info('After-Sale Service page seeded successfully!');
    }
}
