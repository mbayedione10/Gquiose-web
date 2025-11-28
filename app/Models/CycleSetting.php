
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CycleSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'average_cycle_length',
        'average_period_length',
        'track_temperature',
        'track_symptoms',
        'track_mood',
        'track_sexual_activity',
        'notifications_enabled',
    ];

    protected $casts = [
        'track_temperature' => 'boolean',
        'track_symptoms' => 'boolean',
        'track_mood' => 'boolean',
        'track_sexual_activity' => 'boolean',
        'notifications_enabled' => 'boolean',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
