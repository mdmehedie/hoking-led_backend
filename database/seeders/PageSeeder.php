<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@example.com')->first();
        if (!$author) {
            $author = User::first();
        }

        $pages = [
            [
                'title' => [
                    'en' => 'About Us',
                    'bd' => 'আমাদের সম্পর্কে'
                ],
                'slug' => 'about-us',
                'excerpt' => [
                    'en' => 'Learn about our mission, values, and the team behind our innovative solutions.',
                    'bd' => 'আমাদের মিশন, মূল্যমান্য এবং উদ্ভাবনী সমাধানের পিছনে থাকা দলের সম্পর্কে জানুন।'
                ],
                'content' => [
                    'en' => '<h2>Our Story</h2><p>Founded in 2010, we began as a small team of passionate technologists dedicated to solving real-world problems through innovative digital solutions.</p><h2>Our Mission</h2><p>To transform businesses and improve lives through cutting-edge technology solutions that drive efficiency, growth, and success.</p><h2>Our Values</h2><h3>Innovation</h3><p>We constantly push boundaries and explore new possibilities to deliver solutions that matter.</p><h3>Integrity</h3><p>We build trust through transparency, honesty, and ethical business practices.</p><h3>Excellence</h3><p>We pursue perfection in every project, every interaction, and every solution.</p><h2>Our Team</h2><p>Our diverse team brings together expertise from around the world, united by a shared passion for technology and commitment to client success.</p>',
                    'bd' => '<h2>আমাদের গল্প</h2><p>২০১০ সালে প্রতিষ্ঠিত, আমরা উদ্ভাবনী ডিজিটাল সমাধানের মাধ্যমে বাস্তবিক সমস্যা সমাধানের জন্য নিবেদিত প্রযুক্তিগুলির একটি ছোট দল হিসাবে শুরু হয়েছি।</p><h2>আমাদের মিশন</h2><p>অত্যাধুনিক প্রযুক্তিগুলি সমাধানের মাধ্যমে ব্যবসা এবং জীবন উন্নত করার মাধ্যমে দক্ষতা, বৃদ্ধি এবং সাফল্য চালানো কাট-এজিং প্রযুক্তিগুলির মাধ্যমে রূপান্তরণ করা।</p><h2>আমাদের মূল্যমান্য</h2><h3>উদ্ভাবন</h3><p>আমরা ক্রমাণসীমা ঠেলা এবং নতুন সম্ভাব্য অন্বেষণ করি যা গুরুত্বপূর্ণ সমাধান দিতে পারে।</p><h3>অখণ্ডতা</h3><p>আমরা স্বচ্ছলতা, সততা এবং নৈতিক ব্যবসায় অনুশীলনের মাধ্যমে আস্থা তৈরি করি।</p><h3>শ্রেষ্ঠতা</h3><p>আমরা প্রতিটি প্রকল্পে, প্রতিটি মিথস্করণ এবং প্রতিটি সমাধানে নিখুঁতে পারফেকশনের জন্য তাড়াই।</p><h2>আমাদের দল</h2><p>আমাদের বৈচিত্র্য দল বিশ্বজুড়ে দক্ষতা নিয়ে এনে এসেছে, প্রযুক্তিগুলির প্রতি এবং ক্লায়েন্ট সাফল্যের প্রতি একটি ভাগ করা উত্সাহ দ্বারা একত্রিত হয়েছে।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(30),
                'meta_title' => [
                    'en' => 'About Us | Our Company Story and Mission',
                    'bd' => 'আমাদের সম্পর্কে | আমাদের কোম্পানির গল্প এবং মিশন'
                ],
                'meta_description' => [
                    'en' => 'Learn about our company history, mission, values, and the dedicated team behind our innovative technology solutions.',
                    'bd' => 'আমাদের কোম্পানির ইতিহাস, মিশন, মূল্যমান্য এবং উদ্ভাবনী প্রযুক্তিগুলির পিছনে থাকা নিবেদিত দল সম্পর্কে জানুন।'
                ],
                'meta_keywords' => [
                    'en' => 'about us, company story, mission, values, team',
                    'bd' => 'আমাদের সম্পর্কে, কোম্পানির গল্প, মিশন, মূল্যমান্য, দল'
                ]
            ],
            [
                'title' => [
                    'en' => 'Contact Us',
                    'bd' => 'যোগাযোগ করুন'
                ],
                'slug' => 'contact-us',
                'excerpt' => [
                    'en' => 'Get in touch with our team for inquiries, support, or partnership opportunities.',
                    'bd' => 'অনুসন্ধান, সমর্থন বা অংশীদারের সুযোগের জন্য আমাদের দলের সাথে যোগাযোগ করুন।'
                ],
                'content' => [
                    'en' => '<h2>Get in Touch</h2><p>We\'re here to help you transform your business with innovative technology solutions. Whether you have questions, need support, or want to explore partnership opportunities, we\'d love to hear from you.</p><h3>Contact Information</h3><p><strong>Email:</strong> info@example.com<br><strong>Phone:</strong> +1 (555) 123-4567<br><strong>Address:</strong> 123 Business Ave, Suite 100, City, State 12345</p><h3>Business Hours</h3><p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p><h3>Office Locations</h3><p>Our headquarters is located in City, State, with additional offices in New York, London, and Tokyo to serve our global clients.</p><h2>Send Us a Message</h2><p>Fill out the contact form below, and our team will get back to you within 24 business hours.</p>',
                    'bd' => '<h2>যোগাযোগ করুন</h2><p>আমরা উদ্ভাবনী প্রযুক্তিগুলি সমাধানের মাধ্যমে আপনার ব্যবসা রূপান্তরণে সাহায্যতা করার জন্য এখান। আপনার প্রশ্ন আছে, সমর্থন প্রয়োজনীয়, বা অংশীদারের সুযোগ অন্বেষণ করতে চান, আমরা আপনার কাছ থেকে শুনতে পেতে চাই।</p><h3>যোগাযোগের তথ্য</h3><p><strong>ইমেল:</strong> info@example.com<br><strong>ফোন:</strong> +১ (৫৫৫) ১২৩-৪৫৬৭<br><strong>ঠিকানা:</strong> ১২৩ ব্যবসায় অ্যাভি, স্যুট ১০০, শহর, স্টেট ১২৩৪৫</p><h3>ব্যবসায় সময়</h3><p>সোমবার - শুক্রবার: সকাল ৯:০০ পূর্বাহ্ন - ৬:০০ অপরাহ্ন<br>শনিবার: সকাল ১০:০০ পূর্বাহ্ন - ৪:০০ অপরাহ্ন<br>রবিবার: বন্ধ</p><h3>অফিস অবস্থান</h3><p>আমাদের সদর দপ্তর শহর, স্টেটে অবস্থিত, এবং আমাদের বিশ্বব্যাপী ক্লায়েন্টদের সেবা করার জন্য নিউ ইয়র্ক, লন্ডন এবং টোকিওতে অতিরিক্ত অফিস রয়েছে।</p><h2>আমাদের কাছে একটি বার্তা পাঠান</h2><p>নীচের যোগাযোগ ফর্মটি পূরণ করুন, এবং আমাদের দল ২৪ ব্যবসায় ঘন্টার মধ্যে আপনার কাছে ফিরতে দেবে।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(25),
                'meta_title' => [
                    'en' => 'Contact Us | Get in Touch with Our Team',
                    'bd' => 'যোগাযোগ করুন | আমাদের দলের সাথে যোগাযোগ করুন'
                ],
                'meta_description' => [
                    'en' => 'Contact our team for inquiries, support, or partnership opportunities. Find our locations and business hours.',
                    'bd' => 'অনুসন্ধান, সমর্থন বা অংশীদারের জন্য আমাদের দলের সাথে যোগাযোগ করুন। আমাদের অবস্থান এবং ব্যবসায় সময় খুঁজুন।'
                ],
                'meta_keywords' => [
                    'en' => 'contact us, get in touch, support, business hours, locations',
                    'bd' => 'যোগাযোগ করুন, যোগাযোগ, সমর্থন, ব্যবসায় সময়, অবস্থান'
                ]
            ],
            [
                'title' => [
                    'en' => 'Our Services',
                    'bd' => 'আমাদের সেবাসমূল্য'
                ],
                'slug' => 'our-services',
                'excerpt' => [
                    'en' => 'Discover our comprehensive range of digital transformation services designed to drive your business success.',
                    'bd' => 'আপনার ব্যবসা সাফল্য চালানোর জন্য ডিজাইন করা আমাদের বিস্তৃত ডিজিটাল রূপান্তরণ পরিষেবা পরিসর আবিষ্কার করুন।'
                ],
                'content' => [
                    'en' => '<h2>Comprehensive Digital Solutions</h2><p>We offer a full spectrum of services designed to transform your business operations and drive sustainable growth.</p><h3>Digital Transformation Consulting</h3><p>Strategic guidance to help you navigate digital transformation journey, from assessment to implementation and beyond.</p><h3>Custom Software Development</h3><p>Tailored solutions built to address your specific business challenges and opportunities.</p><h3>Cloud Services & Infrastructure</h3><p>Scalable cloud solutions that grow with your business, ensuring reliability and performance.</p><h3>Data Analytics & AI</h3><p>Advanced analytics and artificial intelligence solutions to unlock insights from your data.</p><h3>Cybersecurity Solutions</h3><p>Comprehensive security measures to protect your digital assets and ensure compliance.</p><h3>Managed IT Services</h3><p>Ongoing support and management to keep your systems running optimally.</p><h2>Why Choose Us</h2><p>Our approach combines technical expertise with business acumen to deliver solutions that drive real results and competitive advantage.</p>',
                    'bd' => '<h2>বিস্তৃত ডিজিটাল সমাধান</h2><p>আমরা আপনার ব্যবসা অপারেশন রূপান্তরণ এবং টেকসইটেবল বৃদ্ধি চালানোর জন্য ডিজাইন করা পূর্ণাঙ্গের পরিষেবা অফার করি।</p><h3>ডিজিটাল রূপান্তরণ পরামর্শা</h3><p>মূল্যায়ন থেকে বাস্তবায়ন পর্যন্ত পরিচালনার জন্য কৌশলগত সহায়তা, মূল্যায়ন থেকে বাস্তবায়ন এবং তার বাইরে পর্যন্ত সহায়তা।</p><h3>কাস্টম সফটওয়্যার উন্নয়ন</h3><p>আপনার নির্দিষ্টিত ব্যবসা চ্যালেঞ্জ এবং সুযোগ মোকাবে তৈরি করা সমাধান।</p><h3>ক্লাউড পরিষেবা ও অবকাঠামো</h3><p>আপনার ব্যবসার সাথে বেড়ে বাড়ার জন্য স্কেলেবল ক্লাউড সমাধান যা নির্ভরলতা এবং পারফরম্যান্স নিশ্চিত করে।</p><h3>ডেটা অ্যানালিটিক্স ও AI</h3><p>আপনার ডেটা থেকে অন্তর্দৃষ্টি আনলক করার জন্য উন্নত অ্যানালিটিক্স এবং কৃত্রিম বুদ্ধিমত্তা সমাধান।</p><h3>সাইবার সুরক্ষা সমাধান</h3><p>আপনার ডিজিটাল সম্পদ রক্ষা করার এবং সম্মতি নিশ্চিত করার জন্য বিস্তৃত নিরাপত্তি ব্যবস্থা।</p><h3>পরিচালিত IT পরিষেবা</h3><p>আপনার সিস্টেমগুলি অপ্টিমালভাভাবে চালানোর জন্য চলমান সমর্থন এবং ব্যবস্থাপনা।</p><h2>কেন আমাদের কাছে বেছন</h2><p>আমাদের পদ্ধতিটি কৌশলগত দক্ষতা এবং ব্যবসায় প্রজ্ঞানের সমন্বয়ে বাস্তবায়ন করে যা আসল ফলাফল এবং প্রতিযোগিত্য সুবিধা দেয়।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(20),
                'meta_title' => [
                    'en' => 'Our Services | Digital Transformation Solutions',
                    'bd' => 'আমাদের সেবাসমূল্য | ডিজিটাল রূপান্তরণ সমাধান'
                ],
                'meta_description' => [
                    'en' => 'Explore our comprehensive digital transformation services including consulting, software development, cloud services, AI solutions, and cybersecurity.',
                    'bd' => 'পরামর্শা, সফটওয়্যার উন্নয়ন, ক্লাউড পরিষেবা, AI সমাধান এবং সাইবার সুরক্ষা সহ আমাদের বিস্তৃত ডিজিটাল রূপান্তরণ পরিষেবা অন্বেষণ করুন।'
                ],
                'meta_keywords' => [
                    'en' => 'services, digital transformation, consulting, software development, cloud, AI, cybersecurity',
                    'bd' => 'সেবাসমূল্য, ডিজিটাল রূপান্তরণ, পরামর্শা, সফটওয়্যার উন্নয়ন, ক্লাউড, AI, সাইবার সুরক্ষা'
                ]
            ],
            [
                'title' => [
                    'en' => 'Privacy Policy',
                    'bd' => 'গোপনীয়তা নীতি'
                ],
                'slug' => 'privacy-policy',
                'excerpt' => [
                    'en' => 'Learn how we collect, use, and protect your personal information in accordance with privacy regulations.',
                    'bd' => 'গোপনীয়তা প্রবিধানমের সাথে আমরা কিভাবে আপনার ব্যক্তিগত তথ্য সংগ্রহ করি, ব্যবহার করি এবং সুরক্ষা দিই।'
                ],
                'content' => [
                    'en' => '<h2>Privacy Policy</h2><p>Last updated: January 1, 2024</p><h3>Information We Collect</h3><p>We collect information you provide directly to us, such as when you create an account, contact us, or use our services.</p><h3>How We Use Your Information</h3><p>We use the information we collect to provide, maintain, and improve our services, communicate with you, and ensure security.</p><h3>Information Sharing</h3><p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p><h3>Data Security</h3><p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p><h3>Your Rights</h3><p>You have the right to access, update, or delete your personal information, subject to applicable laws.</p><h3>Contact Us</h3><p>If you have questions about this Privacy Policy, please contact us at privacy@example.com.</p>',
                    'bd' => '<h2>গোপনীয়তা নীতি</h2><p>সর্বশেষ: ১ জানুয়ারি, ২০২৪</p><h3>তথ্য যা আমরা সংগ্রহ করি</h3><p>আমরা তথ্য সংগ্রহ করি যা আপনি আমাদের কাছে সরাসরি প্রদান করেন, যেমন যখন আপনি অ্যাকাউন্ট তৈরি করেন, আমাদের সাথে যোগাযোগ করেন বা আমাদের পরিষেবা ব্যবহার করেন।</p><h3>আমরা আপনার তথ্য কিভাবে ব্যবহার করি</h3><p>আমরা আমরা যে তথ্য সংগ্রহ করি তা পরিষেবা প্রদান, রক্ষণাবিক্ষণ এবং উন্নত করার জন্য ব্যবহার করি, আপনার সাথে যোগাযোগ করি এবং নিরাপত্তা নিশ্চিত করি।</p><h3>তথ্য ভাগাসীকরণ</h3><p>আমরা বিক্রি, বাণিজ্য বা অন্যথভাবে আপনার ব্যক্তিগত তথ্য তৃতীয় পার্টিগুলির কাছে স্থানান্তর করি না, এই নীতিতে বর্ণিত থাকে।</p><h3>ডেটা নিরাপত্তা</h3><p>আমরা অননুমোদিত প্রবেশাধনী এবং সাংগঠনিক ব্যবস্থা বাস্তবায়ন করি যা অননুমোদিত অ্যাক্সেস, পরিবর্তন, প্রকাশন বা ধ্বংসন থেকে আপনার ব্যক্তিগত তথ্য রক্ষা করতে পারে।</p><h3>আপনার অধিকার</h3><p>আপনার ব্যক্তিগত তথ্য অ্যাক্সেস করা, আপডেট করা বা মুছে ফেলার অধিকার রয়েছে, প্রযোজ্যযোগ্য আইন অনুযায়ী হিসাবে।</p><h3>আমাদের সাথে যোগাযোগ করুন</h3><p>যদি আপনার এই গোপনীয়তা নীতি সম্পর্কে প্রশ্ন থাকে, অনুগ্রহ করুন privacy@example.com-এ।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(15),
                'meta_title' => [
                    'en' => 'Privacy Policy | How We Protect Your Data',
                    'bd' => 'গোপনীয়তা নীতি | আমরা কিভাবে আপনার ডেটা সুরক্ষা দিই'
                ],
                'meta_description' => [
                    'en' => 'Learn about our data collection practices, how we use your information, and your rights under privacy regulations.',
                    'bd' => 'আমাদের ডেটা সংগ্রহের অনুশীলন, আমরা আপনার তথ্য কিভাবে ব্যবহার করি এবং গোপনীয়তা প্রবিধানমের অধীনে আপনার অধিকার সম্পর্কে জানুন।'
                ],
                'meta_keywords' => [
                    'en' => 'privacy policy, data protection, GDPR, user rights, information security',
                    'bd' => 'গোপনীয়তা নীতি, ডেটা সুরক্ষা, GDPR, ব্যবহারকারী অধিকার, তথ্য নিরাপত্তা'
                ]
            ]
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                [
                    'title' => json_encode($pageData['title']),
                    'excerpt' => json_encode($pageData['excerpt']),
                    'content' => json_encode($pageData['content']),
                    'image_path' => json_encode($pageData['image_path'] ?? ['en' => 'page-placeholder.jpg', 'bd' => 'page-placeholder.jpg']),
                    'status' => $pageData['status'],
                    'author_id' => $pageData['author_id'],
                    'published_at' => $pageData['published_at'],
                    'meta_title' => json_encode($pageData['meta_title'] ?? []),
                    'meta_description' => json_encode($pageData['meta_description'] ?? []),
                    'meta_keywords' => json_encode($pageData['meta_keywords'] ?? [])
                ]
            );
        }

        $this->command->info('Pages seeded successfully!');
    }
}
