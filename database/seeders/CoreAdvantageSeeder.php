<?php

namespace Database\Seeders;

use App\Models\CoreAdvantage;
use App\Models\Locale;
use Illuminate\Database\Seeder;

class CoreAdvantageSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocale = Locale::defaultCode() ?? 'en';

        $advantages = [
            [
                'title' => 'Innovation',
                'description' => 'We push boundaries to deliver cutting-edge solutions.',
                'icon' => 'heroicon-o-light-bulb',
                'sort_order' => 1,
            ],
            [
                'title' => 'Quality',
                'description' => 'Uncompromising quality in every product and service.',
                'icon' => 'heroicon-o-star',
                'sort_order' => 2,
            ],
            [
                'title' => 'Reliability',
                'description' => 'Dependable solutions you can trust.',
                'icon' => 'heroicon-o-shield-check',
                'sort_order' => 3,
            ],
            [
                'title' => 'Expertise',
                'description' => 'Deep industry knowledge and technical excellence.',
                'icon' => 'heroicon-o-academic-cap',
                'sort_order' => 4,
            ],
            [
                'title' => 'Support',
                'description' => '24/7 dedicated support for your success.',
                'icon' => 'heroicon-o-chat-bubble-left-right',
                'sort_order' => 5,
            ],
        ];

        foreach ($advantages as $advantageData) {
            $advantage = CoreAdvantage::firstOrCreate(
                ['sort_order' => $advantageData['sort_order']],
                $advantageData
            );

            // Set translatable attributes
            foreach (['title', 'description'] as $field) {
                if (isset($advantageData[$field])) {
                    $advantage->setTranslation($field, $defaultLocale, $advantageData[$field]);
                }
            }
            $advantage->save();
        }

        $this->command->info('Core advantages seeded successfully!');
    }
}
