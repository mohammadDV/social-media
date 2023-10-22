<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory,SoftDeletes;
    protected $dates        = ['deleted_at'];
//    protected $dateFormat   = 'U';
    protected $guarded      = [];

    protected $casts = [
        'file' => 'array'
    ];

    protected $visible = [
        'id','text','file', 'user', 'likes'
    ];

    public function comments()
    {
        return $this->morphMany(Comment::class,"commentable",'commentable_type', 'commentable_id')->where('parent_id',0)->with(['likes','parents']);
    }

    public function likes()
    {
        return $this->morphMany(Like::class,"likeable",'likeable_type', 'likeable_id')->with('user');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
