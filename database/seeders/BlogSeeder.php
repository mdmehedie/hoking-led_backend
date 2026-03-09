<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@example.com')->first();
        if (!$author) {
            $author = User::first();
        }

        $blogs = [
            [
                'title' => [
                    'en' => '10 Tech Trends That Will Define 2024',
                    'bd' => '২০২৪ কে সংজ্ঞায়িত করবে এমন ১০টি প্রযুক্তি প্রবণতা'
                ],
                'slug' => '10-tech-trends-that-will-define-2024',
                'excerpt' => [
                    'en' => 'Explore the cutting-edge technologies set to revolutionize industries and reshape our digital landscape in 2024.',
                    'bd' => '২০২৪ সালে শিল্পগুলিকে বিপ্লব করতে এবং আমাদের ডিজিটাল ল্যান্ডস্কেপ পুনরায় আকার দিতে যে অত্যাধুনিক প্রযুক্তিগুলি সেট করা হয়েছে তা অন্বেষণ করুন।'
                ],
                'content' => [
                    'en' => '<h2>Introduction</h2><p>As we step into 2024, the technological landscape continues to evolve at an unprecedented pace. From artificial intelligence to quantum computing, innovations are reshaping how we live, work, and interact with the world around us.</p><h3>1. Artificial Intelligence Evolution</h3><p>AI is no longer just a buzzword; it\'s becoming integral to our daily lives. Advanced machine learning algorithms are now powering everything from healthcare diagnostics to autonomous vehicles.</p><h3>2. Quantum Computing Breakthroughs</h3><p>Quantum computers are moving from theoretical concepts to practical applications, promising to solve complex problems that are impossible for classical computers.</p><h3>3. Sustainable Technology</h3><p>Green tech initiatives are gaining momentum, with innovations in renewable energy, electric vehicles, and sustainable manufacturing processes leading the charge.</p><h3>4. Extended Reality (XR)</h3><p>Virtual, augmented, and mixed reality technologies are converging to create immersive experiences that blur the line between digital and physical worlds.</p><h3>5. Blockchain Beyond Crypto</h3><p>Blockchain technology is finding applications beyond cryptocurrency, revolutionizing supply chain management, digital identity, and decentralized finance.</p><h2>Conclusion</h2><p>These technological trends are not just shaping our future; they\'re actively creating it. Stay informed and embrace the changes that lie ahead.</p>',
                    'bd' => '<h2>ভূমিকা</h2><p>যেহেতু আমরা ২০২৪ সালে প্রবেশ করছি, প্রযুক্তিগত ল্যান্ডস্কেপ অবিশ্বাস্য গতিতে অব্যাহত রয়েছে। কৃত্রিম বুদ্ধিমত্তা থেকে কোয়ান্টাম কম্পিউটিং পর্যন্ত, উদ্ভাবনগুলি আমাদের কীভাবে বাস করি, কাজ করি এবং আমাদের চারপাশের বিশ্বের সাথে ইন্টারঅ্যাক্ট করি তা পুনর্গঠন করছে।</p><h3>১. কৃত্রিম বুদ্ধিমত্তা বিবর্তন</h3><p>এআই আর শুধু একটি বাজওয়ার্ড নয়; এটি আমাদের দৈনন্দিন জীবনের অবিচ্ছেদ্য অংশ হয়ে উঠছে। উন্নত মেশিন লার্নিং অ্যালগরিদম এখন হেলথকেয়ার ডায়াগনস্টিক্স থেকে শুরু করে স্বায়ত্তশাসিত যানবাহন পর্যন্ত সবকিছু চালাচ্ছে।</p><h3>২. কোয়ান্টাম কম্পিউটিং ব্রেকথ্রু</h3><p>কোয়ান্টাম কম্পিউটারগুলি তাত্ত্বিক ধারণা থেকে ব্যবহারিক অ্যাপ্লিকেশনে চলে যাচ্ছে, যা ধ্রুপদী কম্পিউটারগুলির জন্য অসম্ভব জটিল সমস্যাগুলি সমাধান করার প্রতিশ্রুতি দেয়।</p><h3>৩. টেকসই প্রযুক্তি</h3><p>গ্রিন টেক উদ্যোগগুলি গতি পাচ্ছে, নবায়নযোগ্য শক্তি, বৈদ্যুতিক যানবাহন এবং টেকস্ই উত্পাদন প্রক্রিয়াগুলিতে উদ্ভাবনের নেতৃত্ব দিচ্ছে।</p><h3>৪. এক্সটেন্ডেড রিয়েলিটি (XR)</h3><p>ভার্চুয়াল, অগমেন্টেড এবং মিক্সড রিয়েলিটি প্রযুক্তিগুলি ডিজিটাল এবং শারীরিক জগতের মধ্যে রেখাটি ঝাপসা করে এমন নিমগ্ন অভিজ্ঞতা তৈরি করতে একত্রিত হচ্ছে।</p><h3>৫. ক্রিপ্টোর বাইরে ব্লকচেইন</h3><p>ব্লকচেইন প্রযুক্তি ক্রিপ্টোকারেন্সির বাইরে অ্যাপ্লিকেশন খুঁজে পেয়েছে, সাপ্লাই চেইন ম্যানেজমেন্ট, ডিজিটাল পরিচয় এবং বিকেন্দ্রীকৃত অর্থায়নে বিপ্লব ঘটাচ্ছে।</p><h2>উপসংহার</h2><p>এই প্রযুক্তিগত প্রবণতাগুলি শুধু আমাদের ভবিষ্যৎকে আকার দিচ্ছে না; এগুলি সক্রিয়ভাবে এটি তৈরি করছে। তথ্যবান থাকুন এবং সামনের পরিবর্তনগুলি গ্রহণ করুন।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(5),
                'meta_title' => [
                    'en' => '10 Tech Trends That Will Define 2024 | Future Technology Insights',
                    'bd' => '২০২৪ কে সংজ্ঞায়িত করবে এমন ১০টি প্রযুক্তি প্রবণতা | ভবিষ্যৎ প্রযুক্তি অন্তর্দৃষ্টি'
                ],
                'meta_description' => [
                    'en' => 'Discover the top 10 technology trends that will shape 2024, from AI evolution to quantum computing breakthroughs and sustainable tech innovations.',
                    'bd' => 'এআই বিবর্তন থেকে কোয়ান্টাম কম্পিউটিং ব্রেকথ্রু এবং টেকসই টেক উদ্ভাবন পর্যন্ত ২০২৪ কে আকার দেবে এমন শীর্ষ ১০ টি প্রযুক্তি প্রবণতা আবিষ্কার করুন।'
                ],
                'meta_keywords' => [
                    'en' => 'technology trends 2024, AI, quantum computing, sustainable tech, XR, blockchain, innovation',
                    'bd' => 'প্রযুক্তি প্রবণতা ২০২৪, এআই, কোয়ান্টাম কম্পিউটিং, টেকসই টেক, XR, ব্লকচেইন, উদ্ভাবন'
                ]
            ],
            [
                'title' => [
                    'en' => 'The Future of Remote Work: Trends and Predictions',
                    'bd' => 'রিমোট ওয়ার্কের ভবিষ্যৎ: প্রবণতা এবং পূর্বাভাস'
                ],
                'slug' => 'future-of-remote-work-trends-predictions',
                'excerpt' => [
                    'en' => 'How remote work is evolving and what it means for businesses and employees in the coming years.',
                    'bd' => 'রিমোট ওয়ার্ক কীভাবে বিবর্তিত হচ্ছে এবং আসন্ন বছরগুলিতে এটি ব্যবসা এবং কর্মচারীদের জন্য কী বোঝায়।'
                ],
                'content' => [
                    'en' => '<h2>The Remote Work Revolution</h2><p>The COVID-19 pandemic accelerated the shift to remote work, but what started as a necessity has evolved into a permanent fixture of the modern workplace. As we look ahead, several key trends are emerging.</p><h3>Hybrid Models Dominate</h3><p>Most companies are settling into hybrid work arrangements, combining remote and in-office work. This flexibility offers the best of both worlds, maintaining team collaboration while providing work-life balance.</p><h3>Technology Enables Connection</h3><p>Advanced collaboration tools, virtual reality meeting spaces, and AI-powered productivity apps are making remote work more efficient and engaging than ever before.</p><h3>Global Talent Pool</h3><p>Remote work has eliminated geographical barriers, allowing companies to hire the best talent regardless of location. This is democratizing opportunities and creating more diverse workplaces.</p><h3>Focus on Results</h3><p>The shift to remote work has emphasized results over hours worked. Companies are learning to measure productivity by outcomes rather than time spent at a desk.</p><h2>Challenges Ahead</h2><p>While remote work offers many benefits, challenges remain around maintaining company culture, preventing burnout, and ensuring cybersecurity. Companies that address these issues will thrive in the new normal.</p>',
                    'bd' => '<h2>রিমোট ওয়ার্ক বিপ্লব</h2><p>কোভিড-১৯ মহামারী রিমোট ওয়ার্কের দিকে রূপান্তরকে ত্বরান্বিত করেছে, কিন্তু যা একটি প্রয়োজনীয়তা হিসাবে শুরু হয়েছিল তা আধুনিক কর্মক্ষেত্রের একটি স্থায়ী বৈশিষ্ট্যে পরিণত হয়েছে। যেহেতু আমরা এগিয়ে যাচ্ছি, বেশ কয়েকটি মূল প্রবণতা আবির্ভূত হচ্ছে।</p><h3>হাইব্রিড মডেল আধিপত্য</h3><p>বেশিরভাগ কোম্পানি হাইব্রিড কাজের ব্যবস্থায় বসতে শুরু করেছে, রিমোট এবং অফিসে কাজ একত্রিত করে। এই নমনীয়তা উভয় বিশ্বের সেরাটি অফার করে, টিম সহযোগিতা বজায় রাখার সময় কাজ-জীবন ভারসাম্য প্রদান করে।</p><h3>প্রযুক্তি সংযোগ সক্ষম করে</h3><p>উন্নত সহযোগিতা সরঞ্জাম, ভার্চুয়াল রিয়েলিটি মিটিং স্পেস এবং AI-চালিত প্রোডাক্টিভিটি অ্যাপ্স রিমোট ওয়ার্ককে আগের চেয়ে বেশি কার্যকর এবং আকর্ষণীয় করে তুলছে।</p><h3>গ্লোবাল ট্যালেন্ট পুল</h3><p>রিমোট ওয়ার্ক ভৌগলিক বাধা দূর করে দিয়েছে, যা কোম্পানিগুলিকে অবস্থান নির্বিশেষে সেরা প্রতিভা নিয়োগ করতে দেয়। এটি সুযোগগুলিকে গণতান্ত্রিক করছে এবং আরও বৈচিত্র্যময় কর্মক্ষেত্র তৈরি করছে।</p><h3>ফলাফলের উপর ফোকাস</h3><p>রিমোট ওয়ার্কের দিকে রূপান্তর কাজ করা ঘন্টার উপর ফলাফলের উপর জোর দিয়েছে। কোম্পানিগুলি ডেস্কে কাটানো সময়ের পরিবর্তে ফলাফল দ্বারা উত্পাদনশীলতা পরিমাপ করতে শিখছে।</p><h2>সামনের চ্যালেঞ্জ</h2><p>রিমোট ওয়ার্ক অনেক সুবিধা অফার করলেও, কোম্পানির সংস্কৃতি বজায় রাখা, বার্নআউট প্রতিরোধ করা এবং সাইবার সুরক্ষা নিশ্চিত করার চারপাশে চ্যালেঞ্জ রয়েছে। যে কোম্পানিগুলি এই সমস্যাগুলি সমাধান করবে তারা নতুন স্বাভাবিকতায় উন্নতি লাভ করবে।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(3),
                'meta_title' => [
                    'en' => 'The Future of Remote Work: Hybrid Models and Global Talent',
                    'bd' => 'রিমোট ওয়ার্কের ভবিষ্যৎ: হাইব্রিড মডেল এবং গ্লোবাল ট্যালেন্ট'
                ],
                'meta_description' => [
                    'en' => 'Explore how remote work is evolving with hybrid models, global talent pools, and new technologies shaping the future of work.',
                    'bd' => 'হাইব্রিড মডেল, গ্লোবাল ট্যালেন্ট পুল এবং কাজের ভবিষ্যৎ গঠন করা নতুন প্রযুক্তির সাথে রিমোট ওয়ার্ক কীভাবে বিবর্তিত হচ্ছে তা অন্বেষণ করুন।'
                ],
                'meta_keywords' => [
                    'en' => 'remote work, hybrid work, future of work, distributed teams, workplace trends',
                    'bd' => 'রিমোট ওয়ার্ক, হাইব্রিড ওয়ার্ক, কাজের ভবিষ্যৎ, বিতরণকৃত টিম, কর্মক্ষেত্র প্রবণতা'
                ]
            ],
            [
                'title' => [
                    'en' => 'Cybersecurity Best Practices for Small Businesses',
                    'bd' => 'ছোট ব্যবসার জন্য সাইবার সুরক্ষা সেরা অনুশীলন'
                ],
                'slug' => 'cybersecurity-best-practices-small-businesses',
                'excerpt' => [
                    'en' => 'Essential cybersecurity measures every small business should implement to protect their digital assets.',
                    'bd' => 'তাদের ডিজিটাল সম্পদ রক্ষা করতে প্রতিটি ছোট ব্যবসা বাস্তবায়ন করা উচিত এমন প্রয়োজনীয় সাইবার সুরক্ষা ব্যবস্থা।'
                ],
                'content' => [
                    'en' => '<h2>Why Cybersecurity Matters</h2><p>Small businesses are increasingly becoming targets for cybercriminals. With limited resources and often inadequate security measures, they present attractive targets. Implementing robust cybersecurity practices is no longer optional—it\'s essential for survival.</p><h3>1. Employee Training</h3><p>Your employees are your first line of defense. Regular training on phishing, password security, and safe browsing habits can prevent most security breaches.</p><h3>2. Strong Password Policies</h3><p>Implement complex password requirements and two-factor authentication. Consider using password managers to generate and store secure passwords.</p><h3>3. Regular Software Updates</h3><p>Keep all software, including operating systems and applications, updated with the latest security patches. Enable automatic updates where possible.</p><h3>4. Data Backup Strategy</h3><p>Implement the 3-2-1 backup rule: three copies of your data, on two different media types, with one copy off-site. Test your backups regularly.</p><h3>5. Network Security</h3><p>Secure your Wi-Fi networks, use firewalls, and consider VPN access for remote workers. Regularly monitor network traffic for suspicious activity.</p><h2>Incident Response Plan</h2><p>Despite your best efforts, breaches can still occur. Having a clear incident response plan can minimize damage and recovery time.</p>',
                    'bd' => '<h2>কেন সাইবার সুরক্ষা গুরুত্বপূর্ণ</h2><p>সাইবার অপরাধীদের জন্য ছোট ব্যবসাগুলি ক্রমবর্ধমানভাবে লক্ষ্যবস্তুতে পরিণত হচ্ছে। সীমিত সংস্থান এবং প্রায়শই অপর্যাপ্ত নিরাপত্তা ব্যবস্থার সাথে, তারা আকর্ষণীয় লক্ষ্যবস্তু উপস্থাপন করে। শক্তিশালী সাইবার সুরক্ষা অনুশীলন বাস্তবায়ন আর ঐচ্ছিক নয়—এটি বেঁচে থাকার জন্য অপরিহার্য।</p><h3>১. কর্মচারী প্রশিক্ষণ</h3><p>আপনার কর্মচারীরা আপনার প্রথম প্রতিরক্ষা লাইন। ফিশিং, পাসওয়ার্ড সুরক্ষা এবং নিরাপদ ব্রাউজিং অভ্যাসের উপর নিয়মিত প্রশিক্ষণ বেশিরভাগ সুরক্ষা লঙ্ঘন প্রতিরোধ করতে পারে।</p><h3>২. শক্তিশালী পাসওয়ার্ড নীতি</h3><p>জটিল পাসওয়ার্ড প্রয়োজনীয়তা এবং দুই-ফ্যাক্টর প্রমাণীকরণ বাস্তবায়ন করুন। নিরাপদ পাসওয়ার্ড তৈরি এবং সংরক্ষণ করতে পাসওয়ার্ড ম্যানেজার ব্যবহার করার কথা বিবেচনা করুন।</p><h3>৩. নিয়মিত সফটওয়্যার আপডেট</h3><p>অপারেটিং সিস্টেম এবং অ্যাপ্লিকেশন সহ সমস্ত সফটওয়্যার সর্বশেষ সুরক্ষা প্যাচ সহ আপডেট রাখুন। যেখানে সম্ভব স্বয়ংক্রিয় আপডেট সক্ষম করুন।</p><h3>৪. ডেটা ব্যাকআপ কৌশল</h3><p>৩-২-১ ব্যাকআপ নিয়ম বাস্তবায়ন করুন: আপনার ডেটার তিনটি অনুলিপি, দুটি ভিন্ন মিডিয়া টাইপে, একটি অনুলিপি অফ-সাইটে। নিয়মিত আপনার ব্যাকআপ পরীক্ষা করুন।</p><h3>৫. নেটওয়ার্ক সুরক্ষা</h3><p>আপনার Wi-Fi নেটওয়ার্কগুলি সুরক্ষিত করুন, ফায়ারওয়াল ব্যবহার করুন এবং রিমোট কর্মীদের জন্য VPN অ্যাক্সেস বিবেচনা করুন। সন্দেহজনক ক্রিয়াকলাপের জন্য নিয়মিত নেটওয়ার্ক ট্রাফিক পর্যবেক্ষণ করুন।</p><h2>ঘটনা প্রতিক্রিয়া পরিকল্পনা</h2><p>আপনার সেরা প্রচেষ্টা সত্ত্বেও, লঙ্ঘন এখনও ঘটতে পারে। একটি পরিষ্কার ঘটনা প্রতিক্রিয়া পরিকল্পনা থাকা ক্ষতি এবং পুনরুদ্ধার সময় হ্রাস করতে পারে।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(1),
                'meta_title' => [
                    'en' => 'Cybersecurity Best Practices for Small Businesses | Complete Guide',
                    'bd' => 'ছোট ব্যবসার জন্য সাইবার সুরক্ষা সেরা অনুশীলন | সম্পূর্ণ গাইড'
                ],
                'meta_description' => [
                    'en' => 'Learn essential cybersecurity measures for small businesses including employee training, password policies, and data backup strategies.',
                    'bd' => 'কর্মচারী প্রশিক্ষণ, পাসওয়ার্ড নীতি এবং ডেটা ব্যাকআপ কৌশল সহ ছোট ব্যবসার জন্য প্রয়োজনীয় সাইবার সুরক্ষা ব্যবস্থা শিখুন।'
                ],
                'meta_keywords' => [
                    'en' => 'cybersecurity, small business, data protection, security best practices, employee training',
                    'bd' => 'সাইবার সুরক্ষা, ছোট ব্যবসা, ডেটা সুরক্ষা, সুরক্ষা সেরা অনুশীলন, কর্মচারী প্রশিক্ষণ'
                ]
            ]
        ];

        foreach ($blogs as $blogData) {
            Blog::updateOrCreate(
                ['slug' => $blogData['slug']],
                [
                    'title' => json_encode($blogData['title']),
                    'excerpt' => json_encode($blogData['excerpt']),
                    'content' => json_encode($blogData['content']),
                    'image_path' => json_encode($blogData['image_path'] ?? ['en' => 'blog-placeholder.jpg', 'bd' => 'blog-placeholder.jpg']),
                    'status' => $blogData['status'],
                    'author_id' => $blogData['author_id'],
                    'published_at' => $blogData['published_at'],
                    'meta_title' => json_encode($blogData['meta_title'] ?? []),
                    'meta_description' => json_encode($blogData['meta_description'] ?? []),
                    'meta_keywords' => json_encode($blogData['meta_keywords'] ?? [])
                ]
            );
        }

        $this->command->info('Blogs seeded successfully!');
    }
}
