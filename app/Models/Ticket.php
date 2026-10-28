<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';

    const STATUS_CLOSED = 'closed';

    protected $guarded = [];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(TicketSubject::class, 'subject_id', 'id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }
}
