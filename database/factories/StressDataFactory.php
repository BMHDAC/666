<?php

namespace Database\Factories;

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
        ];
    }
}
