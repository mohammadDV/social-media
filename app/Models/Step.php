<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Matches::class, 'step_id', 'id');
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)->withPivot('points', 'games_count')->orderBy('points', 'desc');
    }
}
