<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'notification_schedule_id',
        'title',
        'message',
        'icon',
        'action',
        'image',
        'type',
        'category',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'failed_at',
        'error_message',
        'platform',
        'fcm_message_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    /**
     * Relation avec la campagne de notification (si applicable)
     */
    public function notificationSchedule()
    {
        return $this->belongsTo(NotificationSchedule::class);
    }

    /**
     * Scopes for filtering
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeOpened($query)
    {
        return $query->whereNotNull('opened_at');
    }

    public function scopeClicked($query)
    {
        return $query->whereNotNull('clicked_at');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Marque la notification comme ouverte
     */
    public function markAsOpened()
    {
        if (!$this->opened_at) {
            $this->update([
                'status' => 'opened',
                'opened_at' => now(),
            ]);
        }
    }

    /**
     * Marque la notification comme cliquée
     */
    public function markAsClicked()
    {
        $this->update([
            'status' => 'clicked',
            'clicked_at' => now(),
        ]);

        // Si elle n'était pas encore marquée comme ouverte, le faire aussi
        if (!$this->opened_at) {
            $this->opened_at = now();
            $this->save();
        }
    }
}
