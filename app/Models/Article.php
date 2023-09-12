<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'title',
        'description',
        'rubrique_id',
        'slug',
        'image',
        'status',
        'user_id',
        'video_url',
        'audio_url',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function rubrique()
    {
        return $this->belongsTo(Rubrique::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
