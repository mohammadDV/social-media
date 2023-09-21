<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubStep extends Model
{
    use HasFactory;

    protected $guarded  = [];
    protected $table    = "club_step";
    public $timestamps  = false;
}
