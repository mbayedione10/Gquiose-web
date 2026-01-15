<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'phone',
        'sexe',
        'status',
        'dob',
        'anneedenaissance',
        'password',
        'provider',
        'provider_id',
        'photo',
        'email_verified_at',
        'phone_verified_at',
        'fcm_token',
        'onesignal_player_id',
        'platform',
        'ville_id',
    ];

    protected $searchableFields = [
        'nom',
        'prenom',
        'email',
        'phone',
    ];

    protected $casts = [
        'status' => 'boolean',
        'email_verified_at' => 'datetime',
        'anneedenaissance' => 'integer',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['name'];

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function getFullNameAttribute()
    {
        return $this->prenom.' '.$this->nom;
    }

    public function getNameAttribute()
    {
        return $this->getFullNameAttribute();
    }

    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }

    public function notificationPreferences()
    {
        return $this->hasOne(UserNotificationPreference::class, 'utilisateur_id');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function menstrualCycles()
    {
        return $this->hasMany(MenstrualCycle::class, 'utilisateur_id');
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class, 'utilisateur_id');
    }

    /**
     * Mutator pour anneedenaissance: remplit automatiquement dob avec la tranche d'âge
     */
    protected function setAnneedenaissanceAttribute($value): void
    {
        $this->attributes['anneedenaissance'] = $value;

        if ($value) {
            $age = now()->year - (int) $value;
            $this->attributes['dob'] = $this->getAgeRange($age);
        }
    }

    /**
     * Détermine la tranche d'âge selon l'âge calculé
     */
    private function getAgeRange(int $age): string
    {
        return match (true) {
            $age < 15 => '-15 ans',
            $age >= 15 && $age <= 17 => '15-17 ans',
            $age >= 18 && $age <= 24 => '18-24 ans',
            $age >= 25 && $age <= 29 => '25-29 ans',
            $age >= 30 && $age <= 35 => '30-35 ans',
            default => '+35 ans',
        };
    }
}
