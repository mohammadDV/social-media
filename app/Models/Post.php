<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory,SoftDeletes, Sluggable;

    public function sluggable() : array
    {
        return [
          'slug' => [
              'source' => 'title'
          ]
        ];
    }


    protected $dates        = ['deleted_at'];
//    protected $dateFormat   = 'U';
    protected $guarded      = ['id'];
    protected $casts        = ['image' => 'array'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class,"Commentable",'commentable_type', 'commentable_id')->where('parent_id',0);
    }

    public function likes()
    {
        return $this->morphMany(Comment::class,"Likeable",'likeable_type', 'likeable_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Category()
    {
        return $this->belongsTo(Category::class);
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
