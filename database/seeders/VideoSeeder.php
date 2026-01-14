<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            // Vidéos SSR pour jeunes
            [
                'name' => 'La puberté expliquée aux ados : ce qui change dans ton corps',
                'url' => 'https://www.youtube.com/watch?v=SSR_Puberte_2024',
            ],
            [
                'name' => 'Tout sur les règles : ce que tu dois savoir sans tabou',
                'url' => 'https://www.youtube.com/watch?v=SSR_Regles_2024',
            ],
            [
                'name' => 'Comment bien utiliser un préservatif : démonstration étape par étape',
                'url' => 'https://www.youtube.com/watch?v=SSR_Preservatif_2024',
            ],
            [
                'name' => 'Les méthodes de contraception pour jeunes : pilule, implant, DIU expliqués',
                'url' => 'https://www.youtube.com/watch?v=SSR_Contraception_2024',
            ],
            [
                'name' => 'IST et VIH : comment te protéger et où te faire dépister',
                'url' => 'https://www.youtube.com/watch?v=SSR_IST_VIH_2024',
            ],

            // Vidéos VBG pour jeunes
            [
                'name' => 'C\'est quoi le consentement ? Apprends à dire OUI ou NON',
                'url' => 'https://www.youtube.com/watch?v=VBG_Consentement_2024',
            ],
            [
                'name' => 'Reconnaître les signes de violence dans ton couple',
                'url' => 'https://www.youtube.com/watch?v=VBG_Signes_Violence_2024',
            ],
            [
                'name' => 'Harcèlement sexuel : identifie-le et défends-toi',
                'url' => 'https://www.youtube.com/watch?v=VBG_Harcelement_2024',
            ],
            [
                'name' => 'Tu es victime de violence ? Voici où trouver de l\'aide',
                'url' => 'https://www.youtube.com/watch?v=VBG_Aide_Victimes_2024',
            ],
            [
                'name' => 'Cyberharcèlement et chantage : comment te protéger en ligne',
                'url' => 'https://www.youtube.com/watch?v=VBG_Cyber_2024',
            ],
            [
                'name' => 'Mariage forcé : tes droits et comment refuser',
                'url' => 'https://www.youtube.com/watch?v=VBG_Mariage_Force_2024',
            ],
            [
                'name' => 'Tes droits sexuels et reproductifs expliqués simplement',
                'url' => 'https://www.youtube.com/watch?v=SSR_Droits_2024',
            ],
            [
                'name' => 'Témoignage : J\'ai dit NON au mariage précoce et j\'ai continué mes études',
                'url' => 'https://www.youtube.com/watch?v=VBG_Temoignage_2024',
            ],
            [
                'name' => 'Pression des pairs et première fois : ne te laisse pas influencer',
                'url' => 'https://www.youtube.com/watch?v=SSR_Pression_Pairs_2024',
            ],
            [
                'name' => 'Utiliser l\'app GquiOse : suivre ton cycle et trouver de l\'aide',
                'url' => 'https://www.youtube.com/watch?v=GquiOse_Tuto_2024',
            ],
        ];

        foreach ($videos as $video) {
            Video::firstOrCreate(
                ['url' => $video['url']],
                $video
            );
        }

        $this->command->info('✅ '.count($videos).' vidéos SSR/VBG pour jeunes créées');
    }
}
