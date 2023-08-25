<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id"           => User::factory(),
            "likeable_id"    => Post::factory(),
            "likeable_type"  => Post::class,
            "type"              => Arr::random([1,2]),
        ];
    }
}
