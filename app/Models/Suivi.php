<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suivi extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'observation', 'alerte_id'];

    protected $searchableFields = ['*'];

    public function alerte()
    {
        return $this->belongsTo(Alerte::class);
    }
}
