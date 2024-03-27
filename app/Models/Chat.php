<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'utilisateur_id', 'message_id', 'status', 'censure'];


    protected $casts = [
        'status' => 'boolean'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
