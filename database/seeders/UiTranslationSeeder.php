<?php

namespace Database\Seeders;

use App\Models\UiTranslation;
use Illuminate\Database\Seeder;

class UiTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            // English translations
            [
                'key' => 'Admin Panel',
                'locale' => 'en',
                'value' => 'Admin Panel',
            ],
            [
                'key' => 'Content Management',
                'locale' => 'en',
                'value' => 'Content Management',
            ],
            [
                'key' => 'Product Management',
                'locale' => 'en',
                'value' => 'Product Management',
            ],
            [
                'key' => 'Marketing',
                'locale' => 'en',
                'value' => 'Marketing',
            ],
            [
                'key' => 'Settings',
                'locale' => 'en',
                'value' => 'Settings',
            ],
            [
                'key' => 'User Management',
                'locale' => 'en',
                'value' => 'User Management',
            ],
            [
                'key' => 'Language',
                'locale' => 'en',
                'value' => 'Language',
            ],

            // Bangla translations
            [
                'key' => 'Admin Panel',
                'locale' => 'bd',
                'value' => 'অ্যাডমিন প্যানেল',
            ],
            [
                'key' => 'Content Management',
                'locale' => 'bd',
                'value' => 'কন্টেন্ট ব্যবস্থাপনা',
            ],
            [
                'key' => 'Product Management',
                'locale' => 'bd',
                'value' => 'পণ্য ব্যবস্থাপনা',
            ],
            [
                'key' => 'Marketing',
                'locale' => 'bd',
                'value' => 'মার্কেটিং',
            ],
            [
                'key' => 'Settings',
                'locale' => 'bd',
                'value' => 'সেটিংস',
            ],
            [
                'key' => 'User Management',
                'locale' => 'bd',
                'value' => 'ব্যবহারকারী ব্যবস্থাপনা',
            ],
            [
                'key' => 'Language',
                'locale' => 'bd',
                'value' => 'ভাষা',
            ],
        ];

        foreach ($translations as $translation) {
            UiTranslation::query()->updateOrCreate(
                [
                    'key' => $translation['key'],
                    'locale' => $translation['locale'],
                ],
                [
                    'value' => $translation['value'],
                ]
            );
        }
    }
}
