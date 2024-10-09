<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Matches;
use App\Models\Step;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AddCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add matches';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('level', 3)->firstOrFail();


        die();
        // $leagueId = '3'; // Bondesliga
        // $seasonId = 'eq.45'; // Bondesliga
        // $leagueId = '8'; // Bondesliga
        // $seasonId = 'eq.46408'; // Bondesliga
        // $leagueId = '1'; // Premier league
        // $seasonId = 'eq.44'; // Premier league
        // $leagueId = '6'; // Premier league
        // $seasonId = 'eq.45769'; // Premier league
        // $leagueId = '4'; // Serie A
        // $seasonId = 'eq.38'; // Serie A
        // $leagueId = '9'; // Serie A
        // $seasonId = 'eq.46406'; // Serie A
        // $leagueId = '2'; // Laliga
        // $seasonId = 'eq.179'; // Laliga
        // $leagueId = '7'; // Laliga
        // $seasonId = 'eq.45770'; // Laliga
        // $leagueId = '5'; // Ligue 1
        // $seasonId = 'eq.6919'; // Ligue 1
        $leagueId = '11'; // Ligue 1
        $seasonId = 'eq.46133'; // Ligue 1

        $x = 0;
        while ($x < 10) {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => 'football-devs.p.rapidapi.com',
                'X-RapidAPI-Key' => '1dfc792218msha447271a2eec499p177175jsn9550814613cc',
            ])->get('https://football-devs.p.rapidapi.com/matches', [
                'limit' => 50,
                'offset' => $x * 50,
                'season_id' => $seasonId,
                'lang' => 'en',
            ]);

            $i = 0;
            // Check if the request was successful
            if ($response->successful()) {
                // Process the response
                $data = $response->json();
                foreach ($data as $item) {

                    if (empty($item['round']['round'])) {
                        $item['round']['round'] = 1;
                    }

                    $round = $item['round']['round'];

                    $result = [
                        'status' => 0,
                        'link' => '',
                        'date' => '',
                        'priority' => $round,
                        'user_id' => $user->id,
                    ];

                    $step = Step::updateOrCreate([
                        'title' => 'هفته' . ' ' . $round,
                        "league_id" => $leagueId,
                    ], [
                        "current"   => 0,
                        "priority"  => 1,
                        "status"  => 1,
                        "user_id"   => $user->id,
                    ]);

                    if (empty($step->id)) {

                        dd("step problem " . var_export($item, true));
                        break;
                    } else {
                        $result['step_id'] = $step->id;
                    }


                    $status = trim($item['status']['type']); // finished
                    $homeId = $item['home_team_id'];

                    $awayId = $item['away_team_id'];
                    $teams = Club::whereIn('alias_id', [$awayId, $homeId])->get();

                    if ($teams->count() != 2) {
                        continue;
                        dd("teams problem " . var_export($item, true));
                    }

                    foreach ($teams as $team) {
                        if ($team['alias_id'] == $awayId) {
                            $result['away_id'] = $team['id'];
                        } else {
                            $result['home_id'] = $team['id'];
                        }
                    }

                    if ($status == 'finished') {
                        $result['status'] = 2;
                        $result['hsc'] = $item['home_team_score']['default_time'];
                        $result['asc'] = $item['away_team_score']['default_time'];
                    } else {
                        $result['status'] = 0;
                        $result['hsc'] = '?';
                        $result['asc'] = '?';
                    }

                    $result['date'] = str_replace('T', ' ', explode('+00', $item['start_time'])[0]); // 2023-10-22T13:30:00+00:00

                    $add = Matches::updateOrCreate([
                        "home_id"   => $result['home_id'],
                        "away_id"   => $result['away_id'],
                        "step_id"   => $step->id,
                    ],[
                        "hsc"       => $result['hsc'],
                        "asc"       => $result['asc'],
                        "link"      => $result['link'],
                        "status"    => $result['status'],
                        "date"      => $result['date'],
                        "priority"  => $result['priority'],
                        "user_id"   => $user->id,
                    ]);

                    if ($add) {
                        $i++;
                    }

                }
            } else {
                dd('Error: ' . $response->status());
            }


            $this->info(PHP_EOL.'Done' . $i);
            $x++;
            sleep(5);
        }


        return Command::SUCCESS;
    }
}
