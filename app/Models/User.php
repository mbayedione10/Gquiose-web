<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use App\Models\Traits\FilamentTrait;
use App\Models\Traits\HasPermissions;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, CanResetPasswordContract
{
    use Notifiable;
    use HasFactory;
    use Searchable;
    use FilamentTrait;
    use HasPermissions;
    use CanResetPassword;

    protected $fillable = ['name', 'phone', 'email', 'password', 'role_id'];

    protected $searchableFields = ['*'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->email, config('auth.super_admins'));
    }
}
