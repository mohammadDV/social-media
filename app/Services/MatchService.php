<?php

namespace App\Services;

use App\Models\Club;
use App\Models\ClubLeague;
use App\Models\Matches;
use App\Models\Step;
use App\Models\ClubStep;
use Illuminate\Support\Facades\DB;

class MatchService {

    public function getSteps(int $league_id) : array {

        $steps = cache()->remember("steps.league." . $league_id, now()->addMinutes(config('cache.default_min')),
        function () use ($league_id) {
            return Step::query()
                ->where('league_id', $league_id)
                ->orderBy('priority','ASC')->get();
        });

        $current        = [];
        $alternative    = [];
        $result         = [];
        foreach($steps ?? [] as $key => $item) {

            $result[$key]['id']         = $item->id;
            $result[$key]['title']      = $item->title;
            $result[$key]['priority']   = $item->priority;

            if($item->current === 1){
                $current = $item;
            }

            $alternative = $item;
        }

        return [
            "steps"     => $result,
            "current"   => !empty($current) ?  $current : $alternative,
        ];
    }

    public function getCurrentStep(int $league_id) : object {

        $current = cache()->remember("steps.league.current." . $league_id, now()->addMinutes(config('cache.default_min')),
            function () use ($league_id) {
                return Step::where(['league_id', $league_id],['current',1])->first();
            });

        if(empty($current->id)){
            $current = cache()->remember("steps.league.current.priority." . $league_id, now()->addMinutes(config('cache.default_min')),
                function () use ($league_id) {
                    return Step::where(['league_id', $league_id])->orderBy('priority','ASC')->first();
                });
        }

        return $current;
    }

    public function getMatches(int $step_id) : array {
        $result = [];

        $mateches = cache()->remember("matches.first" . $step_id, now()->addMinutes(config('cache.default_min')),
            function () use ($step_id) {
                return Matches::query()
                    ->where([['step_id',$step_id]])
                    ->take(100)
                    ->orderBy('priority','ASC')
                    ->get();
            });

        $clubs = cache()->remember("matches.clubs" . $step_id, now()->addMinutes(config('cache.default_min')),
            function () use ($mateches) {
                return Club::query()
                    ->whereIn('id',array_merge(array_column($mateches->toArray(),'home_id'),array_column($mateches->toArray(),'away_id')))
                    ->get()
                    ->keyBy('id');
            });

        foreach($mateches ?? [] as $key => $item) {
            $result[$key]['id']         = $item->id;
            $result[$key]['hsc']        = $item->hsc;
            $result[$key]['asc']        = $item->asc;
            $result[$key]['link']       = $item->link;
            $result[$key]['date']       = $item->date;
            $result[$key]['priority']   = $item->priority;
            $result[$key]['status']     = $item->status;
            $result[$key]['status_name'] = $item->statusName();
            $result[$key]['home_id']    = $item->home_id;
            $result[$key]['home']       = $clubs[$item->home_id]->title;
            $result[$key]['home_image'] = $clubs[$item->home_id]->image;
            $result[$key]['away_id']    = $item->away_id;
            $result[$key]['away']       = $clubs[$item->away_id]->title;
            $result[$key]['away_image'] = $clubs[$item->away_id]->image;
        }
        return $result;
    }

    public function getTournamentClubs(int $step_id) : array {
        $result = [];

        $clubStep   = cache()->remember("Tournament.steps." . $step_id, now()->addMinutes(config('cache.default_min')),
            function () use ($step_id) {
                return ClubStep::where('step_id',$step_id)->take(100)->orderBy('points','DESC')->get();
            });

        $clubs = cache()->remember("tournament.club." . $step_id, now()->addMinutes(config('cache.default_min')),
            function () use ($clubStep) {
                Club::whereIn('id',array_merge(array_column($clubStep->toArray(),'club_id')))->get()->keyBy('id');
            });

        foreach($clubStep ?? [] as $key => $item) {
            $result[$key]['id']             = $key+1;
            $result[$key]['club_id']        = $item->club_id;
            $result[$key]['points']         = $item->points;
            $result[$key]['games_count']    = $item->games_count;
            $result[$key]['title']          = $clubs[$item->club_id]->title;
            $result[$key]['image']          = $clubs[$item->club_id]->image;
        }

        return $result;
    }

    public function getClubsPerTournament(int $league_id) : array {
        $result = [];

        $clubLeague = cache()->remember("per.tournament.league." . $league_id, now()->addMinutes(config('cache.default_min')),
            function () use ($league_id) {
                return ClubLeague::where('league_id',$league_id)->take(100)->orderBy('points','DESC')->get();
            });

        $clubs = cache()->remember("per.tournament.club." . $league_id, now()->addMinutes(config('cache.default_min')),
            function () use ($clubLeague) {
                return Club::whereIn('id',array_merge(array_column($clubLeague->toArray(),'club_id')))->get()->keyBy('id');
            });

        foreach($clubLeague ?? [] as $key => $item) {
            $result[$key]['id']             = $key+1;
            $result[$key]['points']         = $item->points;
            $result[$key]['games_count']    = $item->games_count;
            $result[$key]['title']          = $clubs[$item->club_id]->title;
            $result[$key]['image']          = $clubs[$item->club_id]->image;
        }
        return $result;
    }

    public function storeStep($request,$league) {

        return DB::transaction(function () use ($request,$league) {
            $ids        = $request->input('id');
            $titles     = $request->input('title');
            $priorities = $request->input('priority');
            $currents   = $request->input('current');
            $steps      = Step::Where('league_id',$league->id)->get();
            foreach($steps ?? [] as $key => $step){
                $ID = array_search($step->id,$ids);
                if(!$ID){
                    Step::find($step->id)->delete();
                    continue;
                }

                if(!empty($titles[$ID])){
                    Step::find($step->id)->update([
                        "title"     => clear($titles[$ID]),
                        "current"   => clear($currents[$ID]),
                        "priority"  => clear($priorities[$ID]),
                        "user_id"   => auth()->user()->id,
                    ]);
                }

                unset($titles[$ID]);
                unset($currents[$ID]);
                unset($priorities[$ID]);
            }

            foreach($titles ?? [] as $key => $title){
                if(!empty($title)){
                    Step::create([
                        "title"     => clear($title),
                        "current"   => clear($currents[$key]),
                        "priority"  => clear($priorities[$key]),
                        "user_id"   => auth()->user()->id,
                        "league_id" => $league->id,
                    ]);
                }
            }
            return true;
        });
    }

    // public function storeClubs($request,$league) {

    //     return DB::transaction(function () use ($request,$league) {
    //         $club_ids       = $request->input('club_id');
    //         $points         = $request->input('points');
    //         $games_count    = $request->input('games_count');
    //         $clubs          = ClubLeague::Where('league_id',$league->id)->get();
    //         foreach($clubs ?? [] as $key => $club){

    //             $ID = array_search($club->club_id,$club_ids);
    //             if(!$ID){
    //                 ClubLeague::where([["club_id",$club->club_id],["league_id",$league->id]])->delete();
    //                 continue;
    //             }

    //             if(!empty($club_ids[$ID])){
    //                 ClubLeague::where([["club_id",$club_ids[$ID]],["league_id",$league->id]])->update([
    //                     "points"        => clear($points[$ID]),
    //                     "games_count"   => clear($games_count[$ID]),
    //                     // "user_id"       => auth()->user()->id,
    //                 ]);
    //             }

    //             unset($club_ids[$ID]);
    //         }

    //         foreach($club_ids ?? [] as $key => $club_id){
    //             if(!empty($club_id)){
    //                 ClubLeague::create([
    //                     "club_id"       => clear($club_id),
    //                     "points"        => clear($points[$key]),
    //                     "games_count"   => clear($games_count[$key]),
    //                     // "user_id"   => auth()->user()->id,
    //                     "league_id"     => $league->id,
    //                 ]);
    //             }
    //         }

    //         return true;

    //     });


    // }

    public function storeTournamentClubs($request,$step) {

        return DB::transaction(function () use ($request,$step) {
            $club_ids       = $request->input('club_id');
            $points         = $request->input('points');
            $games_count    = $request->input('games_count');
            $clubs          = ClubStep::Where('step_id',$step->id)->get();
            foreach($clubs ?? [] as $key => $club){

                $ID = array_search($club->club_id,$club_ids);
                if(!$ID){
                    ClubStep::where([["club_id",$club->club_id],["step_id",$step->id]])->delete();
                    continue;
                }

                if(!empty($club_ids[$ID])){
                    ClubStep::where([["club_id",$club_ids[$ID]],["step_id",$step->id]])->update([
                        "points"        => clear($points[$ID]),
                        "games_count"   => clear($games_count[$ID]),
                        // "user_id"       => auth()->user()->id,
                    ]);
                }

                unset($club_ids[$ID]);
            }

            foreach($club_ids ?? [] as $key => $club_id){
                if(!empty($club_id)){
                    ClubStep::create([
                        "club_id"       => clear($club_id),
                        "points"        => clear($points[$key]),
                        "games_count"   => clear($games_count[$key]),
                        // "user_id"   => auth()->user()->id,
                        "step_id"     => $step->id,
                    ]);
                }
            }

            return true;

        });


    }

    public function storeMatch($request,$step) {

        return DB::transaction(function () use ($request,$step) {
            $ids        = $request->input('id');
            $home_ids   = $request->input('home_id');
            $away_ids   = $request->input('away_id');
            $hscs       = $request->input('hsc');
            $ascs       = $request->input('asc');
            $links      = $request->input('link');
            // $statuses   = $request->input('status');
            $dates      = $request->input('date');
            $priorities = $request->input('priority');
            $matches    = Matches::Where('step_id',$step->id)->get();
            foreach($matches ?? [] as $key => $match){
                $ID = array_search($match->id,$ids);
                if(!$ID){
                    Matches::find($match->id)->delete();
                    continue;
                }

                if(!empty($home_ids[$ID])){
                    Matches::find($match->id)->update([
                        "home_id"   => clear($home_ids[$ID]),
                        "away_id"   => clear($away_ids[$ID]),
                        "hsc"       => clear($hscs[$ID]),
                        "asc"       => clear($ascs[$ID]),
                        "link"      => clear($links[$ID]),
                        // "status"    => clear($statuses[$ID]),
                        "date"      => clear($dates[$ID]),
                        "priority"  => clear($priorities[$ID]),
                        "user_id"   => auth()->user()->id,
                    ]);
                }

                unset($home_ids[$ID]);
            }

            foreach($home_ids ?? []   as $key => $home_id){
                if(!empty($home_id)){
                    Matches::create([
                        "home_id"   => clear($home_id),
                        "away_id"   => clear($away_ids[$key]),
                        "hsc"       => clear($hscs[$key]),
                        "asc"       => clear($ascs[$key]),
                        "link"      => clear($links[$key]),
                        "status"    => 1,
                        "date"      => clear($dates[$key]),
                        "priority"  => clear($priorities[$key]),
                        "user_id"   => auth()->user()->id,
                        "step_id"   => $step->id,
                    ]);
                }
            }

            return true;

        });


    }

}
