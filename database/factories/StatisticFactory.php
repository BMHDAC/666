<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Statistic>
 */
class StatisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            "step_count" => $this->faker->numberBetween(20, 150),
            "stair_step_count" => $this->faker->numberBetween(0, 50),
            "heart_rate" => $this->faker->numberBetween(75, 150),
            "distance" => $this->faker->randomFloat(0, 0, 400),
            "datetime" => $this->faker->unique()->dateTimeBetween(startDate: '-4 week')->format("Y-m-d")
        ];
    }
}
