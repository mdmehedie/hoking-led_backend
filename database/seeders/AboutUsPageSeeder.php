<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class AboutUsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only delete the about-us page if it exists
        Page::where('slug', 'about-us')->delete();

        $defaultLocale = Locale::defaultCode() ?? 'en';
        $authorId = User::where('email', 'admin@example.com')->first()?->id ?? 1;

        $aboutUsContent = [
            'title' => 'Innovating for Excellence',
            'secondary_title' => 'Our Journey in LED Excellence',
            'first_description' => 'For over a decade, we have been at the forefront of LED innovation, delivering solutions that inspire and illuminate.',
            'first_description_redirect_link' => '/our-history',
            'image_1' => 'pages/about-us/image-1.jpg',
            'image_2' => 'pages/about-us/image-2.jpg',
            'service' => 'Comprehensive LED Manufacturing & Support',
            'countries_active_clients' => '25+',
            'years_warranty' => '5 Years',
            'service_warranty' => '2 Years',
            'our_process' => [
                'title' => 'Our Systematic Approach',
                'steps' => [
                    ['title' => 'Research & Design', 'description' => 'We start by understanding market needs and designing efficient LED solutions.'],
                    ['title' => 'Quality Manufacturing', 'description' => 'Every unit is produced in our state-of-the-art factory with rigorous testing.'],
                    ['title' => 'Global Distribution', 'description' => 'Our logistics network ensures timely delivery to clients worldwide.']
                ]
            ],
            'mission_vision_image' => 'pages/about-us/mission-vision.jpg',
            'mission_vision_title' => 'Shaping the Future Together',
            'mission' => [
                'title' => 'Our Mission',
                'icon' => 'pages/about-us/mission-icon.png',
                'description' => 'To provide sustainable, high-performance LED lighting that empowers businesses and individuals.'
            ],
            'vision' => [
                'title' => 'Our Vision',
                'icon' => 'pages/about-us/vision-icon.png',
                'description' => 'To be the most trusted global partner in LED technology through innovation and integrity.'
            ],
        ];

        $pageData = [
            'slug' => 'about-us',
            'title' => [$defaultLocale => 'About Us'],
            'excerpt' => [$defaultLocale => 'Learn about our journey, process, and the values that drive us.'],
            'content' => [$defaultLocale => $aboutUsContent],
            'status' => 'published',
            'author_id' => $authorId,
            'published_at' => now(),
            'meta_title' => 'About Us - Our Process, Mission & Vision',
            'meta_description' => 'Discover our journey of innovation, our systematic manufacturing process, and our commitment to lighting excellence.',
        ];

        Page::create($pageData);

        $this->command->info('About Us page seeded successfully!');
    }
}
