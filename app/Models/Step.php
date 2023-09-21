<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function league() {
        return $this->belongsTo(League::class);
    }

    public function matches() {
        return $this->hasMany(Matches::class, 'step_id', 'id');
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class);
    }
}
