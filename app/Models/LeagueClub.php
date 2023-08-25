<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeagueClub extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "league_club";
    public $timestamps = false;
}
