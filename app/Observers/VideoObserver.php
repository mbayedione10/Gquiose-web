<?php

namespace App\Observers;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoObserver
{
    /**
     * Handle the Video "creating" event.
     */
    public function creating(Video $video): void
    {
        $this->updateFileSize($video);
    }

    /**
     * Handle the Video "updating" event.
     */
    public function updating(Video $video): void
    {
        $this->updateFileSize($video);
        $this->cleanupOldFiles($video);
    }

    /**
     * Handle the Video "deleted" event.
     */
    public function deleted(Video $video): void
    {
        // Supprimer tous les fichiers associés
        $this->deleteFile($video->video_file);
        $this->deleteFile($video->thumbnail);
        $this->deleteFile($video->subtitle_file);
        $this->deleteFile($video->audiodescription_file);
    }

    /**
     * Met à jour automatiquement la taille du fichier vidéo
     */
    private function updateFileSize(Video $video): void
    {
        if ($video->type === 'file' && $video->video_file) {
            $path = $video->video_file;

            if (Storage::disk('public')->exists($path)) {
                $video->file_size = Storage::disk('public')->size($path);
            }
        }
    }

    /**
     * Supprime les anciens fichiers lors d'une mise à jour
     */
    private function cleanupOldFiles(Video $video): void
    {
        $original = $video->getOriginal();

        // Supprimer l'ancien fichier vidéo si changé
        if (isset($original['video_file']) && $original['video_file'] !== $video->video_file) {
            $this->deleteFile($original['video_file']);
        }

        // Supprimer l'ancienne miniature si changée
        if (isset($original['thumbnail']) && $original['thumbnail'] !== $video->thumbnail) {
            $this->deleteFile($original['thumbnail']);
        }

        // Supprimer l'ancien fichier sous-titres si changé
        if (isset($original['subtitle_file']) && $original['subtitle_file'] !== $video->subtitle_file) {
            $this->deleteFile($original['subtitle_file']);
        }

        // Supprimer l'ancien fichier audiodescription si changé
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
