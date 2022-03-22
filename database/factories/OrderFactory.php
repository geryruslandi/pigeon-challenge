<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Pigeon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "customer_id" => Customer::factory(),
            "distance" => $this->faker->numberBetween(500, 1000),
            "deadline" => $this->faker->dateTimeBetween('now', '+01 months'),
            "assigned_pigeon_id" => null,
            "finished_time" => null,
            "cost" => $this->faker->numberBetween(500, 1000) * 2,
            "status" => Order::STATUS_PENDING
        ];
    }

    public function onGoing()
    {
        return $this->state(function(array $attribute){
            return [
                "status" => Order::STATUS_ON_GOING,
                "assigned_pigeon_id" => Pigeon::factory()
            ];
        });
    }

    public function finished()
    {
        return $this->state(function(array $attribute){
            return [
                "status" => Order::STATUS_FINISHED,
                "assigned_pigeon_id" => Pigeon::factory(),
                "finished_time" => now()->subHour()
            ];
        });
    }
}
