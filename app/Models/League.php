<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)->withPivot('points', 'games_count')->orderBy('points', 'desc');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class)->orderBy('priority', 'asc');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function getTypeNameAttribute()
    {
        return $this->type == 1 ? __('site.League') : __('site.Tournament');
    }

    public function getStatusNameAttribute()
    {
        return $this->status == 1 ? __('site.Active') : __('site.Inactive');
    }
}
