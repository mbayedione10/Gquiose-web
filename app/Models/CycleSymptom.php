
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CycleSymptom extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'menstrual_cycle_id',
        'symptom_date',
        'physical_symptoms',
        'pain_level',
        'mood',
        'discharge_type',
        'temperature',
        'sexual_activity',
        'contraception_used',
        'notes',
    ];

    protected $casts = [
        'symptom_date' => 'date',
        'physical_symptoms' => 'array',
        'mood' => 'array',
        'sexual_activity' => 'boolean',
        'contraception_used' => 'boolean',
        'temperature' => 'decimal:2',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function menstrualCycle()
    {
        return $this->belongsTo(MenstrualCycle::class);
    }
}
