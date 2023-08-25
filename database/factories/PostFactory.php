<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'pre_title'     => $this->faker->title(),
            'title'         => $this->faker->title(),
            'slug'          => $this->faker->slug(),
            'summary'       => $this->faker->text(),
            'content'       => $this->faker->text(),
            'image'         => $this->faker->imageUrl(),
            'video'         => $this->faker->imageUrl(),
            'user_id'       => User::factory(),
            'category_id'   => Category::factory(),
            'status'        => 1,
            'type'          => 0,
        ];
    }
}
