<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use phpDocumentor\Reflection\Types\Integer;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name'        => $this->faker->firstName(),
            'last_name'         => $this->faker->lastName(),
            'national_code'     => $this->faker->isbn10(),
            // 'role_id'           => Role::factory(),
            'role_id'           => 3,
            'mobile'            => $this->faker->phoneNumber(),
            'point'             => 0,
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => '$2y$10$0b.Jq4fE5DUtJB.WhT9RhORp.Fmev0CcvrSuZX5WrQ6Tk/tYfANZm', // password
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's user type should be user.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function user()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 1,
            ];
        });
    }

    /**
     * Indicate that the model's user type should be admin.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 2,
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     *
     * @return $this
     */
    public function withPersonalTeam()
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(function (array $attributes, User $user) {
                    return ['name' => $user->name.'\'s Team', 'user_id' => $user->id, 'personal_team' => true];
                }),
            'ownedTeams'
        );
    }
}
