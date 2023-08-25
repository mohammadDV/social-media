<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "text"              => $this->faker->text(),
            "user_id"           => User::factory(),
            "parent_id"         => 0,
            "status"            => 1,
            "image"             => $this->faker->imageUrl(),
            "commentable_id"    => Post::factory(),
            "commentable_type"  => Post::class,
        ];
    }
}
