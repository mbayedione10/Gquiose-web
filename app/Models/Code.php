<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'email', 'phone', 'utilisateur_id'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
