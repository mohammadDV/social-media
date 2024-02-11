<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function subject() {
        return $this->belongsTo(TicketSubject::class, 'subject_id', 'id');
    }
}
