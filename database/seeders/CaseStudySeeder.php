<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@example.com')->first();
        if (!$author) {
            $author = User::first();
        }

        $caseStudies = [
            [
                'title' => [
                    'en' => 'Digital Transformation Success: Global Retail Chain',
                    'bd' => 'ডিজিটাল রূপান্তরণ সাফল্য: গ্লোবাল খুচরা চেইন'
                ],
                'slug' => 'digital-transformation-global-retail-chain',
                'excerpt' => [
                    'en' => 'How a leading retail chain transformed their operations with our comprehensive digital solution.',
                    'bd' => 'কিভাবে একটি অগ্রণীয় খুচরা চেইন আমাদের বিস্তৃত ডিজিটাল সমাধান দিয়ে তাদের অপারেশন রূপান্তরিত করেছিল।'
                ],
                'content' => [
                    'en' => '<h2>The Challenge</h2><p>Global Retail Chain, a multinational corporation with over 500 stores worldwide, was struggling with outdated legacy systems, inefficient inventory management, and poor customer experience across their digital platforms.</p><h3>Key Problems Identified:</h3><ul><li>Siloed data across multiple systems</li><li>Manual inventory processes causing stockouts</li><li>Inconsistent customer experience</li><li>High operational costs</li></ul><h2>Our Solution</h2><p>We implemented a comprehensive digital transformation strategy including:</p><h3>1. Unified Commerce Platform</h3><p>Integrated all sales channels into a single platform, providing real-time inventory visibility and consistent customer experience across online and offline channels.</p><h3>2. AI-Powered Analytics</h3><p>Implemented predictive analytics for demand forecasting, reducing stockouts by 40% and overstock situations by 35%.</p><h3>3. Mobile-First Customer Experience</h3><p>Redesigned mobile app with personalized recommendations, seamless checkout process, and integrated loyalty program.</p><h2>Results</h2><p>The transformation delivered exceptional results within 12 months:</p><h3>Quantifiable Achievements:</h3><ul><li>45% increase in online sales</li><li>60% reduction in inventory costs</li><li>30% improvement in customer satisfaction</li><li>50% faster time-to-market for new products</li></ul><h2>Client Testimonial</h2><p>"The digital transformation has revolutionized how we operate. We\'re more efficient, our customers are happier, and we\'re seeing significant growth across all channels." - CEO, Global Retail Chain</p>',
                    'bd' => '<h2>চ্যালেঞ্জ</h2><p>বিশ্বব্যাপী খুচরা চেইন, বিশ্বজুড়ে ৫০০ এরও বেশি স্টোর সহ একটি বহুলাত্তাকারী কর্পোরেশন, অদক্ষ উত্তরাধিকারী ব্যবস্থাপনার সিস্টেম, অদক্ষষম ইনভেন্টরি ব্যবস্থাপনা এবং তাদের ডিজিটাল প্ল্যাটফর্ম জুড়ে খারাপ গ্রাহক অভিজ্ঞতার সাথে লড়াইছিল।</p><h3>মূল সমস্যা চিহ্নিত:</h3><ul><li>একাধিক সিস্টেম জুড়ে সিলোড করা ডেটা</li><li>স্টকআউট সৃষ্টি করা ম্যানুয়াল ইনভেন্টরি প্রক্রিয়া</li><li>অসঙ্গতিপূর্ণ গ্রাহক অভিজ্ঞতা</li><li>উচ্চ পরিচালনামূলক খরচ</li></ul><h2>আমাদের সমাধান</h2><p>আমরা একটি বিস্তৃত ডিজিটাল রূপান্তরণ কৌশল বাস্তবায়ন করেছি যার মধ্যে রয়েছে:</p><h3>১. একীভূত বাণিজ্য প্ল্যাটফর্ম</h3><p>সমস্ত বিক্রয় চ্যানেলগুলিকে একটি প্ল্যাটফর্মে একীভূত করেছি, রিয়েল-টাইম ইনভেন্টরি দৃশ্যমানতা এবং অনলাইন এবং অফলাইন চ্যানেল জুড়ে সামঞ্জস্য গ্রাহক অভিজ্ঞতা প্রদান করে।</p><h3>২. AI-চালিত অ্যানালিটিক্স</h3><p>চাহিদা পূর্বাভাসের জন্য পূর্বাভাসমূলক অ্যানালিটিক্স বাস্তবায়ন করেছি, যা স্টকআউট ৪০% কমিয়েছে এবং ওভারস্টক পরিস্থিতি ৩৫% কমিয়েছে।</p><h3>৩. মোবাইল-প্রথম গ্রাহক অভিজ্ঞতা</h3><p>ব্যক্তিগত সুপারিশেশন, নিরবিচ্ছিন্ন চেকআউট প্রক্রিয়া এবং একীভূত লয়্যালটি প্রোগ্রাম সহ মোবাইল অ্যাপটি পুনরায় ডিজাইন করেছি।</p><h2>ফলাফল</h2><p>রূপান্তরণটি ১২ মাসের মধ্যে অসাধারণ ফলাফল দিয়েছে:</p><h3>পরিমাণযোগ্য সাফল্য:</h3><ul><li>অনলাইন বিক্রয়ে ৪৫% বৃদ্ধি</li><li>ইনভেন্টরি খরচে ৬০% হ্রাস</li><li>গ্রাহক সন্তুষ্টিতে ৩০% উন্নতি</li><li>নতুন পণ্যের বাজারে আসতে ৫০% দ্রুততর সময়</li></ul><h2>ক্লায়েন্ট সাক্ষ্যাতমূলক</h2><p>"ডিজিটাল রূপান্তরণ আমাদের কাজ করার উপায় বিপ্লব করেছে। আমরা আরও দক্ষম, আমাদের গ্রাহকরা খুশি, এবং আমরা সমস্ত চ্যানেলে উল্লেখযোগ্য বৃদ্ধি দেখতে পাচ্ছি।" - সিইও, গ্লোবাল খুচরা চেইন</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(10),
                'meta_title' => [
                    'en' => 'Digital Transformation Case Study: Global Retail Chain Success Story',
                    'bd' => 'ডিজিটাল রূপান্তরণ কেস স্টাডি: গ্লোবাল খুচরা চেইন সাফল্যের গল্প'
                ],
                'meta_description' => [
                    'en' => 'Learn how Global Retail Chain achieved 45% sales growth through comprehensive digital transformation with unified commerce platform and AI analytics.',
                    'bd' => 'গ্লোবাল খুচরা চেইন কিভাবে একীভূত বাণিজ্য প্ল্যাটফর্ম এবং AI অ্যানালিটিক্স সহ বিস্তৃত ডিজিটাল রূপান্তরণের মাধ্যমে ৪৫% বিক্রয় বৃদ্ধি অর্জন করেছে তা জানুন।'
                ],
                'meta_keywords' => [
                    'en' => 'digital transformation, retail, e-commerce, AI analytics, case study',
                    'bd' => 'ডিজিটাল রূপান্তরণ, খুচরা, ই-কমার্স, AI অ্যানালিটিক্স, কেস স্টাডি'
                ]
            ],
            [
                'title' => [
                    'en' => 'Healthcare Innovation: Smart Hospital Management System',
                    'bd' => 'স্বাস্থ্যসেবা উদ্ভাবন: স্মার্ট হাসপাতাল ম্যানেজমেন্ট সিস্টেম'
                ],
                'slug' => 'healthcare-innovation-smart-hospital-system',
                'excerpt' => [
                    'en' => 'Revolutionizing patient care with AI-powered hospital management and telemedicine integration.',
                    'bd' => 'AI-চালিত হাসপাতাল ব্যবস্থাপনা এবং টেলিমেডিসিন ইন্টিগ্রেশন সহ রোগীর যত্ন বিপ্লবীকরণ।'
                ],
                'content' => [
                    'en' => '<h2>The Healthcare Challenge</h2><p>Metropolitan General Hospital, a 500-bed facility, was facing challenges with patient management, resource allocation, and inefficient communication between departments.</p><h3>Critical Issues:</h3><ul><li>Long patient wait times</li><li>Inefficient resource utilization</li><li>Paper-based record keeping</li><li>Limited telemedicine capabilities</li></ul><h2>Our Innovative Solution</h2><p>We developed a comprehensive smart hospital management system that transformed their operations:</p><h3>1. AI-Powered Patient Management</h3><p>Implemented intelligent scheduling system that reduced wait times by 60% and optimized resource allocation based on predictive analytics.</p><h3>2. Digital Health Records</h3><p>Replaced paper-based systems with secure, interoperable digital health records accessible to authorized staff across all departments.</p><h3>3. Telemedicine Platform</h3><p>Built integrated telemedicine solution enabling remote consultations and continuous monitoring of chronic conditions.</p><h2>Impact and Results</h2><p>The transformation delivered remarkable improvements in patient care and operational efficiency:</p><h3>Key Achievements:</h3><ul><li>60% reduction in patient wait times</li><li>40% improvement in resource utilization</li><li>50% increase in telemedicine adoption</li><li>35% reduction in administrative costs</li></ul><h2>Patient Experience</h2><p>"The new system has made my hospital visits so much better. I can book appointments online, access my records easily, and even have virtual consultations when needed." - Sarah Johnson, Patient</p>',
                    'bd' => '<h2>স্বাস্থ্যসেবা চ্যালেঞ্জ</h2><p>মেট্রোপলিটন জেনারেল হাসপাতাল, একটি ৫০০-শয্যা সুবিধান, রোগী ব্যবস্থাপনা, সম্পদ বরাদ্দমান এবং বিভাগের মধ্যে অদক্ষষম যোগাযোগের সাথে লড়াইছিল।</p><h3>সমালোচনীয় সমস্যা:</h3><ul><li>দীর্ঘ রোগী অপেক্ষার সময়</li><li>অদক্ষষম সম্পদ ব্যবহার</li><li>কাগজ-ভিত্তিক রেকর্ড রক্ষণ</li><li>সীমিত টেলিমেডিসিন ক্ষমতা</li></ul><h2>আমাদের উদ্ভাবনী সমাধান</h2><p>আমরা একটি বিস্তৃত স্মার্ট হাসপাতাল ব্যবস্থাপনা সিস্টেম তৈরি করেছি যা তাদের অপারেশন রূপান্তরিত করেছে:</p><h3>১. AI-চালিত রোগী ব্যবস্থাপনা</h3><p>বুদ্ধিমান সময়সূচী ব্যবস্থা বাস্তবায়ন করেছি যা অপেক্ষার সময় ৬০% কমিয়েছে এবং পূর্বাভাসমূলক অ্যানালিটিক্সের উপর ভিত্তিতে সম্পদ বরাদ্দমান অপ্টিমাইজ করেছে।</p><h3>২. ডিজিটাল স্বাস্থ্য রেকর্ড</h3><p>নিরাপদ, আন্তঃসংযোগযোগ্য ডিজিটাল স্বাস্থ্য রেকর্ড দিয়ে কাগজ-ভিত্তিক সিস্টেম প্রতিস্থাপিত করেছি যা সব বিভাগের অনুমোদিত কর্মীদের কাছে অ্যাক্সেসযোগ্য।</p><h3>৩. টেলিমেডিসিন প্ল্যাটফর্ম</h3><p>দূরবর্তী পরামর্শ এবং দীর্ঘস্থায়ী অবস্থার নিরবিচ্ছিন্ন পরামর্শ সক্ষম করে একীভূত টেলিমেডিসিন সমাধান তৈরি করেছি।</p><h2>প্রভাব এবং ফলাফল</h2><p>রূপান্তরণটি রোগীর যত্ন এবং পরিচালনামূলক দক্ষতায় উল্লেখযোগ্য উন্নতি এনেছে:</p><h3>মূল সাফল্য:</h3><ul><li>রোগী অপেক্ষার সময় ৬০% হ্রাস</li><li>সম্পদ ব্যবহারে ৪০% উন্নতি</li><li>টেলিমেডিসিন গ্রহণে ৫০% বৃদ্ধি</li><li>প্রশাসনিক খরচে ৩৫% হ্রাস</li></ul><h2>রোগী অভিজ্ঞতা</h2><p>"নতুন সিস্টেম আমার হাসপাতাল ভ্রমণগুলিকে অনেক ভালো করেছে। আমি অনলাইনে অ্যাপয়েন্টমেন্ট বুক করতে পারি, আমার রেকর্ডগুলি সহজেই অ্যাক্সেস করতে পারি, এবং প্রয়োজনীয় ভার্চুয়াল পরামর্শ পেতে পারি।" - সারা জনসন, রোগী</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(8),
                'meta_title' => [
                    'en' => 'Smart Hospital Management System Case Study | Healthcare Innovation',
                    'bd' => 'স্মার্ট হাসপাতাল ম্যানেজমেন্ট সিস্টেম কেস স্টাডি | স্বাস্থ্যসেবা উদ্ভাবন'
                ],
                'meta_description' => [
                    'en' => 'How Metropolitan General Hospital achieved 60% reduction in wait times with AI-powered patient management and telemedicine integration.',
                    'bd' => 'মেট্রোপলিটন জেনারেল হাসপাতাল AI-চালিত রোগী ব্যবস্থাপনা এবং টেলিমেডিসিন ইন্টিগ্রেশন সহ অপেক্ষার সময় ৬০% হ্রাস অর্জন করেছে কিভাবে জানুন।'
                ],
                'meta_keywords' => [
                    'en' => 'healthcare innovation, hospital management, telemedicine, AI in healthcare, digital health',
                    'bd' => 'স্বাস্থ্যসেবা উদ্ভাবন, হাসপাতাল ব্যবস্থাপনা, টেলিমেডিসিন, স্বাস্থ্যসেবায় AI, ডিজিটাল স্বাস্থ্য'
                ]
            ],
            [
                'title' => [
                    'en' => 'Manufacturing Excellence: Smart Factory Implementation',
                    'bd' => 'উত্পাদনা শ্রেষ্ঠতা: স্মার্ট ফ্যাক্টরি বাস্তবায়ন'
                ],
                'slug' => 'manufacturing-excellence-smart-factory',
                'excerpt' => [
                    'en' => 'Transforming traditional manufacturing into Industry 4.0 with IoT and automation technologies.',
                    'bd' => 'IoT এবং অটোমেশন প্রযুক্তিগুলি সহ ঐতিহ্যাসিক উত্পাদনাকে ইন্ডাস্ট্রি ৪.০-এ রূপান্তরণ।'
                ],
                'content' => [
                    'en' => '<h2>The Manufacturing Challenge</h2><p>Traditional Manufacturing Corp. was facing declining productivity, quality control issues, and increasing competition from modern facilities.</p><h3>Key Challenges:</h3><ul><li>Aging equipment and manual processes</li><li>Inconsistent product quality</li><li>High energy consumption</li><li>Limited real-time visibility</li></ul><h2>Industry 4.0 Transformation</h2><p>We implemented a comprehensive smart factory solution that revolutionized their operations:</p><h3>1. IoT-Enabled Equipment</h3><p>Connected all machinery to IoT sensors for real-time monitoring, predictive maintenance, and performance optimization.</p><h3>2. Automated Quality Control</h3><p>Implemented computer vision systems for automated quality inspection, reducing defects by 75% and improving consistency.</p><h3>3. Smart Energy Management</h3><p>Deployed intelligent energy management system that reduced consumption by 30% while maintaining productivity levels.</p><h2>Transformation Results</h2><p>The smart factory implementation delivered exceptional results:</p><h3>Performance Improvements:</h3><ul><li>55% increase in productivity</li><li>75% reduction in defect rates</li><li>30% decrease in energy costs</li><li>40% faster changeover times</li></ul><h2>Employee Impact</h2><p>"The new smart systems have made our jobs easier and safer. We can focus on quality improvement rather than routine tasks." - Production Manager</p>',
                    'bd' => '<h2>উত্পাদনা চ্যালেঞ্জ</h2><p>ঐতিহ্যাসিক উত্পাদনা কর্পোরেশন ক্রমহীন উত্পাদনশীলতা, মানের নিয়ন্ত্রণ সমস্যা এবং আধুনিক সুবিধান থেকে বর্ধিত প্রতিযোগিতার মুখোমুখি হচ্ছিল।</p><h3>মূল চ্যালেঞ্জ:</h3><ul><li>পুরনো সরঞ্জাম এবং ম্যানুয়াল প্রক্রিয়া</li><li>অসঙ্গতিপূর্ণ পণ্যের মান</li><li>উচ্চ শক্তি খরচ</li><li>সীমিত রিয়েল-টাইম দৃশ্যমানতা</li></ul><h2>ইন্ডাস্ট্রি ৪.০ রূপান্তরণ</h2><p>আমরা একটি বিস্তৃত স্মার্ট ফ্যাক্টরি সমাধান বাস্তবায়ন করেছি যা তাদের অপারেশন বিপ্লবীকরণ করেছে:</p><h3>১. IoT-সক্ষম সরঞ্জাম</h3><p>রিয়েল-টাইম মনিটরিং, পূর্বাভাসমূলক রক্ষণাবক্ষণ এবং পারফরম্যান্স অপ্টিমাইজেশনের জন্য সব যন্ত্রপাত্রকে IoT সেন্সরের সাথে সংযোগ করেছি।</p><h3>২. স্বয়ংক্রিয় মানের নিয়ন্ত্রণ</h3><p>স্বয়ংক্রিয় মানের পরিদর্শনের জন্য কম্পিউটার ভিশন সিস্টেম বাস্তবায়ন করেছি, যা ত্রুটি ৭৫% কমিয়েছে এবং ধারাবাহিকতা উন্নত করেছে।</p><h3>৩. স্মার্ট শক্তি ব্যবস্থাপনা</h3><p>উত্পাদনশীলতা স্তর বজায় রেখে বুদ্ধিমান শক্তি ব্যবস্থাপনা সিস্টেম চালু করেছি যা খরচ ৩০% কমিয়েছে।</p><h2>রূপান্তরণ ফলাফল</h2><p>স্মার্ট ফ্যাক্টরি বাস্তবায়ন অসাধারণ ফলাফল দিয়েছে:</p><h3>পারফরম্যান্স উন্নতি:</h3><ul><li>উত্পাদনশীলতায় ৫৫% বৃদ্ধি</li><li>ত্রুটি হার ৭৫% হ্রাস</li><li>শক্তি খরচে ৩০% হ্রাস</li><li>পরিবর্তন সময় ৪০% দ্রুততর</li></ul><h2>কর্মচারী প্রভাব</h2><p>"নতুন স্মার্ট সিস্টেমগুলি আমাদের কাজগুলিকে সহজ এবং নিরাপদ করেছে। আমরা রুটিন কাজের পরিবর্তে মান উন্নতির উপর মনোযোগ দিতে পারি।" - উত্পাদন ব্যবস্থাপক</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subDays(6),
                'meta_title' => [
                    'en' => 'Smart Factory Implementation Case Study | Industry 4.0 Manufacturing',
                    'bd' => 'স্মার্ট ফ্যাক্টরি বাস্তবায়ন কেস স্টাডি | ইন্ডাস্ট্রি ৪.০ উত্পাদনা'
                ],
                'meta_description' => [
                    'en' => 'How Traditional Manufacturing Corp achieved 55% productivity increase with IoT-enabled equipment and automated quality control systems.',
                    'bd' => 'ঐতিহ্যাসিক উত্পাদনা কর্পোরেশন IoT-সক্ষম সরঞ্জাম এবং স্বয়ংক্রিয় মানের নিয়ন্ত্রণ সিস্টেম সহ উত্পাদনশীলতায় ৫৫% বৃদ্ধি অর্জন করেছে কিভাবে জানুন।'
                ],
                'meta_keywords' => [
                    'en' => 'smart factory, Industry 4.0, IoT, manufacturing automation, digital transformation',
                    'bd' => 'স্মার্ট ফ্যাক্টরি, ইন্ডাস্ট্রি ৪.০, IoT, উত্পাদনা অটোমেশন, ডিজিটাল রূপান্তরণ'
                ]
            ]
        ];

        foreach ($caseStudies as $caseStudyData) {
            CaseStudy::updateOrCreate(
                ['slug' => $caseStudyData['slug']],
                [
                    'title' => json_encode($caseStudyData['title']),
                    'excerpt' => json_encode($caseStudyData['excerpt']),
                    'content' => json_encode($caseStudyData['content']),
                    'image_path' => json_encode($caseStudyData['image_path'] ?? ['en' => 'case-study-placeholder.jpg', 'bd' => 'case-study-placeholder.jpg']),
                    'status' => $caseStudyData['status'],
                    'author_id' => $caseStudyData['author_id'],
                    'published_at' => $caseStudyData['published_at'],
                    'meta_title' => json_encode($caseStudyData['meta_title'] ?? []),
                    'meta_description' => json_encode($caseStudyData['meta_description'] ?? []),
                    'meta_keywords' => json_encode($caseStudyData['meta_keywords'] ?? [])
                ]
            );
        }

        $this->command->info('Case studies seeded successfully!');
    }
}
