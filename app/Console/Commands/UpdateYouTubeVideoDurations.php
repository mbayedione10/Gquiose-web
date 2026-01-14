<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\YouTubeService;
use Illuminate\Console\Command;

class UpdateYouTubeVideoDurations extends Command
{
    protected $signature = 'videos:update-youtube-info
                            {--force : Mettre à jour même si les infos existent déjà}
                            {--id= : Mettre à jour une vidéo spécifique par ID}
                            {--duration-only : Ne mettre à jour que la durée}';

    protected $description = 'Récupère et met à jour les infos YouTube (titre, durée, miniature) via l\'API YouTube';

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
            $query->where(function ($q) {
                $q->whereNull('duration')
                  ->orWhereNull('youtube_thumbnail');
            });
        }

        $videos = $query->get();

        if ($videos->isEmpty()) {
            $this->info('Aucune vidéo YouTube à mettre à jour.');
            return self::SUCCESS;
        }

        $durationOnly = $this->option('duration-only');
        $this->info("Mise à jour de {$videos->count()} vidéo(s)...");
        $this->newLine();

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();

        $updated = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($videos as $video) {
            $info = $this->youtubeService->getVideoInfo($video->url);

            if ($info) {
                $updateData = [];

                // Mettre à jour la durée si disponible
                if ($info['duration'] && (!$video->duration || $this->option('force'))) {
                    $updateData['duration'] = $info['duration'];
                }

                if (!$durationOnly) {
                    // Mettre à jour le titre si disponible et vide
                    if ($info['title'] && (!$video->name || $this->option('force'))) {
                        $updateData['name'] = $info['title'];
                    }

                    // Mettre à jour la miniature YouTube si disponible
                    if ($info['thumbnail'] && (!$video->youtube_thumbnail || $this->option('force'))) {
                        $updateData['youtube_thumbnail'] = $info['thumbnail'];
                    }
                }

                if (!empty($updateData)) {
                    $video->update($updateData);
                    $updated++;
                } else {
                    $skipped++;
                }
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
                ['Déjà à jour', $skipped],
                ['Erreurs', $failed],
            ]
        );

        if ($failed > 0) {
            $this->warn('Certaines URLs YouTube sont invalides ou les vidéos n\'existent pas.');
        }

        return self::SUCCESS;
    }
}
