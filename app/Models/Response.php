<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'question_id',
        'reponse',
        'isValid',
        'utilisateur_id',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'isValid' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
