<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Video;
use Illuminate\Http\Request;

class APIVideoController extends Controller
{
    public function videos(Request $request)
    {
        $query = Video::active()->orderBy('id', 'desc');

        // Filtrer par type si spécifié
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $videos = $query->get()->map(function ($video) {
            return $this->formatVideo($video);
        });

        return ApiResponse::success(['videos' => $videos]);
    }

    public function show($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return ApiResponse::error('Vidéo non trouvée', 404);
        }

        return ApiResponse::success(['video' => $this->formatVideo($video)]);
    }

    /**
     * Format video data for API response
     */
    private function formatVideo(Video $video): array
    {
        return [
            'id' => $video->id,
            'name' => $video->name,
            'description' => $video->description,
            'type' => $video->type,
            'video_url' => $video->video_url,
            'thumbnail_url' => $video->thumbnail_url,
            'duration' => $video->duration,
            'resolution' => $video->resolution,
            'file_size' => $video->file_size,
            'file_size_formatted' => $video->formatted_file_size,
            'subtitle_url' => $video->subtitle_url,
            'audiodescription_url' => $video->audiodescription_url,
            'has_subtitles' => !empty($video->subtitle_file),
            'has_audiodescription' => !empty($video->audiodescription_file),
            'created_at' => $video->created_at?->toIso8601String(),
        ];
    }
}
