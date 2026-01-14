<?php

namespace App\Observers;

use App\Models\Video;
use App\Services\YouTubeService;
use Illuminate\Support\Facades\Storage;

class VideoObserver
{
    public function __construct(
        private YouTubeService $youtubeService
    ) {}

    /**
     * Handle the Video "creating" event.
     */
    public function creating(Video $video): void
    {
        $this->updateYouTubeInfo($video);
    }

    /**
     * Handle the Video "created" event.
     * File size is updated here because the file is fully uploaded after creation.
     */
    public function created(Video $video): void
    {
        $this->updateFileSizeAfterSave($video);
    }

    /**
     * Handle the Video "updating" event.
     */
    public function updating(Video $video): void
    {
        $this->updateYouTubeInfo($video);
        $this->cleanupOldFiles($video);
    }

    /**
     * Handle the Video "updated" event.
     * File size is updated here because the file is fully uploaded after update.
     */
    public function updated(Video $video): void
    {
        $this->updateFileSizeAfterSave($video);
    }

    /**
     * Handle the Video "deleted" event.
     */
    public function deleted(Video $video): void
    {
        $this->deleteFile($video->video_file);
        $this->deleteFile($video->thumbnail);
        $this->deleteFile($video->subtitle_file);
        $this->deleteFile($video->audiodescription_file);
    }

    /**
     * Met à jour automatiquement la taille du fichier vidéo après sauvegarde
     */
    private function updateFileSizeAfterSave(Video $video): void
    {
        if ($video->type === 'file' && $video->video_file) {
            $path = $video->video_file;

            try {
                if (Storage::disk('public')->exists($path)) {
                    $fileSize = Storage::disk('public')->size($path);
                    if ($video->file_size !== $fileSize) {
                        $video->updateQuietly(['file_size' => $fileSize]);
                    }
                }
            } catch (\League\Flysystem\UnableToRetrieveMetadata $e) {
                // File might still be in Livewire's temp directory during upload
            }
        }
    }

    /**
     * Récupère automatiquement les infos YouTube (durée)
     */
    private function updateYouTubeInfo(Video $video): void
    {
        if ($video->type !== 'youtube' || !$video->url) {
            return;
        }

        // Ne récupérer que si l'URL a changé ou si la durée n'est pas définie
        $original = $video->getOriginal();
        $urlChanged = !isset($original['url']) || $original['url'] !== $video->url;

        if (!$urlChanged && $video->duration) {
            return;
        }

        $info = $this->youtubeService->getVideoInfo($video->url);

        if ($info) {
            // Mettre à jour la durée si disponible
            if ($info['duration'] && !$video->duration) {
                $video->duration = $info['duration'];
            }
        }
    }

    /**
     * Supprime les anciens fichiers lors d'une mise à jour
     */
    private function cleanupOldFiles(Video $video): void
    {
        $original = $video->getOriginal();

        if (isset($original['video_file']) && $original['video_file'] !== $video->video_file) {
            $this->deleteFile($original['video_file']);
        }

        if (isset($original['thumbnail']) && $original['thumbnail'] !== $video->thumbnail) {
            $this->deleteFile($original['thumbnail']);
        }

        if (isset($original['subtitle_file']) && $original['subtitle_file'] !== $video->subtitle_file) {
            $this->deleteFile($original['subtitle_file']);
        }

        if (isset($original['audiodescription_file']) && $original['audiodescription_file'] !== $video->audiodescription_file) {
            $this->deleteFile($original['audiodescription_file']);
        }
    }

    /**
     * Supprime un fichier du storage
     */
    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
