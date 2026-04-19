<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videos = [
            [
                'slug' => 'company-video',
                'video_url' => 'https://www.youtube.com/watch?v=example1',
            ],
            [
                'slug' => 'home-video',
                'video_url' => 'https://www.youtube.com/watch?v=example2',
            ],
        ];

        foreach ($videos as $videoData) {
            Video::updateOrCreate(
                ['slug' => $videoData['slug']],
                $videoData
            );
        }
    }
}
