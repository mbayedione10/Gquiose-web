<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'icon',
        'action',
        'related_type',
        'related_id',
        'category',
        'image',
        'type',
        'target_audience',
        'filters',
        'scheduled_at',
        'sent_at',
        'status',
        'sent_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
    ];

    protected $casts = [
        'filters' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }
}
