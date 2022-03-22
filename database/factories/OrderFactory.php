<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
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
            "customer_id" => Customer::factory()->create()->id,
            "distance" => $this->faker->numberBetween(500, 1000),
            "deadline" => $this->faker->dateTimeBetween('now', '+01 months'),
            "assigned_pigeon_id" => null,
            "finished_time" => null,
            "status" => Order::STATUS_PENDING
        ];
    }
}
