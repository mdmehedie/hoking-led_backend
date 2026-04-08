<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocale = Locale::defaultCode() ?? 'en';

        $projects = [
            [
                'slug' => 'enterprise-cloud-migration',
                'title' => 'Enterprise Cloud Migration',
                'secondary_title' => 'Modernizing Legacy Infrastructure',
                'excerpt' => 'Successfully migrated 200+ applications to cloud with zero downtime.',
                'description' => '<p>Our client, a Fortune 500 company, needed to migrate their entire on-premise infrastructure to a cloud-based solution. We delivered a seamless migration with zero downtime and 40% cost savings.</p>',
                'status' => 'published',
                'is_featured' => true,
                'is_popular' => true,
                'is_successful' => true,
                'sort_order' => 1,
                'meta_title' => 'Enterprise Cloud Migration Project',
                'meta_description' => 'Successfully migrated 200+ applications to cloud with zero downtime.',
            ],
            [
                'slug' => 'ai-analytics-platform',
                'title' => 'AI-Powered Analytics Platform',
                'secondary_title' => 'Real-Time Business Intelligence',
                'excerpt' => 'Built a custom AI analytics platform processing 10M+ events daily.',
                'description' => '<p>Developed a real-time analytics platform using machine learning to provide actionable business insights, reducing decision-making time by 70%.</p>',
                'status' => 'published',
                'is_featured' => false,
                'is_popular' => true,
                'is_successful' => true,
                'sort_order' => 2,
                'meta_title' => 'AI Analytics Platform Project',
                'meta_description' => 'Built a custom AI analytics platform processing 10M+ events daily.',
            ],
            [
                'slug' => 'mobile-ecommerce-platform',
                'title' => 'Mobile-First E-commerce Platform',
                'secondary_title' => 'Next-Gen Shopping Experience',
                'excerpt' => 'Launched a mobile-first platform increasing conversions by 55%.',
                'description' => '<p>Designed and built a progressive web application for e-commerce, resulting in 55% increase in mobile conversions and 30% improvement in user engagement.</p>',
                'status' => 'published',
                'is_featured' => true,
                'is_popular' => false,
                'is_successful' => true,
                'sort_order' => 3,
                'meta_title' => 'Mobile E-commerce Platform',
                'meta_description' => 'Launched a mobile-first platform increasing conversions by 55%.',
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::firstOrCreate(
                ['slug' => $projectData['slug']],
                $projectData
            );

            // Set translatable attributes
            foreach (['title', 'secondary_title', 'excerpt', 'description', 'meta_title', 'meta_description', 'meta_keywords', 'canonical_url'] as $field) {
                if (isset($projectData[$field])) {
                    $project->setTranslation($field, $defaultLocale, $projectData[$field]);
                }
            }
            $project->save();
        }

        $this->command->info('Projects seeded successfully!');
    }
}
