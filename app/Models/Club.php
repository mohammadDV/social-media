<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Club extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class)->withPivot('points', 'games_count')->orderBy('points', 'desc');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_clubs', 'club_id', 'user_id');
    }

    public function getStatusNameAttribute()
    {
        return $this->status == 1 ? __('site.Active') : __('site.Inactive');
    }
}
