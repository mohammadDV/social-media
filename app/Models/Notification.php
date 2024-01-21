<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public const STATUS_SIMPLE = 'simple';
    public const STATUS_FORCE = 'force';

    protected $guarded      = [];

    protected $hidden = [
        'model_type',
        'model_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
