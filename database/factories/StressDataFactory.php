<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stress_Data>
 */
class StressDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'id' => $this->faker->unique()->uuid(),
            'datetime' => $this->faker->dateTime(),
            'stress_level' => $this->faker->numberBetween(0, 10),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'device_id' => $this->faker->uuid(),
            'user_id' => User::all()->random()->id,
            'prediction' => $this->faker->numberBetween(0, 1),
            'average_heart_rate' => $this->faker->numberBetween(0, 100),
            'prediction' => $this->faker->words(20, true)

        ];
    }
}
