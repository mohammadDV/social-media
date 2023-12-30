<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubLeague extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "club_league";
    public $timestamps = false;

}
