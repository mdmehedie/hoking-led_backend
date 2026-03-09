<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Transform Your Business Today',
                'description' => 'Discover our comprehensive digital transformation solutions designed to drive growth and innovation.',
                'image_path' => 'slider-hero-1.jpg',
                'link' => '/services',
                'alt_text' => 'Digital transformation services hero banner',
                'order' => 1,
                'status' => true,
                'media_type' => 'image',
                'custom_styles' => [
                    'overlay' => 'rgba(0,0,0,0.4)',
                    'text_color' => '#ffffff',
                    'button_color' => '#3b82f6'
                ]
            ],
            [
                'title' => 'Smart Factory Solutions',
                'description' => 'Industry 4.0 transformation with IoT, automation, and AI-powered manufacturing systems.',
                'image_path' => 'slider-manufacturing.jpg',
                'link' => '/case-studies/smart-factory-implementation',
                'alt_text' => 'Smart factory implementation and Industry 4.0 solutions',
                'order' => 2,
                'status' => true,
                'media_type' => 'image',
                'custom_styles' => [
                    'overlay' => 'rgba(0,100,200,0.3)',
                    'text_color' => '#ffffff',
                    'button_color' => '#0066cc'
                ]
            ],
            [
                'title' => 'Healthcare Innovation Platform',
                'description' => 'Revolutionizing patient care with AI-powered hospital management and telemedicine integration.',
                'image_path' => 'slider-healthcare.jpg',
                'link' => '/case-studies/healthcare-innovation-smart-hospital-system',
                'alt_text' => 'Smart hospital management system and healthcare innovation',
                'order' => 3,
                'status' => true,
                'media_type' => 'image',
                'custom_styles' => [
                    'overlay' => 'rgba(0,150,136,0.4)',
                    'text_color' => '#ffffff',
                    'button_color' => '#00796b'
                ]
            ],
            [
                'title' => 'Cloud-Powered Analytics',
                'description' => 'Advanced data analytics and AI solutions to unlock business insights and drive decisions.',
                'image_path' => 'slider-analytics.jpg',
                'link' => '/services',
                'alt_text' => 'Cloud analytics platform and AI-powered business intelligence',
                'order' => 4,
                'status' => true,
                'media_type' => 'image',
                'custom_styles' => [
                    'overlay' => 'rgba(75,0,130,0.4)',
                    'text_color' => '#ffffff',
                    'button_color' => '#6f42c1'
                ]
            ],
            [
                'title' => 'E-commerce Excellence',
                'description' => 'Complete digital commerce solutions with unified platforms and personalized customer experiences.',
                'image_path' => 'slider-ecommerce.jpg',
                'link' => '/case-studies/digital-transformation-global-retail-chain',
                'alt_text' => 'E-commerce platform and digital commerce solutions',
                'order' => 5,
                'status' => true,
                'media_type' => 'image',
                'custom_styles' => [
                    'overlay' => 'rgba(220,53,69,0.4)',
                    'text_color' => '#ffffff',
                    'button_color' => '#dc3545'
                ]
            ],
            [
                'title' => 'Cybersecurity Excellence',
                'description' => 'Comprehensive security solutions to protect your digital assets and ensure compliance.',
                'image_path' => 'slider-security.jpg',
                'link' => '/services',
                'alt_text' => 'Cybersecurity solutions and information security services',
                'order' => 6,
                'status' => true,
                'media_type' => 'video_url',
                'video_url' => 'https://www.youtube.com/watch?v=security-demo',
                'custom_styles' => [
                    'overlay' => 'rgba(0,0,0,0.6)',
                    'text_color' => '#ffffff',
                    'button_color' => '#28a745'
                ]
            ]
        ];

        foreach ($sliders as $sliderData) {
            Slider::updateOrCreate(
                ['title' => $sliderData['title'], 'order' => $sliderData['order']],
                $sliderData
            );
        }

        $this->command->info('Sliders seeded successfully!');
    }
}
