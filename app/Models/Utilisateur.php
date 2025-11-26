<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utilisateur extends Model
{
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
        'password',
        'provider',
        'provider_id',
        'photo',
        'email_verified_at',
        'fcm_token',
        'platform',
        'ville_id'
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
        'email_verified_at' => 'datetime',
        'dob' => 'date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function getNameAttribute()
    {
        return $this->prenom. " " .$this->nom;
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
}
