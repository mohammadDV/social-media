<?php

namespace App\Repositories;

use App\Models\Live;
use App\Repositories\Contracts\ILiveRepository;

class LiveRepository implements ILiveRepository {

    /**
     * Get the live.
     * @return array
     */
    public function index() :array
    {
        // ->addMinutes('1'),
        $livesRow = cache()->remember("live.all", now(),
            function () {
            return Live::query()->orderBy('priority','ASC')->take(50)->get();
        });

        $lives          = [];
        foreach($livesRow ?? [] as $live){
            $lives[slug($live->date)][] = [
                "title"     => $live->title,
                "teams"     => $live->teams,
                "hour"      => $live->hour,
                "date"      => $live->date,
                "link"      => $live->link,
                "info"      => $live->info,
                "priority"  => $live->priority,
            ];
        }
        return $lives;

    }
}
