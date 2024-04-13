<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSend extends Model
{
    use HasFactory;

    protected $guarder = [];

    protected $casts = [
        'conditions' => 'json',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
