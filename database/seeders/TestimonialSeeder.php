<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'client_name' => 'Sarah Johnson',
                'client_position' => 'CEO',
                'client_company' => 'Global Retail Chain',
                'testimonial' => 'The digital transformation has revolutionized how we operate. We\'re more efficient, our customers are happier, and we\'re seeing significant growth across all channels.',
                'rating' => 5,
                'image_path' => 'testimonial-sarah-johnson.jpg',
                'is_visible' => true,
                'sort_order' => 1,
                'meta_title' => 'Sarah Johnson Testimonial | Global Retail Chain CEO',
                'meta_description' => 'CEO testimonial about successful digital transformation partnership and business growth.',
                'meta_keywords' => 'testimonial, CEO, digital transformation, client feedback'
            ],
            [
                'client_name' => 'Michael Chen',
                'client_position' => 'CTO',
                'client_company' => 'Tech Innovations Inc.',
                'testimonial' => 'The smart factory implementation exceeded our expectations. Productivity gains and quality improvements have transformed our competitive position.',
                'rating' => 5,
                'image_path' => 'testimonial-michael-chen.jpg',
                'is_visible' => true,
                'sort_order' => 2,
                'meta_title' => 'Michael Chen Testimonial | Tech Innovations CTO',
                'meta_description' => 'CTO testimonial about smart factory implementation and manufacturing excellence.',
                'meta_keywords' => 'testimonial, CTO, smart factory, manufacturing, client success'
            ],
            [
                'client_name' => 'Dr. Emily Rodriguez',
                'client_position' => 'Medical Director',
                'client_company' => 'Metropolitan General Hospital',
                'testimonial' => 'The new system has made our hospital operations more efficient and patient care significantly better. Wait times are down, and satisfaction is up.',
                'rating' => 5,
                'image_path' => 'testimonial-emily-rodriguez.jpg',
                'is_visible' => true,
                'sort_order' => 3,
                'meta_title' => 'Dr. Emily Rodriguez Testimonial | Healthcare Innovation',
                'meta_description' => 'Medical Director testimonial about hospital management system implementation.',
                'meta_keywords' => 'testimonial, healthcare, hospital management, medical director'
            ],
            [
                'client_name' => 'David Thompson',
                'client_position' => 'Operations Manager',
                'client_company' => 'Traditional Manufacturing Corp.',
                'testimonial' => 'The Industry 4.0 transformation has positioned us as market leaders. The ROI was achieved within 18 months.',
                'rating' => 4,
                'image_path' => 'testimonial-david-thompson.jpg',
                'is_visible' => true,
                'sort_order' => 4,
                'meta_title' => 'David Thompson Testimonial | Manufacturing Excellence',
                'meta_description' => 'Operations Manager testimonial about smart factory implementation and ROI.',
                'meta_keywords' => 'testimonial, operations, manufacturing, Industry 4.0, ROI'
            ],
            [
                'client_name' => 'Lisa Wang',
                'client_position' => 'Marketing Director',
                'client_company' => 'E-commerce Solutions Ltd.',
                'testimonial' => 'The comprehensive digital strategy transformed our online presence. Sales growth exceeded all projections within the first year.',
                'rating' => 5,
                'image_path' => 'testimonial-lisa-wang.jpg',
                'is_visible' => true,
                'sort_order' => 5,
                'meta_title' => 'Lisa Wang Testimonial | E-commerce Success',
                'meta_description' => 'Marketing Director testimonial about digital transformation and e-commerce growth.',
                'meta_keywords' => 'testimonial, marketing, e-commerce, digital strategy'
            ],
            [
                'client_name' => 'Robert Martinez',
                'client_position' => 'IT Director',
                'client_company' => 'Financial Services Group',
                'testimonial' => 'The cloud migration and modernization reduced our operational costs by 40% while improving system reliability.',
                'rating' => 4,
                'image_path' => 'testimonial-robert-martinez.jpg',
                'is_visible' => true,
                'sort_order' => 6,
                'meta_title' => 'Robert Martinez Testimonial | Cloud Migration Success',
                'meta_description' => 'IT Director testimonial about cloud migration and cost reduction.',
                'meta_keywords' => 'testimonial, cloud migration, IT, cost reduction'
            ],
            [
                'client_name' => 'Jennifer Foster',
                'client_position' => 'Product Manager',
                'client_company' => 'StartUp Ventures',
                'testimonial' => 'The AI-powered analytics platform gave us insights we never had before. It\'s transformed our decision-making process.',
                'rating' => 5,
                'image_path' => 'testimonial-jennifer-foster.jpg',
                'is_visible' => true,
                'sort_order' => 7,
                'meta_title' => 'Jennifer Foster Testimonial | AI Analytics Platform',
                'meta_description' => 'Product Manager testimonial about AI analytics platform and business insights.',
                'meta_keywords' => 'testimonial, AI, analytics, product management'
            ],
            [
                'client_name' => 'James Wilson',
                'client_position' => 'Supply Chain Director',
                'client_company' => 'Logistics Plus Inc.',
                'testimonial' => 'The smart supply chain solution eliminated stockouts and reduced inventory costs by 35%. Visibility across all channels.',
                'rating' => 4,
                'image_path' => 'testimonial-james-wilson.jpg',
                'is_visible' => true,
                'sort_order' => 8,
                'meta_title' => 'James Wilson Testimonial | Supply Chain Optimization',
                'meta_description' => 'Supply Chain Director testimonial about smart inventory management and cost reduction.',
                'meta_keywords' => 'testimonial, supply chain, logistics, inventory management'
            ]
        ];

        foreach ($testimonials as $testimonialData) {
            Testimonial::updateOrCreate(
                ['client_name' => $testimonialData['client_name'], 'client_company' => $testimonialData['client_company']],
                $testimonialData
            );
        }

        $this->command->info('Testimonials seeded successfully!');
    }
}
