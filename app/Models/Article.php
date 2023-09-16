<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model
{
    use HasFactory;
    use Searchable;
    use  HasSlug;

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
        'vedette',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
        'vedette' => 'boolean',
    ];

    public function rubrique()
    {
        return $this->belongsTo(Rubrique::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVedette()
    {

    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
