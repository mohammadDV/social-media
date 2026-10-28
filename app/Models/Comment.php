<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory,SoftDeletes;

    protected $dates = ['deleted_at'];

    //    protected $dateFormat   = 'U';
    protected $guarded = [];

    protected $hidden = [
        // 'commentable_type',
        // 'commentable_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Comment::class, 'model', 'model_type', 'model_id');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parents(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with('user')
            ->with('likes.user')
            ->where('is_report', 0)
            ->where('status', 1);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable', 'likeable_type', 'likeable_id');
    }

    public function getReplyAttribute()
    {
        return false;
    }
}
