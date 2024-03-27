<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    public function sluggable() : array
    {
        return [
          'slug' => [
              'source' => 'title'
          ]
        ];
    }


    protected $dates        = ['deleted_at'];
    protected $guarded      = ['id'];
    protected $casts        = ['image' => 'array'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class,"Commentable",'commentable_type', 'commentable_id')
            ->where('parent_id', 0)
            ->where('is_report', 0);
    }

    public function likes()
    {
        return $this->morphMany(Like::class,"Likeable",'likeable_type', 'likeable_id')->with('user');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function advertise()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function getTypeNameAttribute()
    {
        return __('site.' . Config('custom.POST_TYPE')[$this->type]);
    }

    public function getStatusNameAttribute()
    {
        return $this->status == 1 ? __('site.Active') : __('site.Inactive');
    }

}
