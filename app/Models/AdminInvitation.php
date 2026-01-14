<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'token',
        'role_id',
        'invited_by',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isPending(): bool
    {
        return ! $this->isExpired() && ! $this->isAccepted();
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function getActivationUrl(): string
    {
        return url("/admin/invitation/accept/{$this->token}");
    }
}
