<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.api_key');
    }

    /**
     * Extraire l'ID de la vidéo depuis une URL YouTube
     */
    public function extractVideoId(string $url): ?string
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        preg_match($pattern, $url, $matches);

        return $matches[1] ?? null;
    }

    /**
     * Récupérer les informations d'une vidéo YouTube
     */
    public function getVideoInfo(string $url): ?array
    {
        $videoId = $this->extractVideoId($url);

        if (!$videoId) {
            return null;
        }

        // Si pas de clé API, retourner les infos de base
        if (!$this->apiKey) {
            return [
                'video_id' => $videoId,
                'thumbnail' => "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
                'duration' => null,
                'title' => null,
            ];
        }

        try {
            $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'snippet,contentDetails',
                'id' => $videoId,
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['items'])) {
                    $item = $data['items'][0];

                    return [
                        'video_id' => $videoId,
                        'title' => $item['snippet']['title'] ?? null,
                        'description' => $item['snippet']['description'] ?? null,
                        'thumbnail' => $item['snippet']['thumbnails']['high']['url'] ?? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
                        'duration' => $this->parseDuration($item['contentDetails']['duration'] ?? null),
                        'duration_iso' => $item['contentDetails']['duration'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("YouTube API error: " . $e->getMessage());
        }

        return [
            'video_id' => $videoId,
            'thumbnail' => "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
            'duration' => null,
            'title' => null,
        ];
    }

    /**
     * Convertir la durée ISO 8601 en format lisible (MM:SS ou HH:MM:SS)
     */
    public function parseDuration(?string $isoDuration): ?string
    {
        if (!$isoDuration) {
            return null;
        }

        try {
            $interval = new \DateInterval($isoDuration);

            $hours = $interval->h;
            $minutes = $interval->i;
            $seconds = $interval->s;

            if ($hours > 0) {
                return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
            }

            return sprintf('%d:%02d', $minutes, $seconds);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Vérifier si l'API YouTube est configurée
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
