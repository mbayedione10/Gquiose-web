<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'notifications_enabled',
        'cycle_notifications',
        'content_notifications',
        'forum_notifications',
        'health_tips_notifications',
        'admin_notifications',
        'quiet_start',
        'quiet_end',
        'do_not_disturb',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'cycle_notifications' => 'boolean',
        'content_notifications' => 'boolean',
        'forum_notifications' => 'boolean',
        'health_tips_notifications' => 'boolean',
        'admin_notifications' => 'boolean',
        'do_not_disturb' => 'boolean',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
