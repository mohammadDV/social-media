<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory, Sluggable;

    public function sluggable() : array
    {
        return [
          'slug' => [
              'source' => 'title'
          ]
        ];
    }

    const STATUS_ADVERTISE = 'advertise';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusNameAttribute()
    {
        return $this->status == 1 ? __('site.Active') : __('site.Inactive');
    }

}
