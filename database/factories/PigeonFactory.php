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
            "downtime" => $this->faker->numberBetween(1, 5),
            "order_cycle_count" => 0,
            "previous_finished_order_time" => null
        ];
    }

    public function resting()
    {
        return $this->state(function(array $attribute){
            return [
                "order_cycle_count" => 2,
                "previous_finished_order_time" => now(),
                "downtime" => 1
            ];
        });
    }
}
