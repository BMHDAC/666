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
            "step_count" => $this->faker->numberBetween(0, 1000),
            "stair_step_count" => $this->faker->numberBetween(0, 250),
            "heart_rate" => $this->faker->numberBetween(1, 200),
            "distance" => $this->faker->randomFloat(0, 0, 1000),
            "datetime" => $this->faker->dateTimeThisMonth()
        ];
    }
}
