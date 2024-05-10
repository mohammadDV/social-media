<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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
            'pre_title'     => 'نقل و انتقالات استقلال زیر ذره بین ' . rand(1111, 4444),
            'title'         => 'تیمی که سلاح ندارد اما جنگ را بلد است ' . rand(1111, 4444),
            'summary'       => $this->faker->text(),
            'content'       => $this->faker->paragraph(),
            'image'         => $this->faker->imageUrl(),
            'video'         => $this->faker->imageUrl(),
            'user_id'       => rand(1, 19),
            'special'       => Arr::random([0,1]),
            'status'        => 1,
            'type'          => 0,
        ];
    }
}