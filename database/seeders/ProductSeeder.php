<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories first if they don't exist
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Software', 'slug' => 'software'],
            ['name' => 'Hardware', 'slug' => 'hardware'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate($categoryData);
        }

        $electronicsCategory = Category::where('slug', 'electronics')->first();
        $softwareCategory = Category::where('slug', 'software')->first();
        $hardwareCategory = Category::where('slug', 'hardware')->first();
        $accessoriesCategory = Category::where('slug', 'accessories')->first();

        $products = [
            [
                'title' => [
                    'en' => 'Smart Wireless Headphones Pro',
                    'bd' => 'স্মার্ট ওয়্যারলেস হেডফোন প্রো'
                ],
                'slug' => 'smart-wireless-headphones-pro',
                'short_description' => [
                    'en' => 'Premium noise-cancelling wireless headphones with exceptional sound quality.',
                    'bd' => 'অসাধারণ সাউন্ড কোয়ালিটি সহ প্রিমিয়াম নয়েজ-ক্যান্সেলিং ওয়্যারলেস হেডফোন।'
                ],
                'detailed_description' => [
                    'en' => '<p>Experience premium audio quality with our Smart Wireless Headphones Pro. Featuring advanced noise-cancellation technology, 30-hour battery life, and superior comfort for all-day wear.</p><h3>Key Features:</h3><ul><li>Active Noise Cancellation</li><li>30-hour battery life</li><li>Premium leather cushions</li><li>Bluetooth 5.0 connectivity</li><li>Built-in voice assistant</li></ul>',
                    'bd' => '<p>আমাদের স্মার্ট ওয়্যারলেস হেডফোন প্রো দিয়ে প্রিমিয়াম অডিও কোয়ালিটি অনুভব করুন। উন্নত নয়েজ-ক্যান্সেলেশন প্রযুক্তি, 30-ঘন্টার ব্যাটারি লাইফ এবং সারাদিন পরিধানের জন্য উচ্চতর আরাম সহ।</p><h3>মূল বৈশিষ্ট্য:</h3><ul><li>অ্যাক্টিভ নয়েজ ক্যান্সেলেশন</li><li>30-ঘন্টার ব্যাটারি লাইফ</li><li>প্রিমিয়াম লেদার কুশন</li><li>ব্লুটুথ 5.0 সংযোগ</li><li>বিল্ট-ইন ভয়েস অ্যাসিস্ট্যান্ট</li></ul>'
                ],
                'status' => 'published',
                'category_id' => $electronicsCategory->id,
                'is_featured' => true,
                'technical_specs' => [
                    ['key' => 'Battery Life', 'value' => '30 hours'],
                    ['key' => 'Connectivity', 'value' => 'Bluetooth 5.0'],
                    ['key' => 'Weight', 'value' => '250g'],
                    ['key' => 'Frequency Response', 'value' => '20Hz - 20kHz']
                ],
                'tags' => [
                    ['tag' => 'wireless'],
                    ['tag' => 'noise-cancelling'],
                    ['tag' => 'bluetooth'],
                    ['tag' => 'premium']
                ],
                'video_embeds' => [
                    [
                        'type' => 'embed',
                        'title' => 'Product Demo',
                        'url' => 'https://www.youtube.com/watch?v=demo123'
                    ]
                ],
                'meta_title' => [
                    'en' => 'Smart Wireless Headphones Pro - Premium Audio Experience',
                    'bd' => 'স্মার্ট ওয়্যারলেস হেডফোন প্রো - প্রিমিয়াম অডিও অভিজ্ঞতা'
                ],
                'meta_description' => [
                    'en' => 'Discover premium wireless headphones with active noise cancellation and 30-hour battery life.',
                    'bd' => 'অ্যাক্টিভ নয়েজ ক্যান্সেলেশন এবং 30-ঘন্টার ব্যাটারি লাইফ সহ প্রিমিয়াম ওয়্যারলেস হেডফোন আবিষ্কার করুন।'
                ]
            ],
            [
                'title' => [
                    'en' => 'Professional Laptop Stand',
                    'bd' => 'পেশাদার ল্যাপটপ স্ট্যান্ড'
                ],
                'slug' => 'professional-laptop-stand',
                'short_description' => [
                    'en' => 'Ergonomic aluminum laptop stand for improved posture and cooling.',
                    'bd' => 'উন্নত ভঙ্গি এবং কুলিংয়ের জন্য এরগোনমিক অ্যালুমিনিয়াম ল্যাপটপ স্ট্যান্ড।'
                ],
                'detailed_description' => [
                    'en' => '<p>Transform your workspace with our Professional Laptop Stand. Made from premium aluminum, this stand provides optimal ergonomics and improved airflow for your laptop.</p><h3>Benefits:</h3><ul><li>Improves posture and reduces neck strain</li><li>Enhances laptop cooling</li><li>Adjustable height and angle</li><li>Sleek, modern design</li><li>Compatible with all laptop sizes</li></ul>',
                    'bd' => '<p>আমাদের পেশাদার ল্যাপটপ স্ট্যান্ড দিয়ে আপনার ওয়ার্কস্পেস রূপান্তর করুন। প্রিমিয়াম অ্যালুমিনিয়াম থেকে তৈরি, এই স্ট্যান্ডটি আপনার ল্যাপটপের জন্য অপ্টিমাল এরগোনমিক্স এবং উন্নত এয়ারফ্লো প্রদান করে।</p><h3>সুবিধা:</h3><ul><li>ভঙ্গি উন্নত করে এবং ঘাড়ের চাপ কমায়</li><li>ল্যাপটপ কুলিং বাড়ায়</li><li>সামঞ্জস্যযোগ্য উচ্চতা এবং কোণ</li><li>স্লিক, আধুনিক ডিজাইন</li><li>সব ল্যাপটপ সাইজের সাথে সামঞ্জস্যপূর্ণ</li></ul>'
                ],
                'status' => 'published',
                'category_id' => $accessoriesCategory->id,
                'is_featured' => false,
                'technical_specs' => [
                    ['key' => 'Material', 'value' => 'Aluminum Alloy'],
                    ['key' => 'Adjustable Angle', 'value' => '0-45 degrees'],
                    ['key' => 'Weight Capacity', 'value' => '10kg'],
                    ['key' => 'Compatibility', 'value' => '10-17 inch laptops']
                ],
                'tags' => [
                    ['tag' => 'ergonomic'],
                    ['tag' => 'aluminum'],
                    ['tag' => 'adjustable'],
                    ['tag' => 'cooling']
                ],
                'meta_title' => [
                    'en' => 'Professional Laptop Stand - Ergonomic Aluminum Design',
                    'bd' => 'পেশাদার ল্যাপটপ স্ট্যান্ড - এরগোনমিক অ্যালুমিনিয়াম ডিজাইন'
                ],
                'meta_description' => [
                    'en' => 'Ergonomic aluminum laptop stand for better posture and laptop cooling.',
                    'bd' => 'ভালো ভঙ্গি এবং ল্যাপটপ কুলিংয়ের জন্য এরগোনমিক অ্যালুমিনিয়াম ল্যাপটপ স্ট্যান্ড।'
                ]
            ],
            [
                'title' => [
                    'en' => 'Cloud Management Software Suite',
                    'bd' => 'ক্লাউড ম্যানেজমেন্ট সফটওয়্যার স্যুট'
                ],
                'slug' => 'cloud-management-software-suite',
                'short_description' => [
                    'en' => 'Comprehensive cloud management solution for businesses of all sizes.',
                    'bd' => 'সব আকারের ব্যবসার জন্য বিস্তৃত ক্লাউড ম্যানেজমেন্ট সমাধান।'
                ],
                'detailed_description' => [
                    'en' => '<p>Streamline your cloud operations with our comprehensive Cloud Management Software Suite. Designed for efficiency, security, and scalability.</p><h3>Features:</h3><ul><li>Multi-cloud support</li><li>Real-time monitoring</li><li>Automated backups</li><li>Advanced security features</li><li>24/7 technical support</li></ul>',
                    'bd' => '<p>আমাদের বিস্তৃত ক্লাউড ম্যানেজমেন্ট সফটওয়্যার স্যুট দিয়ে আপনার ক্লাউড অপারেশন সহজ করুন। দক্ষতা, নিরাপত্তা এবং স্কেলেবিলিটির জন্য ডিজাইন করা।</p><h3>বৈশিষ্ট্য:</h3><ul><li>মাল্টি-ক্লাউড সমর্থন</li><li>রিয়েল-টাইম মনিটরিং</li><li>স্বয়ংক্রিয় ব্যাকআপ</li><li>উন্নত নিরাপত্তা বৈশিষ্ট্য</li><li>24/7 টেকনিক্যাল সাপোর্ট</li></ul>'
                ],
                'status' => 'published',
                'category_id' => $softwareCategory->id,
                'is_featured' => true,
                'technical_specs' => [
                    ['key' => 'Platform', 'value' => 'Web-based'],
                    ['key' => 'Cloud Support', 'value' => 'AWS, Azure, Google Cloud'],
                    ['key' => 'Users', 'value' => 'Unlimited'],
                    ['key' => 'Support', 'value' => '24/7']
                ],
                'tags' => [
                    ['tag' => 'cloud'],
                    ['tag' => 'management'],
                    ['tag' => 'enterprise'],
                    ['tag' => 'monitoring']
                ],
                'downloads' => [
                    'product-brochure.pdf',
                    'technical-specs.pdf'
                ],
                'meta_title' => [
                    'en' => 'Cloud Management Software Suite - Enterprise Solution',
                    'bd' => 'ক্লাউড ম্যানেজমেন্ট সফটওয়্যার স্যুট - এন্টারপ্রাইজ সমাধান'
                ],
                'meta_description' => [
                    'en' => 'Comprehensive cloud management solution for businesses with multi-cloud support.',
                    'bd' => 'মাল্টি-ক্লাউড সমর্থন সহ ব্যবসার জন্য বিস্তৃত ক্লাউড ম্যানেজমেন্ট সমাধান।'
                ]
            ],
            [
                'title' => [
                    'en' => 'Mechanical Gaming Keyboard RGB',
                    'bd' => 'মেকানিক্যাল গেমিং কীবোর্ড RGB'
                ],
                'slug' => 'mechanical-gaming-keyboard-rgb',
                'short_description' => [
                    'en' => 'High-performance mechanical gaming keyboard with customizable RGB lighting.',
                    'bd' => 'কাস্টমাইজেবল RGB লাইটিং সহ উচ্চ-পারফরম্যান্স মেকানিক্যাল গেমিং কীবোর্ড।'
                ],
                'detailed_description' => [
                    'en' => '<p>Elevate your gaming experience with our Mechanical Gaming Keyboard RGB. Featuring premium mechanical switches, customizable RGB lighting, and programmable keys.</p><h3>Gaming Features:</h3><ul><li>Mechanical switches with 50M+ keystroke lifespan</li><li>Full RGB backlighting with 16.8M colors</li><li>Programmable keys and macros</li><li>Anti-ghosting and N-key rollover</li><li>Durable aluminum frame</li></ul>',
                    'bd' => '<p>আমাদের মেকানিক্যাল গেমিং কীবোর্ড RGB দিয়ে আপনার গেমিং অভিজ্ঞতা উন্নত করুন। প্রিমিয়াম মেকানিক্যাল সুইচ, কাস্টমাইজেবল RGB লাইটিং এবং প্রোগ্রামেবল কী সহ।</p><h3>গেমিং বৈশিষ্ট্য:</h3><ul><li>50M+ কীস্ট্রোক লাইফস্প্যান সহ মেকানিক্যাল সুইচ</li><li>16.8M কালার সহ ফুল RGB ব্যাকলাইটিং</li><li>প্রোগ্রামেবল কী এবং ম্যাক্রোস</li><li>অ্যান্টি-ঘোস্টিং এবং N-কী রোলওভার</li><li>টেকসই অ্যালুমিনিয়াম ফ্রেম</li></ul>'
                ],
                'status' => 'published',
                'category_id' => $hardwareCategory->id,
                'is_featured' => true,
                'technical_specs' => [
                    ['key' => 'Switch Type', 'value' => 'Mechanical Blue'],
                    ['key' => 'Connectivity', 'value' => 'USB-C, Bluetooth 5.0'],
                    ['key' => 'Battery Life', 'value' => '40 hours (wireless)'],
                    ['key' => 'Key Rollover', 'value' => 'N-Key']
                ],
                'tags' => [
                    ['tag' => 'gaming'],
                    ['tag' => 'mechanical'],
                    ['tag' => 'rgb'],
                    ['tag' => 'wireless']
                ],
                'video_embeds' => [
                    [
                        'type' => 'embed',
                        'title' => 'Gaming Demo',
                        'url' => 'https://www.youtube.com/watch?v=gaming456'
                    ]
                ],
                'meta_title' => [
                    'en' => 'Mechanical Gaming Keyboard RGB - Pro Gaming Experience',
                    'bd' => 'মেকানিক্যাল গেমিং কীবোর্ড RGB - প্রো গেমিং অভিজ্ঞতা'
                ],
                'meta_description' => [
                    'en' => 'High-performance mechanical gaming keyboard with RGB lighting and programmable keys.',
                    'bd' => 'RGB লাইটিং এবং প্রোগ্রামেবল কী সহ উচ্চ-পারফরম্যান্স মেকানিক্যাল গেমিং কীবোর্ড।'
                ]
            ],
            [
                'title' => [
                    'en' => 'Wireless Charging Pad',
                    'bd' => 'ওয়্যারলেস চার্জিং প্যাড'
                ],
                'slug' => 'wireless-charging-pad',
                'short_description' => [
                    'en' => 'Fast wireless charging pad for all Qi-enabled devices.',
                    'bd' => 'সব Qi-সক্ষম ডিভাইসের জন্য ফাস্ট ওয়্যারলেস চার্জিং প্যাড।'
                ],
                'detailed_description' => [
                    'en' => '<p>Charge your devices effortlessly with our Wireless Charging Pad. Compatible with all Qi-enabled smartphones and devices.</p><h3>Features:</h3><ul><li>15W fast charging</li><li>Qi-certified</li><li>LED charging indicator</li><li>Anti-slip surface</li><li>Compact and portable design</li></ul>',
                    'bd' => '<p>আমাদের ওয়্যারলেস চার্জিং প্যাড দিয়ে সহজেই আপনার ডিভাইস চার্জ করুন। সব Qi-সক্ষম স্মার্টফোন এবং ডিভাইসের সাথে সামঞ্জস্যপূর্ণ।</p><h3>বৈশিষ্ট্য:</h3><ul><li>15W ফাস্ট চার্জিং</li><li>Qi-সার্টিফায়েড</li><li>LED চার্জিং ইন্ডিকেটর</li><li>অ্যান্টি-স্লিপ সারফেস</li><li>কমপ্যাক্ট এবং পোর্টেবল ডিজাইন</li></ul>'
                ],
                'status' => 'published',
                'category_id' => $accessoriesCategory->id,
                'is_featured' => false,
                'technical_specs' => [
                    ['key' => 'Charging Power', 'value' => '15W'],
                    ['key' => 'Compatibility', 'value' => 'Qi-enabled devices'],
                    ['key' => 'Input', 'value' => 'USB-C PD'],
                    ['key' => 'Safety', 'value' => 'Overcharge protection']
                ],
                'tags' => [
                    ['tag' => 'wireless'],
                    ['tag' => 'charging'],
                    ['tag' => 'qi'],
                    ['tag' => 'fast-charging']
                ],
                'meta_title' => [
                    'en' => 'Wireless Charging Pad - 15W Fast Charging',
                    'bd' => 'ওয়্যারলেস চার্জিং প্যাড - 15W ফাস্ট চার্জিং'
                ],
                'meta_description' => [
                    'en' => 'Fast 15W wireless charging pad compatible with all Qi-enabled devices.',
                    'bd' => 'সব Qi-সক্ষম ডিভাইসের সাথে সামঞ্জস্যপূর্ণ ফাস্ট 15W ওয়্যারলেস চার্জিং প্যাড।'
                ]
            ]
        ];

        foreach ($products as $productData) {
            Product::create([
                'title' => $productData['title'],
                'slug' => $productData['slug'],
                'short_description' => $productData['short_description'],
                'detailed_description' => $productData['detailed_description'],
                'status' => $productData['status'],
                'category_id' => $productData['category_id'],
                'is_featured' => $productData['is_featured'],
                'technical_specs' => $productData['technical_specs'] ?? [],
                'tags' => $productData['tags'] ?? [],
                'video_embeds' => $productData['video_embeds'] ?? [],
                'downloads' => $productData['downloads'] ?? [],
                'published_at' => now(),
                'meta_title' => $productData['meta_title'] ?? [],
                'meta_description' => $productData['meta_description'] ?? [],
                'meta_keywords' => $productData['meta_keywords'] ?? []
            ]);
        }

        $this->command->info('Products seeded successfully!');
    }
}
