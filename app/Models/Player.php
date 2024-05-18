<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function sport(){
        return $this->belongsTo(Sport::class);
    }

    public function club(){
        return $this->belongsTo(Club::class);
    }
}