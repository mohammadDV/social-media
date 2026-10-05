<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';

    const STATUS_CLOSED = 'closed';

    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the user that owns the Chat
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that target the Chat
     */
    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
