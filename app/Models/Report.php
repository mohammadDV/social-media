<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    const STATUS_PENDING = "pending";
    const STATUS_CLOSED = "closed";

    protected $guarded = [];

    protected $hidden = [
        'model_type',
        'model_id',
    ];


    public function model()
    {
        return $this->morphTo();
    }

}
