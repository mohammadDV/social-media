<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\ClubLeague;
use App\Models\League;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClubLeague>
 */
class ClubLeagueFactory extends Factory
{
    protected $model = ClubLeague::class;

    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'league_id' => League::factory(),
            'points' => 0,
            'games_count' => 0,
        ];
    }
}
