<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenstrualCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'period_start_date',
        'period_end_date',
        'cycle_length',
        'period_length',
        'flow_intensity',
        'next_period_prediction',
        'ovulation_prediction',
        'fertile_window_start',
        'fertile_window_end',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'next_period_prediction' => 'date',
        'ovulation_prediction' => 'date',
        'fertile_window_start' => 'date',
        'fertile_window_end' => 'date',
        'is_active' => 'boolean',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function symptoms()
    {
        return $this->hasMany(CycleSymptom::class);
    }

    /**
     * Calculer les prédictions du cycle
     */
    public function calculatePredictions(int $avgCycleLength = 28, int $avgPeriodLength = 5)
    {
        if (! $this->period_start_date) {
            return;
        }

        // Prochaines règles = début + durée cycle moyenne
        $this->next_period_prediction = Carbon::parse($this->period_start_date)->addDays($avgCycleLength);

        // Ovulation = environ 14 jours avant les prochaines règles
        $this->ovulation_prediction = Carbon::parse($this->next_period_prediction)->subDays(14);

        // Fenêtre fertile = 5 jours avant ovulation et jour de l'ovulation
        $this->fertile_window_start = Carbon::parse($this->ovulation_prediction)->subDays(5);
        $this->fertile_window_end = Carbon::parse($this->ovulation_prediction)->addDay();

        // Durée du cycle si période terminée
        if ($this->period_end_date) {
            $this->period_length = Carbon::parse($this->period_start_date)
                ->diffInDays(Carbon::parse($this->period_end_date)) + 1;
        }

        $this->save();
    }

    /**
     * Vérifier si on est dans la période fertile
     */
    public function isInFertileWindow(): bool
    {
        if (! $this->fertile_window_start || ! $this->fertile_window_end) {
            return false;
        }

        $today = Carbon::today();

        return $today->between($this->fertile_window_start, $this->fertile_window_end);
    }

    /**
     * Jours jusqu'aux prochaines règles
     */
    public function daysUntilNextPeriod(): ?int
    {
        if (! $this->next_period_prediction) {
            return null;
        }

        return Carbon::today()->diffInDays($this->next_period_prediction, false);
    }
}
