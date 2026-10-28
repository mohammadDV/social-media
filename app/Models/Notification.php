<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    public const STATUS_SIMPLE = 'simple';

    public const STATUS_FORCE = 'force';

    protected $guarded = [];

    protected $hidden = [
        'model_type',
        'model_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(NotificationSend::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
