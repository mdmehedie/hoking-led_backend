<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@example.com')->first();
        if (!$author) {
            $author = User::first();
        }

        $newsArticles = [
            [
                'title' => [
                    'en' => 'Tech Giant Announces Revolutionary AI Breakthrough',
                    'bd' => 'প্রযুক্তি জায়ান্ট ঘোষণা করল বিপ্লবী এআই ব্রেকথ্রু'
                ],
                'slug' => 'tech-giant-announces-revolutionary-ai-breakthrough',
                'excerpt' => [
                    'en' => 'Major technology company unveils groundbreaking artificial intelligence system that promises to transform multiple industries.',
                    'bd' => 'প্রধান প্রযুক্তি কোম্পানি একটি যুগান্তকারী কৃত্রিম বুদ্ধিমত্তা সিস্টেম উন্মোচন করেছে যা একাধিক শিল্পকে রূপান্তরিত করার প্রতিশ্রুতি দেয়।'
                ],
                'content' => [
                    'en' => '<h2>Historic Announcement</h2><p>In a landmark press conference today, Tech Giant CEO Sarah Chen unveiled what the company is calling "the most significant advancement in artificial intelligence this decade." The new AI system, dubbed "QuantumMind," demonstrates capabilities that were previously thought to be years away.</p><h3>Key Capabilities</h3><p>QuantumMind reportedly achieves human-level performance across multiple domains including natural language understanding, visual recognition, and complex problem-solving. Early tests show the system outperforming human experts in medical diagnosis, financial analysis, and scientific research.</p><h3>Industry Impact</h3><p>The announcement sent shockwaves through the technology sector, with major stocks reacting immediately. Industry experts predict this breakthrough could accelerate AI adoption across healthcare, finance, manufacturing, and education sectors by 3-5 years.</p><h3>What\'s Next</h3><p>Tech Giant plans to roll out the technology through partnerships with major corporations and research institutions. The company has pledged to make the technology available for academic research while maintaining commercial applications.</p><h2>Expert Reactions</h2><p>"This is a game-changer," said Dr. Michael Roberts, AI researcher at MIT. "We\'ve been talking about AGI (Artificial General Intelligence) for years, and this might be the closest we\'ve come to seeing it in practice."</p>',
                    'bd' => '<h2>ঐতিহাসিক ঘোষণা</h2><p>আজ একটি যুগান্তকারী সংবাদ সম্মেলনে, টেক জায়ান্টের সিইও সারা চেন যা কোম্পানিটিকে "এই দশকের সবচেয়ে উল্লেখযোগ্য অগ্রগতি কৃত্রিম বুদ্ধিমত্তায়" বলা হচ্ছে তা উন্মোচন করেছে। নতুন এআই সিস্টেম, যার নাম "কোয়ান্টামমাইন্ড," ক্ষমতা প্রদর্শন করে যা আগে বছরও দূরে বলে মনে করা হত।</p><h3>মূল ক্ষমতা</h3><p>কোয়ান্টামমাইন্ড সম্ভবত প্রাকৃতিক ভাষা বোঝার, ভিজ্যুয়াল স্বীকৃতি এবং জটিল সমস্যা সমাধান সহ একাধিক ডোমেইনে মানব-স্তরের পারফরম্যান্স অর্জন করে। প্রাথমিক পরীক্ষায় দেখা গেছে সিস্টেমটি মেডিকেল ডায়াগনোসিস, আর্থিক বিশ্লেষণ এবং বৈজ্ঞানিক গবেষণায় মানব বিশেষজ্ঞদের ছাড়িয়ে গেছে।</p><h3>শিল্প প্রভাব</h3><p>ঘোষণাটি প্রযুক্তি খাতে ঝড় তুলেছে, প্রধান স্টকগুলি তাৎক্ষণিকভাবে প্রতিক্রিয়া দেখিয়েছে। শিল্প বিশেষজ্ঞরা ভবিষ্যদ্বাণী করছেন যে এই ব্রেকথ্রু স্বাস্থ্যসেবা, অর্থ, উত্পাদন এবং শিক্ষা খাতে এআই গ্রহণকে ৩-৫ বছর এগিয়ে নিতে পারে।</p><h3>পরবর্তী পদক্ষেপ</h3><p>টেক জায়ান্ট প্রধান কর্পোরেশন এবং গবেষণা প্রতিষ্ঠানগুলির সাথে অংশীদারিত্বের মাধ্যমে প্রযুক্তি চালু করার পরিকল্পনা করছে। কোম্পানিটি বাণিজ্যিক অ্যাপ্লিকেশন বজায় রাখার সময় একাডেমিক গবেষণার জন্য প্রযুক্তি উপলব্ধ করার প্রতিশ্রুতি দিয়েছে।</p><h2>বিশেষজ্ঞ প্রতিক্রিয়া</h2><p>"এটি একটি গেম-চেঞ্জার," বলেছেন ড. মাইকেল রবার্টস, এমআইটি-এর এআই গবেষক। "আমরা বছরের পর বছর ধরে AGI (আর্টিফিসিয়াল জেনারেল ইন্টেলিজেন্স) নিয়ে কথা বলছি, এবং এটি হতে পারে অনুশীলনে এটি দেখার সবচেয়ে কাছাকাছি।"</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subHours(6),
                'meta_title' => [
                    'en' => 'Tech Giant Announces Revolutionary AI Breakthrough | QuantumMind AI',
                    'bd' => 'প্রযুক্তি জায়ান্ট ঘোষণা করল বিপ্লবী এআই ব্রেকথ্রু | কোয়ান্টামমাইন্ড এআই'
                ],
                'meta_description' => [
                    'en' => 'Major tech company unveils QuantumMind AI system with human-level capabilities across multiple domains, promising to transform healthcare, finance, and more.',
                    'bd' => 'প্রধান টেক কোম্পানি একাধিক ডোমেইনে মানব-স্তরের ক্ষমতা সহ কোয়ান্টামমাইন্ড এআই সিস্টেম উন্মোচন করেছে, স্বাস্থ্যসেবা, অর্থ এবং আরও অনেক কিছুকে রূপান্তরিত করার প্রতিশ্রুতি দিচ্ছে।'
                ],
                'meta_keywords' => [
                    'en' => 'AI breakthrough, QuantumMind, artificial intelligence, tech news, machine learning',
                    'bd' => 'এআই ব্রেকথ্রু, কোয়ান্টামমাইন্ড, কৃত্রিম বুদ্ধিমত্তা, টেক নিউজ, মেশিন লার্নিং'
                ]
            ],
            [
                'title' => [
                    'en' => 'Global Climate Summit Reaches Historic Agreement',
                    'bd' => 'গ্লোবাল জলবায়ু সম্মেলন ঐতিহাসিক চুক্তিতে পৌঁছেছে'
                ],
                'slug' => 'global-climate-summit-historic-agreement',
                'excerpt' => [
                    'en' => 'World leaders commit to unprecedented carbon reduction targets in landmark climate accord.',
                    'bd' => 'বিশ্ব নেতারা যুগান্তকারী জলবায়ু চুক্তিতে অভূতপূর্ব কার্বন হ্রাস লক্ষ্যমাত্রায় প্রতিশ্রুতিবদ্ধ।'
                ],
                'content' => [
                    'en' => '<h2>Landmark Achievement</h2><p>After two weeks of intense negotiations, world leaders at the Global Climate Summit have reached what experts are calling "the most ambitious climate agreement in history." The accord commits signatory nations to achieve net-zero emissions by 2040, a full decade ahead of previous targets.</p><h3>Key Provisions</h3><p>The agreement includes binding commitments to phase out coal power by 2030, transition 50% of vehicle fleets to electric by 2035, and protect 30% of global land and ocean areas by 2030. Developed nations have pledged $500 billion annually to support developing countries in their transition to green energy.</p><h3>Economic Implications</h3><p>Financial markets responded positively to the announcement, with renewable energy stocks surging and fossil fuel companies facing increased scrutiny. Economists predict the agreement could create millions of new jobs in the green technology sector while reshaping global trade patterns.</p><h3>Challenges Ahead</h3><p>Despite the optimism, implementation remains a significant challenge. Critics point out that meeting these targets will require unprecedented international cooperation and massive investment in infrastructure and technology.</p><h2>Next Steps</h2><p>Follow-up meetings are scheduled for next quarter to establish detailed implementation plans and monitoring mechanisms. The agreement will be formally signed at the United Nations next month.</p>',
                    'bd' => '<h2>যুগান্তকারী অর্জন</h2><p>দুই সপ্তাহের তীব্র আলোচনার পর, গ্লোবাল জলবায়ু সম্মেলনে বিশ্ব নেতারা যা বিশেষজ্ঞরা "ইতিহাসের সবচেয়ে উচ্চাভিলাষী জলবায়ু চুক্তি" বলছেন তাতে পৌঁছেছেন। চুক্তিটি স্বাক্ষরকারী দেশগুলিকে ২০৪০ সালের মধ্যে নেট-জিরো নির্গমন অর্জন করতে প্রতিশ্রুতিবদ্ধ করে, যা পূর্ববর্তী লক্ষ্যমাত্রার চেয়ে এক দশক আগে।</p><h3>মূল বিধান</h3><p>চুক্তিটিতে ২০৩০ সালের মধ্যে কয়লা বিদ্যুৎ পর্যায়ক্রমে বন্ধ করা, ২০৩৫ সালের মধ্যে যানবাহন বহরের ৫০% বৈদ্যুতিকে রূপান্তরিত করা এবং ২০৩০ সালের মধ্যে বিশ্বব্যাপী ভূমি এবং মহাসাগর অঞ্চলের ৩০% রক্ষা করার বাধ্যতামূলক প্রতিশ্রুতি অন্তর্ভুক্ত রয়েছে। উন্নত দেশগুলি সবুজ শক্তিতে রূপান্তরিত হওয়ার জন্য উন্নয়নশীল দেশগুলিকে সমর্থন করার জন্য বার্ষিক $৫০০ বিলিয়ন প্রতিশ্রুতি দিয়েছে।</p><h3>অর্থনৈতিক প্রভাব</h3><p>আর্থিক বাজারগুলি ঘোষণার প্রতি ইতিবাচকভাবে সাড়া দিয়েছে, নবায়নযোগ্য শক্তি স্টক বেড়ে গেছে এবং জীবাশ্ম জ্বালানি কোম্পানিগুলি বর্ধিত পর্যালোচনার মুখোমুখি হচ্ছে। অর্থনীতিবিদরা ভবিষ্যদ্বাণী করছেন যে চুক্তিটি গ্রিন টেকনোলজি খাতে লক্ষ লক্ষ নতুন চাকরি তৈরি করতে পারে এবং বিশ্বব্যাপী বাণিজ্যের প্যাটার্ন পুনর্গঠন করতে পারে।</p><h3>সামনের চ্যালেঞ্জ</h3><p>আশাবাদ সত্ত্বেও, বাস্তবায়ন একটি উল্লেখযোগ্য চ্যালেঞ্জ রয়ে গেছে। সমালোচকরা উল্লেখ করেছেন যে এই লক্ষ্যমাত্রাগুলি পূরণ করতে অভূতপূর্ব আন্তর্জাতিক সহযোগিতা এবং অবকাঠামো এবং প্রযুক্তিতে বিশাল বিনিয়োগের প্রয়োজন হবে।</p><h2>পরবর্তী পদক্ষেপ</h2><p>বিস্তারিত বাস্তবায়ন পরিকল্পনা এবং পর্যবেক্ষণ প্রক্রিয়া প্রতিষ্ঠার জন্য পরবর্তী ত্রৈমাসিকে অনুসরণ সভা নির্ধারিত হয়েছে। চুক্তিটি আগামী মাসে জাতিসংঘে আনুষ্ঠানিকভাবে স্বাক্ষরিত হবে।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subHours(12),
                'meta_title' => [
                    'en' => 'Global Climate Summit Reaches Historic Net-Zero Agreement by 2040',
                    'bd' => 'গ্লোবাল জলবায়ু সম্মেলন ২০৪০ সালের মধ্যে ঐতিহাসিক নেট-জিরো চুক্তিতে পৌঁছেছে'
                ],
                'meta_description' => [
                    'en' => 'World leaders commit to net-zero emissions by 2040 in historic climate agreement with $500B annual support for developing nations.',
                    'bd' => 'বিশ্ব নেতারা উন্নয়নশীল দেশগুলির জন্য বার্ষিক $৫০০বি সমর্থন সহ ঐতিহাসিক জলবায়ু চুক্তিতে ২০৪০ সালের মধ্যে নেট-জিরো নির্গমনের প্রতিশ্রুতি দিয়েছেন।'
                ],
                'meta_keywords' => [
                    'en' => 'climate summit, net-zero emissions, climate agreement, global warming, environmental policy',
                    'bd' => 'জলবায়ু সম্মেলন, নেট-জিরো নির্গমন, জলবায়ু চুক্তি, গ্লোবাল ওয়ার্মিং, পরিবেশ নীতি'
                ]
            ],
            [
                'title' => [
                    'en' => 'Breakthrough in Quantum Computing Promises Medical Revolution',
                    'bd' => 'কোয়ান্টাম কম্পিউটিং-এ ব্রেকথ্রু মেডিকেল বিপ্লবের প্রতিশ্রুতি দেয়'
                ],
                'slug' => 'quantum-computing-medical-revolution-breakthrough',
                'excerpt' => [
                    'en' => 'Researchers achieve quantum supremacy in drug discovery, potentially reducing development time from years to weeks.',
                    'bd' => 'গবেষকরা ওষুধ আবিষ্কারে কোয়ান্টাম শ্রেষ্ঠত্ব অর্জন করেছেন, সম্ভাব্যভাবে উন্নয়ন সময় বছর থেকে সপ্তাহে কমিয়ে আনছে।'
                ],
                'content' => [
                    'en' => '<h2>Scientific Breakthrough</h2><p>Researchers at Quantum Research Institute have announced a major breakthrough in quantum computing that could revolutionize drug discovery and medical research. The team successfully demonstrated quantum supremacy in molecular simulation, achieving in hours what would take traditional computers years to complete.</p><h3>Drug Discovery Impact</h3><p>The breakthrough allows scientists to simulate complex molecular interactions with unprecedented accuracy. This could dramatically accelerate the development of new medications, potentially reducing drug discovery timelines from 10-15 years to just weeks or months.</p><h3>Personalized Medicine</h3><p>Quantum computing enables the analysis of individual genetic profiles and their interaction with potential treatments. This opens the door to truly personalized medicine, where treatments can be tailored to each patient\'s unique genetic makeup.</p><h3>Challenges and Limitations</h3><p>Despite the excitement, researchers caution that practical applications are still years away. Current quantum computers require extreme conditions and are prone to errors. However, this breakthrough demonstrates that the fundamental challenges can be overcome.</p><h2>Future Outlook</h3><p>Major pharmaceutical companies have already begun investing in quantum computing research. The technology could also impact other fields like materials science, cryptography, and financial modeling.</p>',
                    'bd' => '<h2>বৈজ্ঞানিক ব্রেকথ্রু</h2><p>কোয়ান্টাম রিসার্চ ইনস্টিটিউটের গবেষকরা কোয়ান্টাম কম্পিউটিং-এ একটি প্রধান ব্রেকথ্রু ঘোষণা করেছেন যা ওষুধ আবিষ্কার এবং মেডিকেল গবেষণায় বিপ্লব ঘটাতে পারে। দলটি সফলভাবে আণবিক সিমুলেশনে কোয়ান্টাম শ্রেষ্ঠত্ব প্রদর্শন করেছে, যা ঐতিহ্যগত কম্পিউটারগুলিকে বছর লাগবে এমন কাজ ঘন্টায় সম্পন্ন করেছে।</p><h3>ওষুধ আবিষ্কার প্রভাব</h3><p>ব্রেকথ্রুটি বিজ্ঞানীদের অভূতপূর্ব নির্ভুলতার সাথে জটিল আণবিক মিথস্ক্রিয়া সিমুলেট করতে দেয়। এটি নতুন ওষুধের উন্নয়নকে নাটকীয়ভাবে ত্বরান্বিত করতে পারে, সম্ভাব্যভাবে ওষুধ আবিষ্কারের সময়কাল ১০-১৫ বছর থেকে মাত্র কয়েক সপ্তাহ বা মাসে কমিয়ে আনতে পারে।</p><h3>ব্যক্তিগতকৃত ওষুধ</h3><p>কোয়ান্টাম কম্পিউটিং ব্যক্তিগত জেনেটিক প্রোফাইল এবং সম্ভাব্য চিকিৎসার সাথে তাদের মিথস্ক্রিয়া বিশ্লেষণ সক্ষম করে। এটি সত্যিকারের ব্যক্তিগতকৃত ওষুধের দরজা খুলে দেয়, যেখানে চিকিৎসাগুলি প্রতিটি রোগীর অনন্য জেনেটিক গঠন অনুযায়ী তৈরি করা যেতে পারে।</p><h3>চ্যালেঞ্জ এবং সীমাবদ্ধতা</h3><p>উত্তেজনা সত্ত্বেও, গবেষকরা সতর্ক করে যে ব্যবহারিক অ্যাপ্লিকেশনগুলি এখনও বছর দূরে। বর্তমান কোয়ান্টাম কম্পিউটারগুলির চরম অবস্থার প্রয়োজন এবং তারা ত্রুটি-প্রবণ। যাইহোক, এই ব্রেকথ্রু প্রদর্শন করে যে মৌলিক চ্যালেঞ্জগুলি অতিক্রম করা যেতে পারে।</p><h2>ভবিষ্যৎ পূর্বাভাস</h2><p>প্রধান ফার্মাসিউটিক্যাল কোম্পানিগুলি ইতিমধ্যেই কোয়ান্টাম কম্পিউটিং গবেষণায় বিনিয়োগ শুরু করেছে। প্রযুক্তিটি উপকরণ বিজ্ঞান, ক্রিপ্টোগ্রাফি এবং আর্থিক মডেলিং-এর মতো অন্যান্য ক্ষেত্রেও প্রভাব ফেলতে পারে।</p>'
                ],
                'status' => 'published',
                'author_id' => $author->id,
                'published_at' => now()->subHours(18),
                'meta_title' => [
                    'en' => 'Quantum Computing Breakthrough in Drug Discovery | Medical Revolution',
                    'bd' => 'ওষুধ আবিষ্কারে কোয়ান্টাম কম্পিউটিং ব্রেকথ্রু | মেডিকেল বিপ্লব'
                ],
                'meta_description' => [
                    'en' => 'Quantum computing breakthrough achieves molecular simulation in hours, potentially revolutionizing drug discovery and personalized medicine.',
                    'bd' => 'কোয়ান্টাম কম্পিউটিং ব্রেকথ্রু ঘন্টায় আণবিক সিমুলেশন অর্জন করে, সম্ভাব্যভাবে ওষুধ আবিষ্কার এবং ব্যক্তিগতকৃত ওষুধে বিপ্লব ঘটাচ্ছে।'
                ],
                'meta_keywords' => [
                    'en' => 'quantum computing, drug discovery, medical research, molecular simulation, personalized medicine',
                    'bd' => 'কোয়ান্টাম কম্পিউটিং, ওষুধ আবিষ্কার, মেডিকেল গবেষণা, আণবিক সিমুলেশন, ব্যক্তিগতকৃত ওষুধ'
                ]
            ]
        ];

        foreach ($newsArticles as $newsData) {
            $news = News::create([
                'title' => $newsData['title']['en'], // Default to English for database
                'slug' => $newsData['slug'],
                'excerpt' => $newsData['excerpt']['en'], // Default to English
                'content' => $newsData['content']['en'], // Default to English
                'status' => $newsData['status'],
                'author_id' => $newsData['author_id'],
                'published_at' => $newsData['published_at'],
            ]);
            
            // Set translatable attributes
            foreach (['title', 'excerpt', 'content'] as $field) {
                if (isset($newsData[$field]) && is_array($newsData[$field])) {
                    foreach ($newsData[$field] as $locale => $value) {
                        $news->setTranslation($field, $locale, $value);
                    }
                }
            }
            
            // Set meta fields as plain strings (use English version)
            $news->update([
                'meta_title' => $newsData['meta_title']['en'] ?? '',
                'meta_description' => $newsData['meta_description']['en'] ?? '',
                'meta_keywords' => $newsData['meta_keywords']['en'] ?? '',
            ]);
        }

        $this->command->info('News articles seeded successfully!');
    }
}
