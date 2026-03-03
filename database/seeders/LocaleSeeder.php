<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        Locale::query()->updateOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'direction' => 'ltr',
                'is_default' => true,
                'is_active' => true,
            ]
        );

        Locale::query()->updateOrCreate(
            ['code' => 'bd'],
            [
                'name' => 'Bangla',
                'direction' => 'ltr',
                'is_default' => false,
                'is_active' => true,
            ]
        );
    }
}
