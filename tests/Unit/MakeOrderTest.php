<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Pigeon;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MakeOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function customer_can_create_order()
    {
        $customer = Customer::factory()->create();
        Pigeon::factory()->create();
        $order = OrderService::makeOrder($customer, 1, now()->addDay(2));

        $this->assertEquals($order->customer_id, $customer->id);
    }

    /** @test */
    function cant_create_order_if_there_is_no_pigeons_that_can_catchup_the_orders_deadline()
    {
        $customer = Customer::factory()->create();
        Pigeon::factory()->create([
            "speed_per_hour" => 5,
        ]);

        $this->expectException(ValidationException::class);
        OrderService::makeOrder($customer, 300, now()->addHour(2));
    }

    /** @test */
    function cant_create_order_if_there_is_no_pigeons_with_maximum_range_greater_than_orders_distance()
    {
        $customer = Customer::factory()->create();
        Pigeon::factory()->create([
            "maximum_range" => 200,
        ]);

        $this->expectException(ValidationException::class);
        OrderService::makeOrder($customer, 300, now()->addDays(5));
    }
}
