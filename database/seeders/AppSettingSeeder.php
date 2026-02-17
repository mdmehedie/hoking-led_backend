<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AppSetting::create([
            'primary_color' => '#3b82f6',
            'secondary_color' => '#10b981',
            'accent_color' => '#f59e0b',
            'font_family' => 'Arial',
            'base_font_size' => '16px',
        ]);
    }
}
