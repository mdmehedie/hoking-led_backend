<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanyPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only delete the company page if it exists
        Page::where('slug', 'company')->delete();

        $defaultLocale = Locale::defaultCode() ?? 'en';
        $authorId = User::where('email', 'admin@example.com')->first()?->id ?? 1;

        $companyContent = [
            'hero_bg' => 'pages/company/hero-bg.jpg',
            'hero_title' => 'Building the Future of LED Technology',
            'hero_secondary_title' => 'Innovative Solutions for a Brighter World',
            'hero_description' => 'We are pioneers in LED manufacturing, providing high-quality, energy-efficient lighting solutions for global markets.',
            'banner' => 'pages/company/banner.jpg',
            'our_company_description' => [
                'With over a decade of experience in the LED industry, we have established ourselves as a leader in innovative lighting technology.',
                'Our commitment to quality and sustainability drives everything we do, from research and development to final production.',
                'We serve clients across various sectors, ensuring that every project receives the perfect lighting solution tailored to its needs.'
            ],
            'our_factory' => [
                'title' => 'State-of-the-Art Manufacturing',
                'description_1' => 'Our factory is equipped with the latest automated SMT lines and high-precision testing equipment.',
                'description_2' => 'We maintain rigorous quality control standards to ensure the longevity and performance of every LED unit we produce.',
                'redirect_link' => '/manufacturing',
                'image_1' => 'pages/company/factory-1.jpg',
                'image_2' => 'pages/company/factory-2.jpg',
                'image_3' => 'pages/company/factory-3.jpg',
            ],
            'certification_title' => 'Certified Quality & Excellence',
            'certification_description' => 'Our products meet international standards for safety, efficiency, and environmental impact.',
            'mission' => 'To illuminate the world with sustainable and innovative LED solutions that enhance life and protect the planet.',
            'value' => 'Integrity, Innovation, Sustainability, and Customer-Centric Excellence.',
            'growth' => 'Consistently expanding our global footprint while maintaining the highest standards of local service and technical support.',
            'bottom_image' => 'pages/company/bottom-cta.jpg',
        ];

        $pageData = [
            'slug' => 'company',
            'title' => [$defaultLocale => 'Company'],
            'excerpt' => [$defaultLocale => 'Learn about our journey, mission, and manufacturing excellence.'],
            'content' => [$defaultLocale => $companyContent],
            'status' => 'published',
            'author_id' => $authorId,
            'published_at' => now(),
            'meta_title' => 'Our Company - Leading LED Manufacturer',
            'meta_description' => 'Discover the story behind our LED innovations, our state-of-the-art factory, and our commitment to sustainable lighting.',
        ];

        Page::create($pageData);

        $this->command->info('Company page seeded successfully!');
    }
}
