<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StepClub extends Model
{
    use HasFactory;

    protected $guarded  = [];
    protected $table    = "step_club";
    public $timestamps  = false;
}
