<?php

namespace App\Models;

use App\Observers\VideoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[ObservedBy([VideoObserver::class])]
class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'url',
        'type',
        'video_file',
        'thumbnail',
        'subtitle_file',
        'audiodescription_file',
        'duration',
        'resolution',
        'file_size',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Get the video URL (YouTube or file)
     */
    public function getVideoUrlAttribute()
    {
        if ($this->type === 'file' && $this->video_file) {
            return Storage::url($this->video_file);
        }

        return $this->url;
    }

    /**
     * Get the thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return Storage::url($this->thumbnail);
        }

        if ($this->type === 'youtube' && $this->url) {
            $videoId = $this->extractYoutubeId($this->url);
            if ($videoId) {
                return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
            }
        }

        return null;
    }

    /**
     * Get the subtitle file URL
     */
    public function getSubtitleUrlAttribute()
    {
        if ($this->subtitle_file) {
            return Storage::url($this->subtitle_file);
        }

        return null;
    }

    /**
     * Get the audiodescription file URL
     */
    public function getAudiodescriptionUrlAttribute()
    {
        if ($this->audiodescription_file) {
            return Storage::url($this->audiodescription_file);
        }

        return null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYoutubeId($url)
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        preg_match($pattern, $url, $matches);

        return $matches[1] ?? null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
