<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\ClubStep;
use App\Models\Step;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClubStep>
 */
class ClubStepFactory extends Factory
{
    protected $model = ClubStep::class;

    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'step_id' => Step::factory(),
            'points' => 0,
            'games_count' => 0,
        ];
    }
}
