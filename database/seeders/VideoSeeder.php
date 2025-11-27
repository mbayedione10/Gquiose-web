<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            [
                'name' => 'Comment bien laver ses mains',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ],
            [
                'name' => 'Allaitement maternel : les bons gestes',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ2',
            ],
            [
                'name' => 'Prévention du paludisme',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ3',
            ],
            [
                'name' => 'Les vaccins : pourquoi sont-ils importants ?',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ4',
            ],
            [
                'name' => 'Consultations prénatales : ce qu\'il faut savoir',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ5',
            ],
            [
                'name' => 'Traiter la diarrhée chez l\'enfant',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ6',
            ],
            [
                'name' => 'Planning familial : les différentes méthodes',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ7',
            ],
            [
                'name' => 'Gestes de premiers secours',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ8',
            ],
        ];

        foreach ($videos as $video) {
            Video::firstOrCreate(
                ['url' => $video['url']],
                $video
            );
        }

        $this->command->info('✅ ' . count($videos) . ' vidéos créées');
    }
}
