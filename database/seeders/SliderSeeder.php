<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocale = Locale::defaultCode() ?? 'en';

        $sliders = [
            [
                'title' => 'Welcome to Our Platform',
                'description' => 'Discover innovative solutions for your business growth.',
                'label' => 'Hero Slider',
                'primary_button_text' => 'Learn More',
                'primary_button_link' => '/about',
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Latest Products',
                'description' => 'Explore our newest offerings designed for the future.',
                'label' => 'Products Slider',
                'primary_button_text' => 'View Products',
                'primary_button_link' => '/products',
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Get Started Today',
                'description' => 'Join thousands of satisfied customers worldwide.',
                'label' => 'CTA Slider',
                'primary_button_text' => 'Contact Us',
                'primary_button_link' => '/contact',
                'status' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($sliders as $sliderData) {
            $slider = Slider::firstOrCreate(
                ['sort_order' => $sliderData['sort_order']],
                $sliderData
            );

            // Set translatable attributes
            foreach (['title', 'description', 'label', 'primary_button_text'] as $field) {
                if (isset($sliderData[$field])) {
                    $slider->setTranslation($field, $defaultLocale, $sliderData[$field]);
                }
            }
            $slider->save();
        }

        $this->command->info('Sliders seeded successfully!');
    }
}
