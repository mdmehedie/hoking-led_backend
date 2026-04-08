<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocale = Locale::defaultCode() ?? 'en';

        $pages = [
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'excerpt' => 'Learn more about our mission, vision, and the team behind our success.',
                'content' => '<p>We are a team of passionate professionals dedicated to delivering exceptional products and services. Our mission is to empower businesses through innovative technology solutions.</p><h2>Our Vision</h2><p>To be the global leader in providing cutting-edge solutions that transform how businesses operate and grow in the digital age.</p><h2>Our Values</h2><ul><li>Innovation</li><li>Integrity</li><li>Customer First</li><li>Excellence</li></ul>',
                'status' => 'published',
                'author_id' => 1,
                'published_at' => now(),
                'meta_title' => 'About Us - Our Mission and Values',
                'meta_description' => 'Learn more about our mission, vision, and the team behind our success.',
            ],
            [
                'slug' => 'contact-us',
                'title' => 'Contact Us',
                'excerpt' => 'Get in touch with our team for support, inquiries, or partnership opportunities.',
                'content' => '<p>We\'d love to hear from you. Reach out to us through any of the following channels:</p><h2>Office Address</h2><p>123 Business Street, Suite 100, New York, NY 10001</p><h2>Phone</h2><p>+1 (555) 123-4567</p><h2>Email</h2><p>info@company.com</p>',
                'status' => 'published',
                'author_id' => 1,
                'published_at' => now(),
                'meta_title' => 'Contact Us - Get in Touch',
                'meta_description' => 'Get in touch with our team for support, inquiries, or partnership opportunities.',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'excerpt' => 'Our commitment to protecting your privacy and personal data.',
                'content' => '<p>This Privacy Policy describes how we collect, use, and protect your personal information.</p><h2>Information We Collect</h2><p>We collect information you provide directly, such as when you create an account or contact us.</p><h2>How We Use Information</h2><p>We use the information to provide, maintain, and improve our services.</p>',
                'status' => 'published',
                'author_id' => 1,
                'published_at' => now(),
                'meta_title' => 'Privacy Policy',
                'meta_description' => 'Our commitment to protecting your privacy and personal data.',
            ],
        ];

        foreach ($pages as $pageData) {
            $page = Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );

            // Set translatable attributes
            foreach (['title', 'excerpt', 'content'] as $field) {
                if (isset($pageData[$field])) {
                    $page->setTranslation($field, $defaultLocale, $pageData[$field]);
                }
            }
            $page->save();
        }

        $this->command->info('Pages seeded successfully!');
    }
}
