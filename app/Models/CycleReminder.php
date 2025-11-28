<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CycleReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'reminder_type',
        'reminder_time',
        'enabled',
        'days_before',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'days_before' => 'array',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
