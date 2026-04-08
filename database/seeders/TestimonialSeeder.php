<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocale = Locale::defaultCode() ?? 'en';

        $testimonials = [
            [
                'client_name' => 'John Smith',
                'client_position' => 'CEO',
                'client_company' => 'TechCorp Inc.',
                'testimonial' => 'Working with this team transformed our business. Their expertise and dedication exceeded all expectations.',
                'rating' => 5,
                'is_visible' => true,
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Sarah Johnson',
                'client_position' => 'CTO',
                'client_company' => 'InnovateTech',
                'testimonial' => 'The solutions provided were innovative, scalable, and delivered on time. Highly recommend their services.',
                'rating' => 5,
                'is_visible' => true,
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Michael Chen',
                'client_position' => 'Director of Operations',
                'client_company' => 'GlobalRetail',
                'testimonial' => 'Exceptional quality and attention to detail. The project was completed ahead of schedule with outstanding results.',
                'rating' => 4,
                'is_visible' => true,
                'sort_order' => 3,
            ],
            [
                'client_name' => 'Emily Davis',
                'client_position' => 'Founder',
                'client_company' => 'StartupHub',
                'testimonial' => 'From concept to launch, the team provided invaluable guidance and technical expertise that made our product a success.',
                'rating' => 5,
                'is_visible' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($testimonials as $testimonialData) {
            $testimonial = Testimonial::firstOrCreate(
                [
                    'client_name' => $testimonialData['client_name'],
                    'sort_order' => $testimonialData['sort_order'],
                ],
                $testimonialData
            );

            // Set translatable attributes
            foreach (['client_name', 'client_position', 'client_company', 'testimonial'] as $field) {
                if (isset($testimonialData[$field])) {
                    $testimonial->setTranslation($field, $defaultLocale, $testimonialData[$field]);
                }
            }
            $testimonial->save();
        }

        $this->command->info('Testimonials seeded successfully!');
    }
}
