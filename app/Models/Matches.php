<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;

    protected $table = "matches";

    protected $guarded = [];

    public function statusName(){

        switch($this->status){
            case 0:
                $status = __('site.Waiting to start');
                break;
            case 1:
                $status = __('site.On performing');
                break;
            case 2:
                $status = __('site.Finish');
                break;
                default;
                $status = '';
        }
        return $status;
    }

    public function teamHome() {
        return $this->belongsTo(Club::class, 'home_id');
    }

    public function teamAway() {
        return $this->belongsTo(Club::class, 'away_id');
    }


}
