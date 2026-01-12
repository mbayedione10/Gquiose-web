<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\YouTubeService;
use Illuminate\Console\Command;

class UpdateYouTubeVideoDurations extends Command
{
    protected $signature = 'videos:update-durations
                            {--force : Mettre à jour même si la durée existe déjà}
                            {--id= : Mettre à jour une vidéo spécifique par ID}';

    protected $description = 'Récupère et met à jour les durées des vidéos YouTube via l\'API YouTube';

    public function __construct(
        private YouTubeService $youtubeService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (!$this->youtubeService->isConfigured()) {
            $this->error('Clé API YouTube non configurée!');
            $this->line('Ajoutez YOUTUBE_API_KEY dans votre fichier .env');
            $this->line('Obtenez une clé sur: https://console.cloud.google.com/apis/credentials');
            return self::FAILURE;
        }

        $query = Video::where('type', 'youtube');

        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        }

        if (!$this->option('force')) {
            $query->whereNull('duration');
        }

        $videos = $query->get();

        if ($videos->isEmpty()) {
            $this->info('Aucune vidéo YouTube à mettre à jour.');
            return self::SUCCESS;
        }

        $this->info("Mise à jour de {$videos->count()} vidéo(s)...");
        $this->newLine();

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();

        $updated = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($videos as $video) {
            $info = $this->youtubeService->getVideoInfo($video->url);

            if ($info && $info['duration']) {
                $video->update(['duration' => $info['duration']]);
                $updated++;
            } elseif ($info && !$info['duration']) {
                $skipped++;
            } else {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Statut', 'Nombre'],
            [
                ['Mises à jour', $updated],
                ['URL invalide', $skipped],
                ['Erreurs', $failed],
            ]
        );

        if ($skipped > 0) {
            $this->warn('Certaines URLs YouTube sont invalides ou les vidéos n\'existent pas.');
        }

        return self::SUCCESS;
    }
}
