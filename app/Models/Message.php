<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['theme_id', 'utilisateur_id', 'status', 'question'];

    protected $casts = [
        'status' => 'boolean'
    ];


    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
