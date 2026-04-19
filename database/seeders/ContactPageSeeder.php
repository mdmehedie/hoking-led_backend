<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only delete the contact page if it exists
        Page::where('slug', 'contact')->delete();

        $defaultLocale = Locale::defaultCode() ?? 'en';
        $authorId = User::where('email', 'admin@example.com')->first()?->id ?? 1;

        $contactContent = [
            'title' => 'Get in Touch with Us',
            'description' => 'Have questions about our LED solutions? Our team is here to help you with expert advice and support.',
            'contacts' => [
                [
                    'icon' => 'pages/contact/phone-icon.png',
                    'title' => 'Call Us',
                    'related_to_contact' => [
                        ['text' => '+1 (555) 123-4567'],
                        ['text' => '+1 (555) 765-4321']
                    ],
                    'contact_link' => 'tel:+15551234567'
                ],
                [
                    'icon' => 'pages/contact/email-icon.png',
                    'title' => 'Email Us',
                    'related_to_contact' => [
                        ['text' => 'sales@hokingled.com'],
                        ['text' => 'support@hokingled.com']
                    ],
                    'contact_link' => 'mailto:sales@hokingled.com'
                ],
                [
                    'icon' => 'pages/contact/location-icon.png',
                    'title' => 'Our Office',
                    'related_to_contact' => [
                        ['text' => '123 LED Innovation Way'],
                        ['text' => 'Shenzhen, China']
                    ],
                    'contact_link' => 'https://maps.google.com'
                ]
            ],
        ];

        $pageData = [
            'slug' => 'contact',
            'title' => [$defaultLocale => 'Contact'],
            'excerpt' => [$defaultLocale => 'Reach out to our team for sales, support, or general inquiries.'],
            'content' => [$defaultLocale => $contactContent],
            'status' => 'published',
            'author_id' => $authorId,
            'published_at' => now(),
            'meta_title' => 'Contact Us - Hoking LED Support',
            'meta_description' => 'Contact Hoking LED for high-quality LED manufacturing solutions. Our team is available for sales inquiries and technical support.',
        ];

        Page::create($pageData);

        $this->command->info('Contact page seeded successfully!');
    }
}
