<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'     => $this->faker->title(),
            'image'     => $this->faker->imageUrl(),
            'user_id'   => User::factory(),
            'parent_id' => 0,
            'status'    => 1,
        ];
    }
}
