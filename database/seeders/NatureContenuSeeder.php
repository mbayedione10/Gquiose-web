<?php

namespace Database\Seeders;

use App\Models\NatureContenu;
use Illuminate\Database\Seeder;

class NatureContenuSeeder extends Seeder
{
    public function run(): void
    {
        $natures = [
            ['nom' => 'Messages texte', 'description' => 'Messages textuels'],
            ['nom' => 'Images/Photos', 'description' => 'Images et photos'],
            ['nom' => 'Vidéos', 'description' => 'Contenus vidéo'],
            ['nom' => 'Captures d\'écran', 'description' => 'Captures d\'écran'],
            ['nom' => 'Enregistrements audio', 'description' => 'Fichiers audio'],
            ['nom' => 'Liens URL', 'description' => 'Liens internet'],
            ['nom' => 'Commentaires publics', 'description' => 'Commentaires publics sur les réseaux'],
            ['nom' => 'Messages privés', 'description' => 'Messages privés/DM'],
            ['nom' => 'Publications/Posts', 'description' => 'Publications sur les réseaux sociaux'],
            ['nom' => 'Stories', 'description' => 'Stories (Instagram, Facebook...)'],
            ['nom' => 'Autre', 'description' => 'Autre type de contenu'],
        ];

        foreach ($natures as $nature) {
            NatureContenu::create($nature);
        }
    }
}
