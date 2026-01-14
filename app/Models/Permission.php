<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'label', 'type'];

    protected $searchableFields = ['*'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
