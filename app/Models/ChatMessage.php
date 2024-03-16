<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_READ = 'read';

    protected $guarded      = [];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}


