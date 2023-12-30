<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function leagues()
    {
        return $this->belongsToMany(League::class)->withPivot('points', 'games_count')->orderBy('points', 'desc');
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function sport(){
        return $this->belongsTo(Sport::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'favorite_clubs', 'club_id','user_id');
    }

    public function getStatusNameAttribute()
    {
        return $this->status == 1 ? __('site.Active') : __('site.Inactive');
    }

}
