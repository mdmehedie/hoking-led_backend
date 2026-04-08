<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\Locale;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocale = Locale::defaultCode() ?? 'en';
        $author = User::first();

        $caseStudies = [
            [
                'slug' => 'digital-transformation-retail-chain',
                'title' => 'Digital Transformation for Global Retail Chain',
                'excerpt' => 'How we helped a major retail chain achieve 40% efficiency improvement through digital transformation.',
                'project_description_data' => [
                    [
                        'title' => 'Challenge',
                        'description' => 'The client needed to modernize their legacy systems across 500+ store locations while maintaining uninterrupted operations.',
                        'image' => null,
                    ],
                    [
                        'title' => 'Solution',
                        'description' => 'Implemented a cloud-based microservices architecture with real-time data synchronization.',
                        'image' => null,
                    ],
                ],
                'status' => 'published',
                'author_id' => $author?->id ?? 1,
                'published_at' => now(),
                'meta_title' => 'Digital Transformation Case Study - Retail Chain',
                'meta_description' => 'How we helped a major retail chain achieve 40% efficiency improvement through digital transformation.',
            ],
            [
                'slug' => 'smart-manufacturing-iot',
                'title' => 'Smart Manufacturing IoT Implementation',
                'excerpt' => 'IoT-enabled smart factory solution that reduced downtime by 60% and increased production efficiency.',
                'project_description_data' => [
                    [
                        'title' => 'Challenge',
                        'description' => 'Manufacturing plant experiencing frequent equipment failures and unplanned downtime.',
                        'image' => null,
                    ],
                    [
                        'title' => 'Solution',
                        'description' => 'Deployed IoT sensors with predictive analytics dashboard for real-time equipment monitoring.',
                        'image' => null,
                    ],
                ],
                'status' => 'published',
                'author_id' => $author?->id ?? 1,
                'published_at' => now(),
                'meta_title' => 'Smart Manufacturing IoT Case Study',
                'meta_description' => 'IoT-enabled smart factory solution that reduced downtime by 60%.',
            ],
        ];

        foreach ($caseStudies as $caseStudyData) {
            $insertData = $caseStudyData;
            $projectDesc = $insertData['project_description_data'] ?? null;
            unset($insertData['project_description_data']);

            $caseStudy = CaseStudy::firstOrCreate(
                ['slug' => $insertData['slug']],
                $insertData
            );

            // Set translatable attributes
            foreach (['title', 'excerpt'] as $field) {
                if (isset($caseStudyData[$field])) {
                    $caseStudy->setTranslation($field, $defaultLocale, $caseStudyData[$field]);
                }
            }
            if ($projectDesc) {
                $caseStudy->setTranslation('project_description', $defaultLocale, $projectDesc);
            }
            $caseStudy->save();
        }

        $this->command->info('Case studies seeded successfully!');
    }
}
