<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PigeonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name(),
            "speed_per_hour" => $this->faker->numberBetween(45, 90),
            "maximum_range" => $this->faker->numberBetween(500, 1000),
            "cost_per_distance" => 2,
            "downtime" => $this->faker->numberBetween(1, 5)
        ];
    }
}
