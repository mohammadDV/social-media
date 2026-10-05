<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'image' => $this->faker->imageUrl(),
            'user_id' => User::factory(),
            'type' => 'advertise',
            'status' => 1,
            'file' => $this->faker->url(),
        ];
    }
}
